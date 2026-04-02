<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\DonationVerificationLog;
use App\Models\User;
use App\Models\WebsiteSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class DonationSettlementService
{
    public function approve(
        Donation $donation,
        ?int $actedBy = null,
        ?\DateTimeInterface $paidAt = null,
        ?\DateTimeInterface $verifiedAt = null,
        ?string $reason = null
    ): Donation {
        $donation->refresh();

        if (
            strtolower((string) $donation->payment_status) === 'paid'
            && strtolower((string) $donation->verification_status) === 'approved'
            && $donation->receipt_number
            && $donation->receipt_pdf_path
            && Storage::disk('local')->exists((string) $donation->receipt_pdf_path)
        ) {
            return $donation;
        }

        $paidAtValue = $paidAt ?: $donation->paid_at ?: now();
        $verifiedAtValue = $verifiedAt ?: $donation->verified_at ?: now();
        $receiptNumber = $donation->receipt_number ?: $this->generateReceiptNumber($donation);
        $filePath = $donation->receipt_pdf_path ?: ('receipts/' . $receiptNumber . '.pdf');

        if (! $donation->receipt_pdf_path || ! Storage::disk('local')->exists($filePath)) {
            $this->generateReceiptPdf($donation, $receiptNumber, $verifiedAtValue, $actedBy, $filePath);
        }

        $donation->forceFill([
            'payment_status' => 'paid',
            'verification_status' => 'approved',
            'verified_by' => $actedBy,
            'paid_at' => $paidAtValue,
            'verified_at' => $verifiedAtValue,
            'receipt_number' => $receiptNumber,
            'receipt_pdf_path' => $filePath,
            'rejection_reason' => null,
        ])->save();

        DonationVerificationLog::create([
            'donation_id' => $donation->id,
            'acted_by' => $actedBy,
            'action' => 'approve',
            'reason' => $reason,
        ]);

        return $donation;
    }

    public function reject(
        Donation $donation,
        ?int $actedBy = null,
        ?string $reason = null,
        ?string $paymentStatus = null
    ): Donation {
        $donation->refresh();
        $currentVerification = strtolower((string) $donation->verification_status);
        if ($currentVerification === 'approved') {
            return $donation;
        }

        $nextPaymentStatus = $paymentStatus ?: (strtolower((string) $donation->payment_status) === 'expired' ? 'expired' : 'failed');
        $nextPaymentStatus = in_array($nextPaymentStatus, ['failed', 'expired'], true) ? $nextPaymentStatus : 'failed';

        $donation->forceFill([
            'payment_status' => $nextPaymentStatus,
            'verification_status' => 'rejected',
            'verified_by' => $actedBy,
            'verified_at' => $donation->verified_at ?: now(),
            'rejection_reason' => $reason ?: 'Pembayaran ditolak',
        ])->save();

        DonationVerificationLog::create([
            'donation_id' => $donation->id,
            'acted_by' => $actedBy,
            'action' => 'reject',
            'reason' => $reason,
        ]);

        return $donation;
    }

    private function generateReceiptNumber(Donation $donation): string
    {
        return 'KW-' . now()->format('Ymd') . '-' . str_pad((string) $donation->id, 5, '0', STR_PAD_LEFT);
    }

    private function generateReceiptPdf(
        Donation $donation,
        string $receiptNumber,
        \DateTimeInterface $verifiedAt,
        ?int $actedBy,
        string $filePath
    ): void {
        $approver = $actedBy ? User::query()->find($actedBy) : null;
        if (! $approver) {
            $approver = (object) ['name' => 'Sistem Otomatis'];
        }
        $receiverName = (string) (WebsiteSetting::query()->where('key', 'manager_name')->value('value') ?? '');
        if ($receiverName === '') {
            $receiverName = (string) ($approver->name ?? 'Sistem');
        }

        $organizationName = (string) (
            WebsiteSetting::query()->where('key', 'company_name')->value('value')
            ?: WebsiteSetting::query()->where('key', 'website_name')->value('value')
            ?: config('app.name')
        );

        $pdf = Pdf::loadView('reports.pdf.receipt-official', [
            'donation' => $donation,
            'receiptNumber' => $receiptNumber,
            'verifiedAt' => $verifiedAt,
            'approver' => $approver,
            'receiverName' => $receiverName,
            'organizationName' => $organizationName,
        ])->setPaper('a5', 'portrait');

        Storage::disk('local')->put($filePath, $pdf->output());
    }
}
