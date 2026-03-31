<?php

namespace App\Services\BankMutation;

use Illuminate\Support\Facades\Storage;

class JsonFileBankMutationProvider implements BankMutationProviderInterface
{
    public function __construct(private readonly string $disk, private readonly string $path)
    {
    }

    public function fetchIncomingMutations(): array
    {
        if (! Storage::disk($this->disk)->exists($this->path)) {
            return [];
        }

        $raw = Storage::disk($this->disk)->get($this->path);
        $decoded = json_decode($raw, true);
        if (! is_array($decoded)) {
            return [];
        }

        $rows = [];
        foreach ($decoded as $item) {
            if (! is_array($item)) {
                continue;
            }

            $reference = trim((string) ($item['reference'] ?? ''));
            $amount = (int) ($item['amount'] ?? 0);
            $occurredAt = trim((string) ($item['occurred_at'] ?? ''));
            $description = trim((string) ($item['description'] ?? ''));

            if ($reference === '' || $amount <= 0 || $occurredAt === '') {
                continue;
            }

            $rows[] = [
                'reference' => $reference,
                'amount' => $amount,
                'occurred_at' => $occurredAt,
                'description' => $description !== '' ? $description : null,
            ];
        }

        return $rows;
    }
}
