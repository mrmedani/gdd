<?php

namespace App\Domains\Reports\Support;

use App\Domains\Expenses\Models\Expense;
use App\Domains\Settings\Models\Setting;
use App\Domains\Treasury\Models\MonthlyClosure;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

/**
 * Génération PDF de rapport partagaée (mensuel / annuel / personnalisé) et
 * envoi WhatsApp du document. Utilisée par ReportController (POST /share)
 * ET par ReportsIndex::sendWhatsApp (toast Livewire) — une seule source.
 */
class ReportPdf
{
    /** Téléphone WhatsApp de l'utilisateur connecté ou null. */
    public static function userPhone(int $userId): ?string
    {
        $user = User::find($userId);
        return $user?->whatsapp_phone ?: null;
    }

    /**
     * Envoie le rapport par WhatsApp au compte de l'utilisateur connecté.
     * @return array{ok: bool, error: ?string}
     */
    public static function shareWhatsApp(int $userId, string $kind, array $data): array
    {
        $phone = self::userPhone($userId);
        if (!$phone) {
            return ['ok' => false, 'error' => __('reports.share_no_phone')];
        }

        try {
            $path = self::buildTemp($kind, $data);
        } catch (\Throwable $e) {
            Log::warning('ReportPdf share génération: ' . $e->getMessage());
            return ['ok' => false, 'error' => __('reports.share_error')];
        }

        $reportNice = match ($kind) {
            'monthly' => __('reports.monthly_report'),
            'annual' => __('reports.annual_report'),
            default => __('reports.custom_report'),
        };
        $caption = __('reports.share_caption', ['report' => $reportNice]);
        $ok = \App\Services\WhatsAppService::sendMedia(
            Setting::get('whatsapp_worker_url') ?: 'http://127.0.0.1:9090',
            $phone, $path, basename($path), $caption
        );
        @unlink($path);
        return $ok
            ? ['ok' => true, 'error' => null]
            : ['ok' => false, 'error' => __('reports.share_failed')];
    }

    /** Génère le PDF (temporaire) d'un rapport sur disque et renvoie son chemin. */
    public static function buildTemp(string $kind, array $data): string
    {
        if ($kind === 'monthly') {
            $yearMonth = sprintf('%04d-%02d', $data['year'], $data['month']);
            $expenses = Expense::with('category.parent', 'employee', 'creator')
                ->latest('date');
            \App\Domains\Reports\Support\ReportAggregator::applyFilters($expenses, $data);
            $expenses = $expenses->byPeriod($yearMonth)->get();
            $total = (float) $expenses->sum('amount');
            $closure = MonthlyClosure::where('month', $yearMonth)->first();
            $payload = [
                'month' => $data['year'] ? null : null,
                'year' => $data['year'],
                'month' => $data['month'],
                'yearMonth' => $yearMonth,
                'expenses' => $expenses, 'total' => $total,
                'gains' => $closure?->gains ?? 0,
                'balance' => $closure?->balance ?? ($closure?->gains ?? 0) - $total,
                'byCategory' => ReportAggregator::byCategory($expenses, $total),
                'byPaymentMethod' => ReportAggregator::byPaymentMethod($expenses),
                'topExpenses' => $expenses->sortByDesc('amount')->take(10),
                'company' => ['name' => Setting::get('app_name', config('app.name')), 'currency' => getCurrency()],
                'periodLabel' => formatPeriodLabel($yearMonth),
            ];
            $view = 'reports.monthly';
        } elseif ($kind === 'annual') {
            $expenses = Expense::with('category.parent', 'employee', 'creator')->latest('date');
            \App\Domains\Reports\Support\ReportAggregator::applyFilters($expenses, $data);
            $expenses = $expenses->byYear($data['year'])->get();
            $total = (float) $expenses->sum('amount');
            $payload = [
                'year' => $data['year'], 'expenses' => $expenses, 'total' => $total,
                'gains' => MonthlyClosure::where('month', 'like', "{$data['year']}-%")->sum('gains'),
                'byCategory' => ReportAggregator::byCategory($expenses, $total),
                'byPaymentMethod' => ReportAggregator::byPaymentMethod($expenses),
                'topExpenses' => $expenses->sortByDesc('amount')->take(10),
                'byEmployee' => ReportAggregator::byEmployee($expenses, $total),
                'company' => ['name' => Setting::get('app_name', config('app.name')), 'currency' => getCurrency()],
            ];
            $view = 'reports.annual';
        } elseif ($kind === 'custom') {
            $start = \Carbon\Carbon::parse($data['date_from'])->startOfDay();
            $end = \Carbon\Carbon::parse($data['date_to'])->endOfDay();
            $expenses = Expense::with('category.parent', 'employee', 'creator')
                ->whereBetween('date', [$start->toDateString(), $end->toDateString()])->latest('date');
            \App\Domains\Reports\Support\ReportAggregator::applyFilters($expenses, $data);
            $expenses = $expenses->get();
            $payload = self::buildRangePayload($expenses, $start, $end);
            $view = 'reports.custom';
        } else {
            throw new \InvalidArgumentException('kind inconnu : ' . $kind);
        }

        $pdf = Pdf::loadView($view, $payload);
        $pdf->setPaper('A4', 'portrait');
        $tmp = ini_get('upload_tmp_dir') ?: sys_get_temp_dir();
        $path = $tmp . DIRECTORY_SEPARATOR . 'report-' . $kind . '-' . md5(json_encode($data)) . '.pdf';
        $pdf->save($path);
        return $path;
    }

    /** Payload génériques pour /reports.custom (rapport personnalisé). */
    public static function buildRangePayload($expenses, \Carbon\Carbon $start, \Carbon\Carbon $end): array
    {
        $total = (float) $expenses->sum('amount');
        $daily = ReportAggregator::dailyBreakdown($expenses, $start, $end);

        return [
            'periodLabel' => __('reports.custom_range', ['from' => $start->format('d/m/Y'), 'to' => $end->format('d/m/Y')]),
            'periodStart' => $start->format('d/m/Y'),
            'periodEnd' => $end->format('d/m/Y'),
            'expenses' => $expenses,
            'total' => $total,
            'count' => $expenses->count(),
            'avg' => $expenses->count() > 0 ? round($total / max(1, $start->diffInDays($end) + 1), 2) : 0,
            'byCategory' => ReportAggregator::byCategory($expenses, $total),
            'byPaymentMethod' => ReportAggregator::byPaymentMethod($expenses),
            'byEmployee' => ReportAggregator::byEmployee($expenses, $total),
            'topExpenses' => $expenses->sortByDesc('amount')->take(10),
            'dailyBreakdown' => $daily,
            'maxDaily' => $daily->max('total'),
            'busiestDay' => $daily->sortByDesc('total')->first(),
            'company' => [
                'name' => Setting::get('app_name', config('app.name')),
                'currency' => getCurrency(),
            ],
        ];
    }
}
