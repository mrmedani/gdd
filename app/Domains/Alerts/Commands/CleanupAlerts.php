<?php

namespace App\Domains\Alerts\Commands;

use App\Domains\Alerts\Models\Alert;
use Illuminate\Console\Command;

class CleanupAlerts extends Command
{
    protected $signature = 'alerts:cleanup {days=90 : Supprimer les alertes plus vieilles que X jours}';
    protected $description = 'Delete alerts older than the specified number of days';

    public function handle(): int
    {
        $days = (int) $this->argument('days');
        $cutoff = now()->subDays($days);

        $deleted = Alert::where('created_at', '<', $cutoff)->delete();

        $this->info("Deleted {$deleted} alert(s) older than {$days} days.");
        return self::SUCCESS;
    }
}
