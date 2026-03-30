<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MidtransService
{
    public function createTransaction(array $donation): array
    {
        $serverKey = config('services.midtrans.server_key');
        $isProduction = (bool) config('services.midtrans.is_production');
        $snapUrl = $isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        $orderId = 'DON-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6));

        if (! $serverKey) {
            return [
                'order_id' => $orderId,
                'status' => 'simulated',
                'redirect_url' => null,
                'payload' => ['message' => 'Midtrans belum dikonfigurasi'],
            ];
        }

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $donation['amount'],
            ],
            'customer_details' => [
                'first_name' => $donation['donor_name'] ?? 'Donatur',
                'email' => $donation['donor_email'] ?? null,
                'phone' => $donation['donor_phone'] ?? null,
            ],
        ];

        $response = Http::withBasicAuth($serverKey, '')->post($snapUrl, $payload);

        return [
            'order_id' => $orderId,
            'status' => $response->successful() ? 'pending' : 'failed',
            'redirect_url' => $response->json('redirect_url'),
            'payload' => $response->json() ?: ['raw' => $response->body()],
        ];
    }
}
