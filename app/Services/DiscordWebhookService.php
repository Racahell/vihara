<?php

namespace App\Services;

use App\Models\DiscordLog;
use Illuminate\Support\Facades\Http;

class DiscordWebhookService
{
    public function send(string $event, array $payload): void
    {
        $url = config('services.discord.webhook_url');

        if (! $url) {
            return;
        }

        try {
            $response = Http::timeout(8)->post($url, [
                'content' => '[' . strtoupper($event) . '] ' . json_encode($payload, JSON_UNESCAPED_UNICODE),
            ]);

            DiscordLog::create([
                'event' => $event,
                'status_code' => $response->status(),
                'payload' => $payload,
                'response_body' => $response->body(),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            DiscordLog::create([
                'event' => $event,
                'status_code' => 0,
                'payload' => $payload,
                'response_body' => $e->getMessage(),
                'created_at' => now(),
            ]);
        }
    }
}
