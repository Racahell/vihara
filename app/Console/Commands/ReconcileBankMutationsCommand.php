<?php

namespace App\Console\Commands;

use App\Services\BankMutation\BankMutationReconciler;
use Illuminate\Console\Command;

class ReconcileBankMutationsCommand extends Command
{
    protected $signature = 'donations:reconcile-bank {--dry-run : Simulasi tanpa menyimpan perubahan}';

    protected $description = 'Rekonsiliasi mutasi bank untuk auto-verifikasi donasi transfer.';

    public function handle(BankMutationReconciler $reconciler): int
    {
        $result = $reconciler->reconcile((bool) $this->option('dry-run'));

        $this->info('Rekonsiliasi selesai.');
        $this->line('Total mutasi: ' . $result['total']);
        $this->line('Matched: ' . $result['matched']);
        $this->line('Skipped: ' . $result['skipped']);

        return self::SUCCESS;
    }
}
