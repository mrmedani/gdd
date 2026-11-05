<?php

namespace App\Domains\Statistics\Support;

use Illuminate\Support\Facades\Cache;

/**
 * Invalidateur du cache des statistiques (page /statistics).
 *
 * MÉCANIQUE : version numérique incrémentée. Chaque entrée de cache stats est
 * clé sur `gdd:stats:v{ver}:{period}` — la purge = incrément du compteur,
 * compatible avec TOUT driver de cache (DB, file, redis), contrairement aux tags.
 *
 * RÈGLE : toute mutation d'une donnée financial affichée en stats DOIT appeler
 * bump() — dépenses, entrées, investissements, clôtures, commissions stations.
 * (les observers et les composants Livewire s'en chargent — voir leurs fichiers).
 */
class StatsCacheInvalidator
{
    public const VER_KEY = 'gdd:stats:ver';

    public static function bump(): void
    {
        Cache::put(self::VER_KEY, (int) Cache::get(self::VER_KEY, 0) + 1, now()->addDays(60));
    }

    public static function version(): int
    {
        return (int) Cache::get(self::VER_KEY, 0);
    }

    public static function key(string $period): string
    {
        return 'gdd:stats:v' . self::version() . ':' . $period;
    }
}
