<?php

namespace App\Http\Controllers\Umat;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Donation;
use App\Models\DonationCategory;
use App\Models\WebsiteSetting;
use App\Services\AuditLogService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DonationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isOwnerReadOnly = $user?->hasRole('owner') ?? false;
        $submissionToken = (string) Str::uuid();
        $request->session()->put('user_donation_submission_token', $submissionToken);
        $perPage = 5;
        $myDonations = Donation::where('user_id', auth()->id())
            ->latest()
            ->paginate($perPage, ['*'], 'my_page')
            ->withQueryString();

        $monitorDonations = $isOwnerReadOnly
            ? Donation::latest('donated_at')
                ->paginate($perPage, ['*'], 'monitor_page')
                ->withQueryString()
            : collect();

        return view('umat.donations', [
            'categories' => DonationCategory::where('is_active', true)->get(),
            'activities' => Activity::where('is_active', true)->orderBy('start_at')->get(),
            'myDonations' => $myDonations,
            'monitorDonations' => $monitorDonations,
            'canCreateDonation' => $user?->hasAnyRole(['umat', 'superadmin', 'admin']) ?? false,
            'isOwnerReadOnly' => $isOwnerReadOnly,
            'donationBank' => $this->donationBankConfig(),
            'donationSubmissionToken' => $submissionToken,
        ]);
    }

    public function store(Request $request, AuditLogService $auditLogService)
    {
        $this->ensureSingleSubmission($request, 'user_donation_submission_token');

        $user = $request->user();
        $data = $this->validateDonationRequest($request, $user?->name, $user?->email, $user?->phone);
        $donation = $this->createDonation($data, $user?->id);

        $auditLogService->record($request, 'create_donation', 'Donasi dibuat #' . $donation->id, 'donations', $donation->id);

        return $this->renderInstructionView($donation, false);
    }

    public function storeGuest(Request $request)
    {
        $this->ensureSingleSubmission($request, 'guest_donation_submission_token');

        $data = $this->validateDonationRequest($request);
        $donation = $this->createDonation($data, null);

        return $this->renderInstructionView($donation, true);
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

    public function pay(Request $request, Donation $donation)
    {
        $user = $request->user();
        $canOpen = ((int) $donation->user_id === (int) $user->id) || $user->hasAnyRole(['superadmin', 'admin']);
        abort_unless($canOpen, 403);

        $isPendingPayment = strtolower((string) $donation->payment_status) === 'pending';
        $isPendingVerification = strtolower((string) $donation->verification_status) === 'pending';
        if (! ($isPendingPayment && $isPendingVerification)) {
            return back()->withErrors(['donation' => 'Transaksi tidak dapat dilanjutkan. Status saat ini: ' . strtoupper((string) $donation->payment_status) . '.']);
        }

        return $this->renderInstructionView($donation, false);
    }

    private function validateDonationRequest(Request $request, ?string $defaultName = null, ?string $defaultEmail = null, ?string $defaultPhone = null): array
    {
        $validated = $request->validate([
            'donation_category_id' => ['nullable', 'exists:donation_categories,id'],
            'activity_id' => ['nullable', 'exists:activities,id'],
            'amount' => ['required', 'integer', 'min:1000'],
            'note' => ['nullable', 'string', 'max:255'],
            'donor_type' => ['required', 'in:named,anonymous'],
            'donor_name' => ['nullable', 'string', 'max:255'],
            'donor_email' => ['nullable', 'email', 'max:255'],
            'donor_phone' => ['nullable', 'string', 'max:32'],
        ]);
        $validated['payment_channel'] = 'bank_transfer';

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

    private function createDonation(array $data, ?int $userId): Donation
    {
        $bank = $this->donationBankConfig();
        $channel = (string) $data['payment_channel'];

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
            'midtrans_order_id' => null,
            'payment_payload' => [
                'channel' => $channel,
                'donor_type' => $data['donor_type'],
                'bank_name' => $bank['bank_name'],
                'account_number' => $bank['account_number'],
                'account_holder' => $bank['account_holder'],
                'verification_key' => (string) Str::uuid(),
            ],
            'bank_transfer_proof_path' => null,
            'donated_at' => now(),
        ]);
    }

    private function renderInstructionView(Donation $donation, bool $isGuest)
    {
        $channel = (string) data_get($donation->payment_payload, 'channel', 'bank_transfer');

        return view($isGuest ? 'donations.transfer-instruction-guest' : 'donations.transfer-instruction', [
            'donation' => $donation,
            'isGuest' => $isGuest,
            'bank' => $this->donationBankConfig(),
            'transferChannel' => $channel,
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

    private function ensureSingleSubmission(Request $request, string $sessionKey): void
    {
        $token = (string) $request->input('submission_token', '');
        $sessionToken = (string) $request->session()->get($sessionKey, '');

        if ($token === '' || $sessionToken === '' || ! hash_equals($sessionToken, $token)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'donation' => 'Permintaan donasi terdeteksi duplikat. Silakan muat ulang halaman donasi lalu kirim sekali lagi.',
            ]);
        }

        $request->session()->forget($sessionKey);
    }

}
