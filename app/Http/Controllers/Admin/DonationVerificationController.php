<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Services\AuditLogService;
use App\Services\DonationSettlementService;
use App\Services\DiscordWebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Storage;

class DonationVerificationController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->integer('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;

        return view('admin.donation-verification', [
            'perPage' => $perPage,
            'donations' => Donation::latest()
                ->paginate($perPage)
                ->withQueryString()
                ->through(function (Donation $donation) {
                    $proofPath = (string) ($donation->bank_transfer_proof_path ?? '');
                    $extension = strtolower(pathinfo($proofPath, PATHINFO_EXTENSION));
                    $isProofImage = in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'svg'], true);

                    $donation->setAttribute('proof_is_image', $isProofImage);
                    $donation->setAttribute('proof_preview_url', $isProofImage ? route('admin.donation-proof.preview', $donation) : null);

                    return $donation;
                }),
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

    public function downloadReceipt(Donation $donation, DonationSettlementService $donationSettlementService)
    {
        $receiptPath = (string) ($donation->receipt_pdf_path ?? '');

        if ($receiptPath === '' || ! Storage::disk('local')->exists($receiptPath)) {
            $isApprovedPaid = strtolower((string) $donation->payment_status) === 'paid'
                && strtolower((string) $donation->verification_status) === 'approved';

            if ($isApprovedPaid) {
                $donation = $donationSettlementService->approve(
                    $donation,
                    auth()->id(),
                    $donation->paid_at ?: now(),
                    $donation->verified_at ?: now(),
                    'Regenerate missing receipt file'
                );
                $receiptPath = (string) ($donation->receipt_pdf_path ?? '');
            }
        }

        abort_unless($receiptPath !== '' && Storage::disk('local')->exists($receiptPath), 404);

        return Storage::disk('local')->download($receiptPath, ($donation->receipt_number ?: 'kwitansi') . '.pdf');
    }

    public function downloadProof(Donation $donation)
    {
        abort_unless($donation->bank_transfer_proof_path, 404);
        abort_unless(Storage::disk('local')->exists((string) $donation->bank_transfer_proof_path), 404);

        return Storage::disk('local')->download($donation->bank_transfer_proof_path);
    }

    public function previewProof(Donation $donation)
    {
        abort_unless($donation->bank_transfer_proof_path, 404);

        $path = (string) $donation->bank_transfer_proof_path;
        $response = $this->streamProofFromDisk('local', $path);
        if ($response instanceof BinaryFileResponse) {
            return $response;
        }

        $fallbackResponse = $this->streamProofFromDisk('public', $path);
        abort_unless($fallbackResponse instanceof BinaryFileResponse, 404);

        return $fallbackResponse;
    }

    private function streamProofFromDisk(string $disk, string $path): ?BinaryFileResponse
    {
        if (!Storage::disk($disk)->exists($path)) {
            return null;
        }

        $mime = (string) Storage::disk($disk)->mimeType($path);
        if (!Str::startsWith($mime, 'image/')) {
            return null;
        }

        return response()->file(Storage::disk($disk)->path($path), [
            'Content-Type' => $mime,
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }
}
