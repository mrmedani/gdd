<?php

namespace App\Domains\Contracts\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'title',
        'party',
        'start_date',
        'end_date',
        'notes',
        'reminder_sent_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'reminder_sent_at' => 'boolean',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Contrats d'un utilisateur donné uniquement (sa liste personnelle). */
    public function scopeVisibleTo($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    /** Jours restants avant expiration (négatif = déjà expiré). */
    public function daysUntilExpiry(): int
    {
        return (int) now()->startOfDay()->diffInDays($this->end_date->copy()->startOfDay(), false);
    }

    /** Statut affiché : active / expiring / expired. */
    public function status(): string
    {
        $d = $this->daysUntilExpiry();
        if ($d < 0) return 'expired';
        if ($d <= 3) return 'expiring';
        return 'active';
    }

    /** Fenêtre d'alerte J-3 : pas encore expiré, expire dans ≤ 3 jours. */
    public function isExpiringSoon(): bool
    {
        $d = $this->daysUntilExpiry();
        return $d >= 0 && $d <= 3;
    }
}
