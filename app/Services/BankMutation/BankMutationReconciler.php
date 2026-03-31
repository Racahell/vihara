<?php

namespace App\Services\BankMutation;

use App\Models\Donation;
use App\Services\DonationSettlementService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class BankMutationReconciler
{
    public function __construct(
        private readonly BankMutationProviderInterface $provider,
        private readonly DonationSettlementService $donationSettlementService
    ) {
    }

    /**
     * @return array{total:int,matched:int,skipped:int}
     */
    public function reconcile(bool $dryRun = false): array
    {
        $mutations = $this->provider->fetchIncomingMutations();
        $matched = 0;
        $skipped = 0;

        foreach ($mutations as $mutation) {
            $amount = (int) ($mutation['amount'] ?? 0);
            $reference = (string) ($mutation['reference'] ?? '');
            $occurredAtRaw = (string) ($mutation['occurred_at'] ?? '');

            if ($amount <= 0 || $reference === '' || $occurredAtRaw === '') {
                $skipped++;
                continue;
            }

            try {
                $occurredAt = CarbonImmutable::parse($occurredAtRaw);
            } catch (\Throwable) {
                $skipped++;
                continue;
            }
            $windowStart = $occurredAt->subHours((int) config('services.bank_mutation.match_window_hours', 48));
            $windowEnd = $occurredAt->addHours((int) config('services.bank_mutation.match_window_hours', 48));

            $donation = Donation::query()
                ->where('payment_method', 'transfer')
                ->where('verification_status', 'pending')
                ->whereIn('payment_status', ['pending', 'paid'])
                ->where('amount', $amount)
                ->whereBetween('donated_at', [$windowStart, $windowEnd])
                ->orderBy('donated_at')
                ->first();

            if (! $donation) {
                $skipped++;
                continue;
            }

            if ($dryRun) {
                $matched++;
                continue;
            }

            DB::transaction(function () use ($donation, $mutation, $reference): void {
                $lockedDonation = Donation::query()->whereKey($donation->id)->lockForUpdate()->first();
                if (! $lockedDonation) {
                    return;
                }

                $payload = (array) ($lockedDonation->payment_payload ?? []);
                $payload['bank_mutation_match'] = [
                    'reference' => $reference,
                    'amount' => (int) ($mutation['amount'] ?? 0),
                    'occurred_at' => (string) ($mutation['occurred_at'] ?? ''),
                    'description' => $mutation['description'] ?? null,
                    'matched_at' => now()->toDateTimeString(),
                ];

                $paidAt = CarbonImmutable::parse((string) $mutation['occurred_at']);

                $lockedDonation->forceFill([
                    'payment_payload' => $payload,
                    'paid_at' => $paidAt,
                ])->save();

                $this->donationSettlementService->approve(
                    $lockedDonation,
                    null,
                    $paidAt,
                    now(),
                    'Auto-approved dari rekonsiliasi mutasi bank: ' . $reference
                );
            });

            $matched++;
        }

        return [
            'total' => count($mutations),
            'matched' => $matched,
            'skipped' => $skipped,
        ];
    }
}
