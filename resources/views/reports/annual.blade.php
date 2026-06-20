<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('reports.annual_report') }} {{ $year }}</title>
    <style>
        @page { margin: 20mm 15mm 25mm 15mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1e293b; line-height: 1.5; }
        .header { text-align: center; border-bottom: 3px solid #1e40af; padding-bottom: 12px; margin-bottom: 20px; }
        .header h1 { color: #1e40af; font-size: 18px; margin: 0 0 4px 0; }
        .header .company-name { font-size: 14px; color: #475569; margin: 0; }
        .header .period { font-size: 11px; color: #64748b; margin: 4px 0 0 0; }
        .section { margin-bottom: 20px; }
        .section h2 { color: #1e40af; font-size: 13px; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px; margin: 0 0 10px 0; text-transform: uppercase; letter-spacing: 0.5px; }
        .kpi-grid { display: flex; gap: 15px; margin-bottom: 20px; }
        .kpi-card { flex: 1; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; text-align: center; }
        .kpi-card .label { font-size: 8px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .kpi-card .value { font-size: 16px; font-weight: bold; color: #0f172a; }
        .kpi-card.primary { background: #eff6ff; border-color: #bfdbfe; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background: #1e40af; color: white; padding: 8px 10px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.3px; }
        th.right { text-align: right; }
        td { padding: 6px 10px; border-bottom: 1px solid #e2e8f0; font-size: 9px; }
        td.right { text-align: right; }
        tr:nth-child(even) { background: #f8fafc; }
        .total-row td { font-weight: bold; border-top: 2px solid #1e40af; border-bottom: 2px solid #1e40af; }
        .month-bar { height: 16px; background: #f1f5f9; border-radius: 3px; overflow: hidden; }
        .month-bar .fill { height: 100%; border-radius: 3px; display: flex; align-items: center; justify-content: flex-end; padding-right: 4px; font-size: 7px; color: white; font-weight: bold; min-width: 20px; }
        .month-bar .fill.low { background: #3b82f6; }
        .month-bar .fill.medium { background: #2563eb; }
        .month-bar .fill.high { background: #1d4ed8; }
        .month-bar .fill.very-high { background: #1e40af; }
        .category-item { display: flex; align-items: center; padding: 4px 0; border-bottom: 1px solid #f1f5f9; font-size: 9px; }
        .category-item .cat-name { flex: 1; }
        .category-item .cat-pct { width: 40px; text-align: right; color: #64748b; margin-right: 10px; }
        .category-item .cat-amount { width: 80px; text-align: right; font-weight: bold; }
        .footer { position: fixed; bottom: -20mm; left: 0; right: 0; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 5px; }
        .footer .page-number:after { content: "Page " counter(page); }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ __('reports.annual_report') }}</h1>
        <p class="company-name">{{ $company['name'] }}</p>
        <p class="period">{{ __('reports.year') }} {{ $year }}</p>
    </div>

    <!-- KPI Cards -->
    <div class="kpi-grid">
        <div class="kpi-card success">
            <div class="label">{{ __('reports.total_gains') }}</div>
            <div class="value" style="color:#059669">{{ formatMoney($gains) }} {{ $company['currency'] }}</div>
        </div>
        <div class="kpi-card primary">
            <div class="label">{{ __('reports.total_expenses') }}</div>
            <div class="value">{{ formatMoney($total) }} {{ $company['currency'] }}</div>
        </div>
        <div class="kpi-card" style="border-color: {{ $balance >= 0 ? '#bbf7d0' : '#fecaca' }}">
            <div class="label">{{ __('reports.balance') }}</div>
            <div class="value" style="color: {{ $balance >= 0 ? '#059669' : '#dc2626' }}">{{ formatMoney($balance) }} {{ $company['currency'] }}</div>
        </div>
        <div class="kpi-card">
            <div class="label">{{ __('reports.monthly_avg') }}</div>
            <div class="value">{{ formatMoney($expenses->count() > 0 ? $total / max($byMonth->filter(fn($m) => $m['count'] > 0)->count(), 1) : 0) }}</div>
        </div>
    </div>

    <!-- Monthly Breakdown with Bars -->
    @if(count($byMonth) > 0)
        <div class="section">
            <h2>{{ __('reports.monthly_breakdown') }}</h2>
            <table>
                <thead>
                    <tr>
                        <th style="width:25%">{{ __('reports.month') }}</th>
                        <th>{{ __('reports.distribution') }}</th>
                        <th class="right" style="width:15%">{{ __('expenses.amount') }}</th>
                        <th class="right" style="width:10%">{{ __('reports.operations') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php $maxMonthly = $byMonth->max('total'); @endphp
                    @foreach($byMonth as $m)
                        @php
                            $pct = $maxMonthly > 0 ? ($m['total'] / $maxMonthly) * 100 : 0;
                            $barClass = $pct > 75 ? 'very-high' : ($pct > 50 ? 'high' : ($pct > 25 ? 'medium' : 'low'));
                        @endphp
                        <tr>
                            <td>{{ $m['label'] }}</td>
                            <td>
                                <div class="month-bar">
                                    @if($pct > 0)
                                        <div class="fill {{ $barClass }}" style="width: {{ max($pct, 3) }}%">
                                            @if($pct > 30){{ formatMoney($m['total']) }}@endif
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="right">{{ formatMoney($m['total']) }}</td>
                            <td class="right">{{ $m['count'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td>{{ __('reports.total') }}</td>
                        <td></td>
                        <td class="right">{{ formatMoney($total) }}</td>
                        <td class="right">{{ $expenses->count() }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif

    <!-- Category Summary -->
    @if(count($byCategory) > 0)
        <div class="section">
            <h2>{{ __('dashboard.by_category') }}</h2>
            @foreach($byCategory as $cat => $data)
                <div class="category-item">
                    <span class="cat-name">{{ $cat }}</span>
                    <span class="cat-pct">{{ $data['percentage'] }}%</span>
                    <span class="cat-amount">{{ formatMoney($data['total']) }} ({{ $data['count'] }})</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- All Expenses -->
    <div class="section">
        <h2>{{ __('expenses.list') }}</h2>
        <table>
            <thead>
                <tr>
                    <th>{{ __('expenses.date') }}</th>
                    <th>{{ __('expenses.description') }}</th>
                    <th>{{ __('expenses.category') }}</th>
                    <th>{{ __('expenses.employee') }}</th>
                    <th class="right">{{ __('expenses.amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                    <tr>
                        <td>{{ $expense->date->format('d/m/Y') }}</td>
                        <td>{{ $expense->description }}</td>
                        <td>{{ $expense->category?->translated_name ?? $expense->category_key }}</td>
                        <td>{{ $expense->employee?->name ?? '-' }}</td>
                        <td class="right">{{ formatMoney($expense->amount) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center; padding:20px;">{{ __('reports.no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        <span>{{ __('reports.generated_at') }} {{ now()->format('d/m/Y H:i') }} | </span>
        <span class="page-number"></span>
    </div>
</body>
</html>
