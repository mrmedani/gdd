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
            // FIX: addMonthNoOverflow() — addMonth() depuis le 31 (ou le 29/30 en février)
            // déborde (ex : 31/10 -> 1er déc.) et fait sauter le mois suivant entier
            // (alerte jour 10 posée le 31/10 -> affichait 10/12 au lieu de 10/11)
            $next = $from->copy()->addMonthNoOverflow();
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

    /**
     * FIX anti-spam : CASCADE de rappels au lieu d'un WhatsApp quotidien pendant
     * toute la fenêtre. Jours autorisés (dans [0, lead_days]) :
     *   J-lead (premier jour de la fenêtre), J-7, J-3, J-1, J-0.
     * lead_days = 3 -> rappels J-3, J-1, J-0. lead_days = 1 -> J-1, J-0.
     */
    public function isReminderDay(): bool
    {
        $d = $this->daysUntilDue();
        if ($d < 0 || $d > (int) $this->lead_days) {
            return false;
        }
        $lead = (int) $this->lead_days;
        $allowed = array_unique([$lead, 7, 3, 1, 0]);
        $allowed = array_filter($allowed, fn ($x) => $x >= 0 && $x <= $lead);
        return in_array($d, $allowed, true);
    }

    /**
     * FIX boucle ouverte : l'engagement a-t-il été "payé" pour la période courante ?
     * Heuristique : une dépense de la PÉRIODE COURANTE (découpage platform 21->20,
     * via getPeriodFromDate + getPeriodRange — cohérent avec la clôture) dont la
     * description contient un mot significatif du libellé (>= 4 chars, casse ignorée).
     * Permet le badge « Payé ce mois » sur /alerts au lieu de rester « Active » pour always.
     */
    public function wasPaidThisMonth(?Carbon $from = null): bool
    {
        $from = $from ?? now();
        $range = \getPeriodRange(\getPeriodFromDate($from));

        $label = strtolower(trim($this->label));
        $tokens = array_values(array_filter(explode(' ', $label), fn ($t) => mb_strlen($t) >= 4));
        if (empty($tokens)) {
            return false;
        }

        $rows = \App\Domains\Expenses\Models\Expense::query()
            ->whereBetween('date', [$range['start'], $range['end']])
            ->selectRaw('LOWER(description) AS d')
            ->pluck('d');

        foreach ($tokens as $t) {
            foreach ($rows as $desc) {
                if (str_contains($desc, $t)) {
                    return true;
                }
            }
        }
        return false;
    }
}
