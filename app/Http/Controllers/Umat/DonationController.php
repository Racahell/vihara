<?php

namespace App\Http\Controllers\Umat;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Donation;
use App\Models\DonationCategory;
use App\Services\AuditLogService;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DonationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isOwnerReadOnly = $user?->hasRole('owner') ?? false;

        return view('umat.donations', [
            'categories' => DonationCategory::where('is_active', true)->get(),
            'activities' => Activity::where('is_active', true)->orderBy('start_at')->get(),
            'myDonations' => Donation::where('user_id', auth()->id())->latest()->take(10)->get(),
            'monitorDonations' => $isOwnerReadOnly ? Donation::latest('donated_at')->take(20)->get() : collect(),
            'canCreateDonation' => $user?->hasAnyRole(['umat', 'superadmin', 'admin']) ?? false,
            'isOwnerReadOnly' => $isOwnerReadOnly,
            'donationBank' => $this->donationBankConfig(),
        ]);
    }

    public function store(Request $request, QrCodeService $qrCodeService, AuditLogService $auditLogService)
    {
        $user = $request->user();
        $data = $this->validateDonationRequest($request, $user?->name, $user?->email, $user?->phone);
        $donation = $this->createManualDonation($request, $data, $user?->id);

        $auditLogService->record($request, 'create_donation', 'Donasi dibuat #' . $donation->id, 'donations', $donation->id);

        return $this->renderInstructionView($donation, $qrCodeService, false);
    }

    public function storeGuest(Request $request, QrCodeService $qrCodeService)
    {
        $data = $this->validateDonationRequest($request);
        $donation = $this->createManualDonation($request, $data, null);

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

    private function createManualDonation(Request $request, array $data, ?int $userId): Donation
    {
        $bank = $this->donationBankConfig();

        return Donation::create([
            'user_id' => $userId,
            'donation_category_id' => $data['donation_category_id'] ?? null,
            'activity_id' => $data['activity_id'] ?? null,
            'donor_name' => $data['donor_name'],
            'donor_email' => $data['donor_email'] ?? null,
            'donor_phone' => $data['donor_phone'] ?? null,
            'amount' => $data['amount'],
            'note' => $data['note'] ?? null,
            'payment_method' => 'transfer',
            'payment_status' => 'pending',
            'verification_status' => 'pending',
            'payment_payload' => [
                'channel' => $data['payment_channel'],
                'donor_type' => $data['donor_type'],
                'bank_name' => $bank['bank_name'],
                'account_number' => $bank['account_number'],
                'account_holder' => $bank['account_holder'],
                'verification_key' => (string) Str::uuid(),
                'qris_payload' => sprintf(
                    'VIHARA-DONASI|ID:%s|NOMINAL:%s|DONATUR:%s',
                    uniqid('DON', false),
                    $data['amount'],
                    $data['donor_name']
                ),
            ],
            'bank_transfer_proof_path' => null,
            'donated_at' => now(),
        ]);
    }

    private function renderInstructionView(Donation $donation, QrCodeService $qrCodeService, bool $isGuest)
    {
        $channel = (string) data_get($donation->payment_payload, 'channel', 'bank_transfer');
        $qrPayload = $channel === 'qris'
            ? (string) data_get($donation->payment_payload, 'qris_payload', $donation->id)
            : 'TRANSFER|' . $donation->id . '|' . $donation->amount;
        $qrDataUri = null;

        try {
            $qrDataUri = $qrCodeService->dataUri($qrPayload, 300, 8);
        } catch (\Throwable) {
            $qrDataUri = null;
        }

        return view($isGuest ? 'donations.transfer-instruction-guest' : 'donations.transfer-instruction', [
            'donation' => $donation,
            'isGuest' => $isGuest,
            'bank' => $this->donationBankConfig(),
            'transferChannel' => $channel,
            'qrDataUri' => $qrDataUri,
            'qrPayload' => $qrPayload,
            'verificationKey' => (string) data_get($donation->payment_payload, 'verification_key', ''),
        ]);
    }

    private function donationBankConfig(): array
    {
        return [
            'bank_name' => config('donation.bank_name', 'BCA'),
            'account_number' => config('donation.account_number', '1234567890'),
            'account_holder' => config('donation.account_holder', 'Vihara Dharma Sejahtera'),
        ];
    }
}
