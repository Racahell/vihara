<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\DonationVerificationLog;
use App\Services\AuditLogService;
use App\Services\DiscordWebhookService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DonationVerificationController extends Controller
{
    public function index()
    {
        return view('admin.donation-verification', [
            'donations' => Donation::latest()->paginate(20),
        ]);
    }

    public function verify(Request $request, Donation $donation, AuditLogService $auditLogService, DiscordWebhookService $discord)
    {
        $data = $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        if ($data['action'] === 'approve') {
            $receiptNumber = 'KW-' . now()->format('Ymd') . '-' . str_pad((string) $donation->id, 5, '0', STR_PAD_LEFT);
            $filePath = 'receipts/' . $receiptNumber . '.pdf';

            $pdf = Pdf::loadView('reports.pdf.receipt-official', [
                'donation' => $donation,
                'receiptNumber' => $receiptNumber,
                'verifiedAt' => now(),
                'approver' => $request->user(),
            ])->setPaper('a5', 'portrait');

            Storage::disk('local')->put($filePath, $pdf->output());

            $donation->update([
                'payment_status' => 'paid',
                'verification_status' => 'approved',
                'verified_by' => $request->user()->id,
                'verified_at' => now(),
                'receipt_number' => $receiptNumber,
                'receipt_pdf_path' => $filePath,
                'rejection_reason' => null,
            ]);
        } else {
            $donation->update([
                'verification_status' => 'rejected',
                'verified_by' => $request->user()->id,
                'verified_at' => now(),
                'rejection_reason' => $data['reason'] ?? 'Tidak memenuhi verifikasi',
            ]);
        }

        DonationVerificationLog::create([
            'donation_id' => $donation->id,
            'acted_by' => $request->user()->id,
            'action' => $data['action'],
            'reason' => $data['reason'] ?? null,
        ]);

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
