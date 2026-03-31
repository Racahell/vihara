<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MidtransService
{
    public function createQrisTransaction(array $donation): array
    {
        $serverKey = (string) config('services.midtrans.server_key');
        $isProduction = (bool) config('services.midtrans.is_production');
        $chargeUrl = $isProduction
            ? 'https://api.midtrans.com/v2/charge'
            : 'https://api.sandbox.midtrans.com/v2/charge';

        $orderId = 'DON-QRIS-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6));

        if ($serverKey === '') {
            return [
                'order_id' => $orderId,
                'status' => 'simulated',
                'qr_string' => null,
                'qr_image' => null,
                'expired_at' => now()->addMinutes(15)->toDateTimeString(),
                'payload' => ['message' => 'Midtrans belum dikonfigurasi'],
            ];
        }

        $payload = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $donation['amount'],
            ],
            'customer_details' => [
                'first_name' => $donation['donor_name'] ?? 'Donatur',
                'email' => $donation['donor_email'] ?? null,
                'phone' => $donation['donor_phone'] ?? null,
            ],
            'custom_expiry' => [
                'expiry_duration' => 15,
                'unit' => 'minute',
            ],
        ];

        $response = Http::withBasicAuth($serverKey, '')
            ->acceptJson()
            ->post($chargeUrl, $payload);
        $body = $response->json() ?: ['raw' => $response->body()];

        $actions = collect((array) ($body['actions'] ?? []));
        $qrImage = (string) ($actions->firstWhere('name', 'generate-qr-code')['url'] ?? '');
        $qrString = (string) ($body['qr_string'] ?? '');

        return [
            'order_id' => $orderId,
            'status' => $response->successful() ? 'pending' : 'failed',
            'qr_string' => $qrString !== '' ? $qrString : null,
            'qr_image' => $qrImage !== '' ? $qrImage : null,
            'expired_at' => (string) ($body['expiry_time'] ?? now()->addMinutes(15)->toDateTimeString()),
            'payload' => $body,
        ];
    }

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
