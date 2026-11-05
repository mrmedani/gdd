<?php

namespace App\Shared\Support;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * SQL de regroupement des DÉPENSES par période comptable (découpage 21→20),
 * compatible MySQL ET SQLite (extrait de StatisticsIndex, partagé avec Dashboard).
 *
 * Principe : une dépense appartiendra à la période dont le jour 21 ouvre la
 * période — si DAY(date) > 21 (configurable via getMonthPeriodStartDay) la
 * dépense glisse sur le mois suivant, sinon elle reste sur le mois courant.
 */
class PeriodSql
{
    /**
     * @param  Carbon  $start  début de la fenêtre (inclus)
     * @param  Carbon  $end    fin de la fenêtre (inclus)
     * @return array<string, float>  période "Y-m" => total
     */
    public static function expenseTotalsByPeriod(Carbon $start, Carbon $end): array
    {
        $startDay = getMonthPeriodStartDay();
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            $expr = "strftime('%Y-%m',
                CASE
                    WHEN CAST(strftime('%d', date) AS INT) > ? THEN date(date, '+1 month')
                    ELSE date
                END) as period,
            SUM(amount) as total";
        } else {
            $expr = "DATE_FORMAT(
                CASE
                    WHEN DAY(date) > ? THEN DATE_ADD(date, INTERVAL 1 MONTH)
                    ELSE date
                END,
                '%Y-%m'
            ) as period,
            SUM(amount) as total";
        }

        return \App\Domains\Expenses\Models\Expense::query()
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw($expr, [$startDay])
            ->groupBy('period')
            ->pluck('total', 'period')
            ->map(fn ($t) => (float) $t)
            ->all();
    }
}
