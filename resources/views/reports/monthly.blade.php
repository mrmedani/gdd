<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('reports.monthly_report') }} - {{ formatPeriodLabel($yearMonth) }}</title>
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
        .kpi-card.success { background: #f0fdf4; border-color: #bbf7d0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background: #1e40af; color: white; padding: 8px 10px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.3px; }
        th.right { text-align: right; }
        td { padding: 6px 10px; border-bottom: 1px solid #e2e8f0; font-size: 9px; }
        td.right { text-align: right; }
        tr:nth-child(even) { background: #f8fafc; }
        .total-row td { font-weight: bold; border-top: 2px solid #1e40af; border-bottom: 2px solid #1e40af; }
        .category-summary { margin-bottom: 20px; }
        .category-item { display: flex; align-items: center; padding: 4px 0; border-bottom: 1px solid #f1f5f9; }
        .category-item .cat-name { flex: 1; font-size: 10px; }
        .category-item .cat-bar { flex: 2; height: 14px; background: #f1f5f9; border-radius: 3px; margin: 0 10px; overflow: hidden; }
        .category-item .cat-bar .fill { height: 100%; background: #1e40af; border-radius: 3px; }
        .category-item .cat-amount { width: 80px; text-align: right; font-weight: bold; font-size: 10px; }
        .category-item .cat-pct { width: 40px; text-align: right; color: #64748b; font-size: 9px; }
        .footer { position: fixed; bottom: -20mm; left: 0; right: 0; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 5px; }
        .footer .page-number:after { content: "Page " counter(page); }
        .payment-grid { display: flex; gap: 15px; margin-bottom: 15px; }
        .payment-item { flex: 1; padding: 8px; border: 1px solid #e2e8f0; border-radius: 4px; text-align: center; }
        .payment-item .pm-label { font-size: 8px; color: #64748b; text-transform: uppercase; }
        .payment-item .pm-amount { font-size: 12px; font-weight: bold; color: #0f172a; margin-top: 2px; }
        .payment-item .pm-count { font-size: 8px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ __('reports.monthly_report') }}</h1>
        <p class="company-name">{{ $company['name'] }}</p>
        <p class="period">{{ formatPeriodLabel($yearMonth) }}</p>
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
            <div class="label">{{ __('reports.expense_count') }}</div>
            <div class="value">{{ $expenses->count() }}</div>
        </div>
    </div>

    <!-- Category Summary -->
    @if(count($byCategory) > 0)
        <div class="section">
            <h2>{{ __('dashboard.by_category') }}</h2>
            <div class="category-summary">
                @php $maxTotal = $byCategory->max(fn($items) => $items->sum('amount')); @endphp
                @foreach($byCategory as $cat => $items)
                    @php $catTotal = $items->sum('amount'); @endphp
                    <div class="category-item">
                        <span class="cat-name">{{ $cat }}</span>
                        <div class="cat-bar">
                            @if($maxTotal > 0)
                                <div class="fill" style="width: {{ ($catTotal / $maxTotal) * 100 }}%"></div>
                            @endif
                        </div>
                        <span class="cat-pct">{{ $total > 0 ? round(($catTotal / $total) * 100, 1) : 0 }}%</span>
                        <span class="cat-amount">{{ formatMoney($catTotal) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Payment Methods Summary -->
    @if(count($byPaymentMethod) > 0)
        <div class="section">
            <h2>{{ __('reports.payment_methods') }}</h2>
            <div class="payment-grid">
                @foreach($byPaymentMethod as $method => $data)
                    <div class="payment-item">
                        <div class="pm-label">{{ __("payment_methods.{$method}") }}</div>
                        <div class="pm-amount">{{ formatMoney($data['total']) }}</div>
                        <div class="pm-count">{{ $data['count'] }} {{ __('reports.operations') }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Expense Details -->
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
                        <td>{{ $expense->category && $expense->category->parent ? $expense->category->parent->translated_name . ' > ' . $expense->category->translated_name : ($expense->category?->translated_name ?? $expense->category_key) }}</td>
                        <td>{{ $expense->employee?->name ?? '-' }}</td>
                        <td class="right">{{ formatMoney($expense->amount) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center; padding:20px;">{{ __('reports.no_data') }}</td></tr>
                @endforelse
            </tbody>
            @if($expenses->count() > 0)
                <tfoot>
                    <tr class="total-row">
                        <td colspan="4" style="text-align:right;">{{ __('reports.total') }}</td>
                        <td class="right">{{ formatMoney($total) }} {{ $company['currency'] }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

    <div class="footer">
        <span>{{ __('reports.generated_at') }} {{ now()->format('d/m/Y H:i') }} | </span>
        <span class="page-number"></span>
    </div>
</body>
</html>
