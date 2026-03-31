<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Services\DonationSettlementService;
use App\Services\DiscordWebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MidtransCallbackController extends Controller
{
    public function __invoke(Request $request, DiscordWebhookService $discord, DonationSettlementService $donationSettlementService)
    {
        $data = $request->all();
        $orderId = $data['order_id'] ?? null;
        $serverKey = (string) config('services.midtrans.server_key');
        $signature = (string) ($data['signature_key'] ?? '');

        if ($serverKey !== '' && $signature !== '') {
            $expectedSignature = hash('sha512', ($orderId ?? '') . ($data['status_code'] ?? '') . ($data['gross_amount'] ?? '') . $serverKey);
            if (! hash_equals($expectedSignature, $signature)) {
                return response()->json(['message' => 'invalid_signature'], 403);
            }
        }

        $donation = Donation::where('midtrans_order_id', $orderId)->first();
        if (! $donation) {
            return response()->json(['message' => 'donation_not_found'], 404);
        }

        $transactionStatus = $data['transaction_status'] ?? 'pending';
        $fraudStatus = $data['fraud_status'] ?? null;

        $paid = in_array($transactionStatus, ['capture', 'settlement'], true)
            && ($fraudStatus === null || $fraudStatus === 'accept');
        $expired = $transactionStatus === 'expire';
        $failed = in_array($transactionStatus, ['deny', 'cancel'], true);

        DB::transaction(function () use (
            $donation,
            $data,
            $transactionStatus,
            $paid,
            $failed,
            $expired,
            $donationSettlementService
        ): void {
            $lockedDonation = Donation::query()
                ->whereKey($donation->id)
                ->lockForUpdate()
                ->first();
            if (! $lockedDonation) {
                return;
            }

            $payload = (array) ($lockedDonation->payment_payload ?? []);
            $payload['midtrans_last_callback'] = $data;
            $payload['midtrans_last_status'] = $transactionStatus;
            $payload['midtrans_last_callback_at'] = now()->toDateTimeString();

            $lockedDonation->forceFill([
                'midtrans_transaction_id' => $data['transaction_id'] ?? $lockedDonation->midtrans_transaction_id,
                'payment_payload' => $payload,
            ])->save();

            if ($paid) {
                $donationSettlementService->approve(
                    $lockedDonation,
                    null,
                    now(),
                    now(),
                    'Auto-approved dari callback Midtrans (' . $transactionStatus . ')'
                );
                return;
            }

            if ($failed || $expired) {
                $donationSettlementService->reject(
                    $lockedDonation,
                    null,
                    'Midtrans status: ' . $transactionStatus,
                    $expired ? 'expired' : 'failed'
                );
                return;
            }
        });

        $discord->send('midtrans_callback', [
            'order_id' => $orderId,
            'status' => $transactionStatus,
            'donation_id' => $donation->id,
        ]);

        return response()->json(['message' => 'ok']);
    }
}
