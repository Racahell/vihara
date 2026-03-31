<?php

namespace App\Services\BankMutation;

class NullBankMutationProvider implements BankMutationProviderInterface
{
    public function fetchIncomingMutations(): array
    {
        return [];
    }
}
