<?php

namespace App\Domains\Treasury\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Commission d'une station (agence wilaya) pour une période.
 * Registre SAISI MANUELLEMENT — pas de calcul automatique du montant.
 * Intégrée comme ligne séparée « Commissions stations » à la clôture,
 * déduite de la caisse SANS créer de dépense dans /expenses.
 */
class StationCharge extends Model
{
    // SoftDeletes inutile ici : les commissions d'une période clôturée ne doivent
    // jamais être réutilisées silencieusement. Delete() = coherence garantie.
    protected $fillable = [
        'station_name',
        'period',
        'amount',
        'paid_at',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isPaid(): bool
    {
        return $this->paid_at !== null;
    }

    /** Total RÈGLÉ pour une période. */
    public static function paidTotalFor(string $period): float
    {
        return (float) static::where('period', $period)->whereNotNull('paid_at')->sum('amount');
    }

    /** Toutes les commissions d'une période (label Y-m). */
    public function scopePeriod($query, string $period)
    {
        return $query->where('period', $period)->orderBy('station_name');
    }

    /** Total des commissions d'une période. */
    public static function totalFor(string $period): float
    {
        return (float) static::where('period', $period)->sum('amount');
    }

    /** Noms de stations déjà utilisées (pour l'autocomplétion du formulaire). */
    public static function knownStations(): array
    {
        return static::query()
            ->select('station_name')
            ->distinct()
            ->orderBy('station_name')
            ->pluck('station_name')
            ->all();
    }
}
