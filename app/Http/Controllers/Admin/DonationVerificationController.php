<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Services\AuditLogService;
use App\Services\DonationSettlementService;
use App\Services\DiscordWebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DonationVerificationController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->integer('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;

        return view('admin.donation-verification', [
            'perPage' => $perPage,
            'donations' => Donation::latest()->paginate($perPage)->withQueryString(),
        ]);
    }

    public function verify(
        Request $request,
        Donation $donation,
        AuditLogService $auditLogService,
        DiscordWebhookService $discord,
        DonationSettlementService $donationSettlementService
    )
    {
        $data = $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        if ($data['action'] === 'approve') {
            $donationSettlementService->approve(
                $donation,
                (int) $request->user()->id,
                now(),
                now(),
                $data['reason'] ?? 'Verifikasi manual admin'
            );
        } else {
            $donationSettlementService->reject(
                $donation,
                (int) $request->user()->id,
                $data['reason'] ?? 'Tidak memenuhi verifikasi'
            );
        }

        $auditLogService->record($request, 'verify_donation', 'Verifikasi donasi #' . $donation->id . ' (' . $data['action'] . ')', 'donations', $donation->id);
        $discord->send('donation_verification', [
            'donation_id' => $donation->id,
            'action' => $data['action'],
            'by' => $request->user()->email,
        ]);

        return back()->with('status', 'Verifikasi donasi berhasil diproses.');
    }

    public function downloadReceipt(Donation $donation)
    {
        abort_unless($donation->receipt_pdf_path, 404);

        return Storage::disk('local')->download($donation->receipt_pdf_path, ($donation->receipt_number ?: 'kwitansi') . '.pdf');
    }

    public function downloadProof(Donation $donation)
    {
        abort_unless($donation->bank_transfer_proof_path, 404);

        return Storage::disk('local')->download($donation->bank_transfer_proof_path);
    }
}
