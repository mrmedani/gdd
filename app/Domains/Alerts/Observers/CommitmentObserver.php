<?php

namespace App\Domains\Alerts\Observers;

use App\Domains\Alerts\Models\Commitment;

class CommitmentObserver
{
    public function created(Commitment $c): void
    {
        \App\Domains\AI\Support\AiCacheInvalidator::invalidateCommitments();
    }

    public function updated(Commitment $c): void
    {
        \App\Domains\AI\Support\AiCacheInvalidator::invalidateCommitments();
    }

    public function deleted(Commitment $c): void
    {
        \App\Domains\AI\Support\AiCacheInvalidator::invalidateCommitments();
    }

    /** Soft-deleted via restore aussi */
    public function restored(Commitment $c): void {}

    public function forceDeleted(Commitment $c): void
    {
        \App\Domains\AI\Support\AiCacheInvalidator::invalidateCommitments();
    }
}
