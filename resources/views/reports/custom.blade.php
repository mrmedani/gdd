<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ session('locale', 'ar') === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('reports.custom_report') }} — {{ $periodLabel ?? '' }}</title>
    <style>
        /* Palette cohérente avec les templates mensuel/annuel existants */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; color: #1e293b; font-size: 11px; }
        .header { background: #1e293b; color: #fff; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 20px; font-weight: 800; }
        .header .meta { text-align: right; font-size: 10px; color: #cbd5e1; }
        .header .meta strong { color: #fff; }
        .content { padding: 20px 24px; }
        .kpi-grid { display: flex; gap: 12px; margin-bottom: 20px; }
        .kpi { flex: 1; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px; text-align: center; }
        .kpi .label { font-size: 9px; text-transform: uppercase; letter-spacing: .06em; color: #94a3b8; font-weight: 700; margin-bottom: 4px; }
        .kpi .value { font-size: 16px; font-weight: 800; }
        .kpi .rose { color: #e11d48; } .kpi .emerald { color: #059669; } .kpi .blue { color: #2563eb; }
        h2 { font-size: 14px; margin: 18px 0 8px; color: #0f172a; font-weight: 700; border-bottom: 2px solid #3b82f6; padding-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th { background: #f1f5f9; color: #475569; font-size: 10px; text-transform: uppercase; letter-spacing: .05em; padding: 6px 8px; border: 1px solid #e2e8f0; text-align: left; font-weight: 700; }
        td { padding: 5px 8px; border: 1px solid #f1f5f9; font-size: 10.5px; }
        tr:nth-child(even) td { background: #f8fafc; }
        .total-row td { background: #eff6ff; font-weight: 800; border-top: 2px solid #3b82f6; }
        .num { text-align: right; white-space: nowrap; }
        .footer { margin-top: 18px; text-align: center; color: #94a3b8; font-size: 9px; border-top: 1px solid #e2e8f0; padding-top: 8px; }
        .two-col { display: flex; gap: 14px; }
        .two-col > div { flex: 1; }
        .bar { height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden; }
        .bar span { display: block; height: 100%; background: #3b82f6; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>{{ __('reports.custom_report') }}</h1>
            <div class="meta">{{ $periodLabel }}</div>
        </div>
        <div class="meta">
            <strong>{{ $company['name'] }}</strong><br>
            {{ now()->translatedFormat(__("reports.generated_at")) }}
        </div>
    </div>

    <div class="content">
        {{-- KPI --}}
        <div class="kpi-grid">
            <div class="kpi"><div class="label">{{ __('reports.total_expenses') }}</div><div class="value rose">{{ formatMoney($total) }} {{ $company['currency'] }}</div></div>
            <div class="kpi"><div class="label">{{ __('reports.expense_count') }}</div><div class="value blue">{{ $count }}</div></div>
            <div class="kpi"><div class="label">{{ __('reports.avg_per_day') }}</div><div class="value emerald">{{ formatMoney($avg) }} {{ $company['currency'] }}</div></div>
            <div class="kpi"><div class="label">{{ __('reports.busiest_day') }}</div><div class="value">{{ $busiestDay ? $busiestDay['label'] . " (" . formatMoney($busiestDay['total']) . ")" : '—' }}</div></div>
        </div>

        {{-- Catégories + Paiement --}}
        <div class="two-col">
            <div>
                <h2>{{ __('dashboard.by_category') }}</h2>
                <table>
                    <thead><tr><th style="width:45%">{{ __('expenses.category') }}</th><th class="num" style="width:12%">{{ __('reports.share') }}</th><th class="num" style="width:22%">{{ __('expenses.amount') }}</th><th class="num"></th></tr></thead>
                    <tbody>
                        @foreach($byCategory as $catName => $info)
                            <tr>
                                <td>{{ $catName }}</td>
                                <td class="num">{{ $info['percentage'] }}%</td>
                                <td class="num"><strong>{{ number_format($info['total'], 2) }}</strong></td>
                                <td style="vertical-align:middle"><div class="bar"><span style="width: {{ $info['percentage'] }}%"></span></div></td>
                            </tr>
                        @endforeach
                        <tr class="total-row"><td colspan="2">{{ __('reports.total') }}</td><td class="num">{{ number_format($total, 2) }}</td><td></td></tr>
                    </tbody>
                </table>
            </div>
            <div>
                <h2>{{ __('expenses.payment_method') }}</h2>
                <table>
                    <thead><tr><th>{{ __('expenses.payment_method') }}</th><th class="num">{{ __('reports.expense_count') }}</th><th class="num">{{ __('expenses.amount') }}</th></tr></thead>
                    <tbody>
                        @forelse($byPaymentMethod as $method => $info)
                            <tr><td>{{ __("payment_methods.{$method}") }}</td><td class="num">{{ $info['count'] }}</td><td class="num"><strong>{{ number_format($info['total'], 2) }}</strong></td></tr>
                        @empty
                            <tr><td colspan="3" style="text-align:center; color:#94a3b8">{{ __('reports.no_data') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <h2>{{ __('dashboard.by_employee') }}</h2>
                <table>
                    <thead><tr><th style="width:40%">{{ __('employees.employee') }}</th><th class="num">{{ __('reports.share') }}</th><th class="num">{{ __('expenses.amount') }}</th></tr></thead>
                    <tbody>
                        @forelse($byEmployee as $info)
                            <tr><td>{{ $info['name'] }}</td><td class="num">{{ $info['percentage'] }}%</td><td class="num"><strong>{{ number_format($info['total'], 2) }}</strong></td></tr>
                        @empty
                            <tr><td colspan="3" style="text-align:center; color:#94a3b8">{{ __('reports.no_data') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Top 10 --}}
        <h2>{{ __('reports.top_expenses') }}</h2>
        <table>
            <thead><tr><th style="width:10%">{{ __('expenses.date') }}</th><th style="width:40%">{{ __('expenses.description') }}</th><th class="num">{{ __('expenses.amount') }}</th><th style="width:20%">{{ __('expenses.category') }}</th><th style="width:16%">{{ __('employees.employee') }}</th></tr></thead>
            <tbody>
                @forelse($topExpenses as $e)
                    <tr><td>{{ $e->date->format('d/m/Y') }}</td><td>{{ Str::limit($e->description, 50) }}</td><td class="num"><strong>{{ number_format($e->amount, 2) }}</strong></td><td>{{ $e->category?->translated_name ?? $e->category_key }}</td><td>{{ $e->employee?->name ?? '—' }}</td></tr>
                @empty
                    <tr><td colspan="5" style="text-align:center; color:#94a3b8">{{ __('reports.no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>

        {{-- Détail journalier (solide) --}}
        <h2>{{ __('reports.daily_breakdown') }}</h2>
        <table>
            <thead><tr><th>{{ __('reports.date') }}</th><th class="num">{{ __('reports.operation_count') }}</th><th class="num">{{ __('expenses.amount') }}</th></tr></thead>
            <tbody>
                @foreach($dailyBreakdown as $d)
                    @if($d['total'] > 0)
                        <tr>
                            <td>{{ $d['label'] }} / {{ $d['date']->format('d/m') }}</td>
                            <td class="num">{{ $d['count'] }}</td>
                            <td class="num">{{ number_format($d['total'], 2) }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            {{ __('reports.footer_generated_by', ['name' => $company['name']]) }} — {{ __("reports.custom_report") }}
        </div>
    </div>
</body>
</html>
