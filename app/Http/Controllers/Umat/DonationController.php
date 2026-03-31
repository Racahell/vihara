<?php

namespace App\Http\Controllers\Umat;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Donation;
use App\Models\DonationCategory;
use App\Models\WebsiteSetting;
use App\Services\AuditLogService;
use App\Services\MidtransService;
use App\Services\QrCodeService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DonationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isOwnerReadOnly = $user?->hasRole('owner') ?? false;
        $perPage = 5;
        $myDonations = Donation::where('user_id', auth()->id())
            ->latest()
            ->paginate($perPage, ['*'], 'my_page')
            ->withQueryString();
        $myDonations->each(fn (Donation $donation) => $this->expireTimedOutQrisTransaction($donation));

        $monitorDonations = $isOwnerReadOnly
            ? Donation::latest('donated_at')
                ->paginate($perPage, ['*'], 'monitor_page')
                ->withQueryString()
            : collect();
        $monitorDonations->each(fn (Donation $donation) => $this->expireTimedOutQrisTransaction($donation));

        return view('umat.donations', [
            'categories' => DonationCategory::where('is_active', true)->get(),
            'activities' => Activity::where('is_active', true)->orderBy('start_at')->get(),
            'myDonations' => $myDonations,
            'monitorDonations' => $monitorDonations,
            'canCreateDonation' => $user?->hasAnyRole(['umat', 'superadmin', 'admin']) ?? false,
            'isOwnerReadOnly' => $isOwnerReadOnly,
            'donationBank' => $this->donationBankConfig(),
        ]);
    }

    public function store(Request $request, QrCodeService $qrCodeService, AuditLogService $auditLogService, MidtransService $midtransService)
    {
        $user = $request->user();
        $data = $this->validateDonationRequest($request, $user?->name, $user?->email, $user?->phone);
        $donation = $this->createDonation($request, $data, $user?->id, $midtransService);

        $auditLogService->record($request, 'create_donation', 'Donasi dibuat #' . $donation->id, 'donations', $donation->id);

        return $this->renderInstructionView($donation, $qrCodeService, false);
    }

    public function storeGuest(Request $request, QrCodeService $qrCodeService, MidtransService $midtransService)
    {
        $data = $this->validateDonationRequest($request);
        $donation = $this->createDonation($request, $data, null, $midtransService);

        return $this->renderInstructionView($donation, $qrCodeService, true);
    }

    public function uploadProof(Request $request, Donation $donation, AuditLogService $auditLogService)
    {
        $user = $request->user();
        $canUpload = ((int) $donation->user_id === (int) $user->id) || $user->hasAnyRole(['superadmin', 'admin']);
        abort_unless($canUpload, 403);

        $data = $request->validate([
            'transfer_proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:3072'],
        ]);

        if ($donation->bank_transfer_proof_path) {
            Storage::disk('local')->delete($donation->bank_transfer_proof_path);
        }

        $proofPath = $data['transfer_proof']->store('donation-proofs', 'local');
        $donation->update([
            'bank_transfer_proof_path' => $proofPath,
            'paid_at' => now(),
            'payment_status' => 'paid',
        ]);

        $auditLogService->record($request, 'upload_donation_proof', 'Upload bukti transfer donasi #' . $donation->id, 'donations', $donation->id);

        return back()->with('status', 'Bukti transfer berhasil diunggah. Menunggu verifikasi admin.');
    }

    public function uploadGuestProof(Request $request, Donation $donation)
    {
        $data = $request->validate([
            'verification_key' => ['required', 'string'],
            'transfer_proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:3072'],
        ]);

        $expectedKey = (string) data_get($donation->payment_payload, 'verification_key');
        abort_unless($expectedKey !== '' && hash_equals($expectedKey, $data['verification_key']), 403);

        if ($donation->bank_transfer_proof_path) {
            Storage::disk('local')->delete($donation->bank_transfer_proof_path);
        }

        $proofPath = $data['transfer_proof']->store('donation-proofs', 'local');
        $donation->update([
            'bank_transfer_proof_path' => $proofPath,
            'paid_at' => now(),
            'payment_status' => 'paid',
        ]);

        return back()->with('status', 'Bukti transfer guest berhasil diunggah. Menunggu verifikasi admin.');
    }

    public function pay(Request $request, Donation $donation, QrCodeService $qrCodeService)
    {
        $user = $request->user();
        $canOpen = ((int) $donation->user_id === (int) $user->id) || $user->hasAnyRole(['superadmin', 'admin']);
        abort_unless($canOpen, 403);

        $this->expireTimedOutQrisTransaction($donation);
        $donation->refresh();

        $isPendingPayment = strtolower((string) $donation->payment_status) === 'pending';
        $isPendingVerification = strtolower((string) $donation->verification_status) === 'pending';
        if (! ($isPendingPayment && $isPendingVerification)) {
            return back()->withErrors(['donation' => 'Transaksi tidak dapat dilanjutkan. Status saat ini: ' . strtoupper((string) $donation->payment_status) . '.']);
        }

        return $this->renderInstructionView($donation, $qrCodeService, false);
    }

    private function validateDonationRequest(Request $request, ?string $defaultName = null, ?string $defaultEmail = null, ?string $defaultPhone = null): array
    {
        $validated = $request->validate([
            'donation_category_id' => ['nullable', 'exists:donation_categories,id'],
            'activity_id' => ['nullable', 'exists:activities,id'],
            'amount' => ['required', 'integer', 'min:1000'],
            'note' => ['nullable', 'string', 'max:255'],
            'payment_channel' => ['required', 'in:bank_transfer,qris'],
            'donor_type' => ['required', 'in:named,anonymous'],
            'donor_name' => ['nullable', 'string', 'max:255'],
            'donor_email' => ['nullable', 'email', 'max:255'],
            'donor_phone' => ['nullable', 'string', 'max:32'],
        ]);

        if ($validated['donor_type'] === 'named' && empty($validated['donor_name']) && empty($defaultName)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'donor_name' => 'Nama donatur wajib diisi jika donasi tidak anonim.',
            ]);
        }

        $validated['donor_name'] = $validated['donor_type'] === 'anonymous'
            ? 'Anonim'
            : ($validated['donor_name'] ?: ($defaultName ?: 'Donatur'));
        $validated['donor_email'] = $validated['donor_email'] ?: $defaultEmail;
        $validated['donor_phone'] = $validated['donor_phone'] ?: $defaultPhone;

        return $validated;
    }

    private function createDonation(Request $request, array $data, ?int $userId, MidtransService $midtransService): Donation
    {
        $bank = $this->donationBankConfig();
        $channel = (string) $data['payment_channel'];
        $midtrans = null;
        if ($channel === 'qris') {
            $midtrans = $midtransService->createQrisTransaction([
                'amount' => $data['amount'],
                'donor_name' => $data['donor_name'],
                'donor_email' => $data['donor_email'] ?? null,
                'donor_phone' => $data['donor_phone'] ?? null,
            ]);
        }

        return Donation::create([
            'user_id' => $userId,
            'donation_category_id' => $data['donation_category_id'] ?? null,
            'activity_id' => $data['activity_id'] ?? null,
            'donor_name' => $data['donor_name'],
            'donor_email' => $data['donor_email'] ?? null,
            'donor_phone' => $data['donor_phone'] ?? null,
            'amount' => $data['amount'],
            'note' => $data['note'] ?? null,
            'payment_method' => $channel === 'qris' ? 'midtrans' : 'transfer',
            'payment_status' => ($midtrans && $midtrans['status'] === 'failed') ? 'failed' : 'pending',
            'verification_status' => 'pending',
            'midtrans_order_id' => $midtrans['order_id'] ?? null,
            'payment_payload' => [
                'channel' => $channel,
                'donor_type' => $data['donor_type'],
                'bank_name' => $bank['bank_name'],
                'account_number' => $bank['account_number'],
                'account_holder' => $bank['account_holder'],
                'verification_key' => (string) Str::uuid(),
                'qris_payload' => $midtrans['qr_string'] ?? null,
                'qris_image' => $midtrans['qr_image'] ?? null,
                'qris_expired_at' => $midtrans['expired_at'] ?? null,
                'gateway_status' => $midtrans['status'] ?? null,
                'gateway_payload' => $midtrans['payload'] ?? null,
            ],
            'bank_transfer_proof_path' => null,
            'donated_at' => now(),
        ]);
    }

    private function renderInstructionView(Donation $donation, QrCodeService $qrCodeService, bool $isGuest)
    {
        $channel = (string) data_get($donation->payment_payload, 'channel', 'bank_transfer');
        $qrPayload = $channel === 'qris'
            ? (string) data_get($donation->payment_payload, 'qris_payload', '')
            : 'TRANSFER|' . $donation->id . '|' . $donation->amount;
        $qrDataUri = null;
        $qrisImage = null;
        $qrisExpiredAt = (string) data_get($donation->payment_payload, 'qris_expired_at', '');

        if ($channel === 'qris') {
            $qrisImage = (string) data_get($donation->payment_payload, 'qris_image', '');
            if ($qrisImage === '' && $qrPayload === '') {
                // Fallback QR saat gateway tidak mengembalikan payload/image (misalnya mode simulasi).
                $qrPayload = 'SIMULATED-QRIS|' . $donation->id . '|' . $donation->amount . '|' . ($donation->donor_name ?? 'DONOR');
            }

            if ($qrisImage === '' && $qrPayload !== '') {
                try {
                    $qrDataUri = $qrCodeService->dataUri($qrPayload, 300, 8);
                } catch (\Throwable) {
                    $qrDataUri = null;
                }
            }
        } else {
            try {
                $qrDataUri = $qrCodeService->dataUri($qrPayload, 300, 8);
            } catch (\Throwable) {
                $qrDataUri = null;
            }
        }

        return view($isGuest ? 'donations.transfer-instruction-guest' : 'donations.transfer-instruction', [
            'donation' => $donation,
            'isGuest' => $isGuest,
            'bank' => $this->donationBankConfig(),
            'transferChannel' => $channel,
            'qrDataUri' => $qrDataUri,
            'qrPayload' => $qrPayload,
            'qrisImage' => $qrisImage,
            'qrisExpiredAt' => $qrisExpiredAt,
            'verificationKey' => (string) data_get($donation->payment_payload, 'verification_key', ''),
        ]);
    }

    private function donationBankConfig(): array
    {
        try {
            $settings = WebsiteSetting::query()
                ->whereIn('key', ['donation_bank_name', 'donation_account_number', 'donation_account_holder'])
                ->pluck('value', 'key')
                ->toArray();
        } catch (QueryException) {
            $settings = [];
        }

        return [
            'bank_name' => (string) ($settings['donation_bank_name'] ?? config('donation.bank_name', 'BCA')),
            'account_number' => (string) ($settings['donation_account_number'] ?? config('donation.account_number', '1234567890')),
            'account_holder' => (string) ($settings['donation_account_holder'] ?? config('donation.account_holder', 'Vihara Dharma Sejahtera')),
        ];
    }

    private function expireTimedOutQrisTransaction(Donation $donation): void
    {
        $channel = strtolower((string) data_get($donation->payment_payload, 'channel', ''));
        $paymentStatus = strtolower((string) $donation->payment_status);
        if ($channel !== 'qris' || $paymentStatus !== 'pending') {
            return;
        }

        $baseTime = $donation->donated_at ?? $donation->created_at;
        if (! $baseTime) {
            return;
        }

        if (now()->greaterThan($baseTime->copy()->addMinutes(15))) {
            $donation->update([
                'payment_status' => 'failed',
            ]);
        }
    }
}
