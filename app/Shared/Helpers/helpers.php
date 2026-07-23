<?php

use App\Domains\Settings\Models\Setting;
use Illuminate\Support\Number;

if (!function_exists('formatMoney')) {
    function formatMoney(float|int|string|null $amount, ?string $locale = null): string
    {
        $loc = $locale ?? app()->getLocale();
        // Force l'utilisation des chiffres latins pour la langue arabe
        if ($loc === 'ar') {
            $loc = 'en'; // Utilise le format anglais (1,000.00) qui affiche des chiffres latins
        }
        return Number::format((float) ($amount ?? 0), precision: 2, locale: $loc);
    }
}

if (!function_exists('getMonthPeriodStartDay')) {
    function getMonthPeriodStartDay(): int
    {
        return (int) Setting::get('month_period_start_day', 20);
    }
}

if (!function_exists('getPeriodFromDate')) {
    function getPeriodFromDate($date): string
    {
        $date = $date instanceof \Carbon\Carbon ? $date : \Carbon\Carbon::parse($date);
        $startDay = getMonthPeriodStartDay();
        if ($date->day > $startDay) {
            $next = $date->copy()->addMonth();
            if ($next->day !== $date->day) {
                $next = $next->modify('last day of previous month');
            }
            return $next->format('Y-m');
        }
        return $date->format('Y-m');
    }
}

if (!function_exists('getPeriodRange')) {
    function getPeriodRange(string $yearMonth): array
    {
        $startDay = getMonthPeriodStartDay();
        $date = \Carbon\Carbon::createFromFormat('Y-m-d', $yearMonth . '-01')->startOfDay();
        $end = $date->copy()->day($startDay);
        $start = $end->copy()->subMonth()->addDay();
        return ['start' => $start, 'end' => $end];
    }
}

if (!function_exists('formatPeriodLabel')) {
    function formatPeriodLabel(string $yearMonth): string
    {
        $range = getPeriodRange($yearMonth);
        $start = $range['start'];
        $end = $range['end'];
        $locale = app()->getLocale();

        $startLabel = $start->translatedFormat('d F');
        $endLabel = $end->translatedFormat('d F Y');
        if ($start->year !== $end->year) {
            $startLabel = $start->translatedFormat('d F Y');
        }

        return match ($locale) {
            'ar' => 'من ' . $startLabel . ' إلى ' . $endLabel,
            'en' => 'From ' . $startLabel . ' to ' . $endLabel,
            default => 'Du ' . $startLabel . ' au ' . $endLabel,
        };
    }
}

if (!function_exists('formatPeriodLabelShort')) {
    function formatPeriodLabelShort(string $yearMonth): string
    {
        $range = getPeriodRange($yearMonth);
        $start = $range['start'];
        $end = $range['end'];
        $locale = app()->getLocale();

        $startLabel = $start->translatedFormat('j M');
        $endLabel = $end->translatedFormat('j M Y');
        if ($start->year !== $end->year) {
            $startLabel = $start->translatedFormat('j M Y');
        }

        return match ($locale) {
            'ar' => $startLabel . ' ← ' . $endLabel,
            'en' => $startLabel . ' → ' . $endLabel,
            default => $startLabel . ' → ' . $endLabel,
        };
    }
}

if (!function_exists('getCurrency')) {
    function getCurrency(): string
    {
        return Setting::get('currency', 'MAD');
    }
}



