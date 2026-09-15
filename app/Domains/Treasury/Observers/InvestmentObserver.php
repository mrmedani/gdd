<?php

namespace App\Domains\Treasury\Observers;

use App\Domains\Treasury\Models\Investment;

class InvestmentObserver
{
    public function created(Investment $iv): void
    {
        \App\Domains\AI\Support\AiCacheInvalidator::invalidateInvestments();
    }

    public function updated(Investment $iv): void
    {
        \App\Domains\AI\Support\AiCacheInvalidator::invalidateInvestments();
    }

    public function deleted(Investment $iv): void
    {
        \App\Domains\AI\Support\AiCacheInvalidator::invalidateInvestments();
    }

    public function restored(Investment $iv): void {}

    public function forceDeleted(Investment $iv): void
    {
        \App\Domains\AI\Support\AiCacheInvalidator::invalidateInvestments();
    }
}
