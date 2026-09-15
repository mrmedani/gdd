<?php

namespace App\Domains\Contracts\Observers;

use App\Domains\Contracts\Models\Contract;

class ContractObserver
{
    public function created(Contract $c): void
    {
        \App\Domains\AI\Support\AiCacheInvalidator::invalidateContracts();
    }

    public function updated(Contract $c): void
    {
        \App\Domains\AI\Support\AiCacheInvalidator::invalidateContracts();
    }

    public function deleted(Contract $c): void
    {
        \App\Domains\AI\Support\AiCacheInvalidator::invalidateContracts();
    }

    public function restored(Contract $c): void {}

    public function forceDeleted(Contract $c): void
    {
        \App\Domains\AI\Support\AiCacheInvalidator::invalidateContracts();
    }
}
