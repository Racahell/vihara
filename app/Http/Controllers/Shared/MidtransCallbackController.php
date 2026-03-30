<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Services\DiscordWebhookService;
use Illuminate\Http\Request;

class MidtransCallbackController extends Controller
{
    public function __invoke(Request $request, DiscordWebhookService $discord)
    {
        $data = $request->all();
        $orderId = $data['order_id'] ?? null;

        $donation = Donation::where('midtrans_order_id', $orderId)->first();
        if (! $donation) {
            return response()->json(['message' => 'donation_not_found'], 404);
        }

        $transactionStatus = $data['transaction_status'] ?? 'pending';
        $fraudStatus = $data['fraud_status'] ?? null;

        $paid = in_array($transactionStatus, ['capture', 'settlement'], true) && ($fraudStatus === null || $fraudStatus === 'accept');

        $donation->update([
            'payment_status' => $paid ? 'paid' : (in_array($transactionStatus, ['deny', 'cancel', 'expire'], true) ? 'failed' : 'pending'),
            'midtrans_transaction_id' => $data['transaction_id'] ?? null,
            'payment_payload' => $data,
            'paid_at' => $paid ? now() : null,
        ]);

        $discord->send('midtrans_callback', [
            'order_id' => $orderId,
            'status' => $transactionStatus,
            'donation_id' => $donation->id,
        ]);

        return response()->json(['message' => 'ok']);
    }
}
