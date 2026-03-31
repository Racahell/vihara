<?php

namespace App\Services\BankMutation;

interface BankMutationProviderInterface
{
    /**
     * @return array<int, array{
     *   reference:string,
     *   amount:int,
     *   occurred_at:string,
     *   description?:string
     * }>
     */
    public function fetchIncomingMutations(): array;
}
