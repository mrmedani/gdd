<?php

namespace App\Domains\Alerts\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Commitment extends Model
{
    protected $fillable = [
        'label',
        'day',
        'amount',
        'lead_days',
        'is_active',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'day' => 'integer',
            'amount' => 'decimal:2',
            'lead_days' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Échéances d'un utilisateur donné uniquement (sa liste personnelle). */
    public function scopeVisibleTo($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    /**
     * Prochaine échéance (aujourd'hui inclus). Le jour est ramené au dernier
     * jour du mois si le mois est plus court (ex : 31 -> 28 février).
     */
    public function nextDueDate(?Carbon $from = null): Carbon
    {
        $from = ($from ?? now())->copy()->startOfDay();
        $candidate = $from->copy()->day(min($this->day, $from->daysInMonth));
        if ($candidate->lt($from)) {
            $next = $from->copy()->addMonth();
            $candidate = $next->copy()->day(min($this->day, $next->daysInMonth));
        }
        return $candidate->startOfDay();
    }

    /** Jours restants avant l'échéance (0 = aujourd'hui). */
    public function daysUntilDue(): int
    {
        return (int) now()->startOfDay()->diffInDays($this->nextDueDate(), false);
    }

    /** True si l'échéance tombe dans la fenêtre d'alerte (lead_days). */
    public function isDueSoon(): bool
    {
        $d = $this->daysUntilDue();
        return $d >= 0 && $d <= (int) $this->lead_days;
    }
}
