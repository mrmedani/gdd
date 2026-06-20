<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('reports.monthly_report') }} - {{ formatPeriodLabel($yearMonth) }}</title>
    <style>
        @page { margin: 18mm 14mm 22mm 14mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #1e293b; line-height: 1.5; }

        /* ─── Header ─── */
        .header { text-align: center; padding-bottom: 14px; margin-bottom: 6px; }
        .header h1 { color: #1e40af; font-size: 20px; margin: 0 0 4px 0; letter-spacing: 1px; text-transform: uppercase; font-weight: bold; }
        .header .company-name { font-size: 13px; color: #334155; margin: 0; font-weight: bold; }
        .header .period { font-size: 11px; color: #475569; margin: 6px 0 0 0; font-weight: normal; }
        .header .meta { font-size: 8px; color: #94a3b8; margin: 8px 0 0 0; }
        .header .meta span { margin: 0 6px; }

        /* ─── Gradient Line ─── */
        .gradient-line { margin-bottom: 16px; }
        .gradient-line table { width: 100%; border-collapse: collapse; }
        .gradient-line td { height: 3px; padding: 0; }

        /* ─── Sections ─── */
        .section { margin-bottom: 20px; page-break-inside: avoid; }
        .section h2 {
            color: #1e40af; font-size: 12px; padding-bottom: 6px; margin: 0 0 10px 0;
            text-transform: uppercase; letter-spacing: 0.8px; font-weight: bold;
            border-bottom: 2px solid #e2e8f0;
        }
        .section h2 .badge {
            background: #1e40af; color: #fff; font-size: 7px; padding: 2px 8px;
            border-radius: 10px; margin-left: 6px; font-weight: normal; vertical-align: middle;
        }

        /* ─── KPI Cards ─── */
        .kpi-grid { width: 100%; margin-bottom: 18px; }
        .kpi-grid table { width: 100%; border-collapse: collapse; }
        .kpi-grid td { padding: 0; width: 25%; }
        .kpi-card {
            margin: 0 3px; padding: 12px 8px; border: 1px solid #e2e8f0;
            border-radius: 6px; text-align: center;
        }
        .kpi-card .label {
            font-size: 7px; color: #64748b; text-transform: uppercase;
            letter-spacing: 0.5px; margin-bottom: 4px; font-weight: bold;
        }
        .kpi-card .value { font-size: 14px; font-weight: bold; color: #0f172a; margin: 4px 0; }
        .kpi-card .sub { font-size: 7px; color: #64748b; margin-top: 4px; line-height: 1.4; }
        .delta-pos { color: #059669; font-weight: bold; }
        .delta-neg { color: #dc2626; font-weight: bold; }
        .kpi-card.bg-green { background: #f0fdf4; border-color: #bbf7d0; }
        .kpi-card.bg-blue { background: #eff6ff; border-color: #bfdbfe; }
        .kpi-card.bg-amber { background: #fffbeb; border-color: #fde68a; }
        .kpi-card.bg-purple { background: #faf5ff; border-color: #e9d5ff; }

        /* ─── Data Tables ─── */
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.data thead th {
            background: #1e40af; color: #fff; padding: 7px 8px; text-align: left;
            font-size: 8px; text-transform: uppercase; letter-spacing: 0.3px;
        }
        table.data thead th.right { text-align: right; }
        table.data thead th.center { text-align: center; }
        table.data tbody td {
            padding: 5px 8px; border-bottom: 1px solid #e2e8f0;
            font-size: 8px; vertical-align: middle;
        }
        table.data tbody td.right { text-align: right; }
        table.data tbody td.center { text-align: center; }
        table.data tbody tr:nth-child(even) { background: #f8fafc; }
        table.data tfoot td {
            font-weight: bold; border-top: 2px solid #1e40af;
            background: #f1f5f9; padding: 6px 8px; font-size: 8px;
        }
        table.data tfoot td.right { text-align: right; }

        /* ─── Progress Bars ─── */
        .bar-cell { position: relative; }
        .bar-bg { background: #f1f5f9; border-radius: 3px; height: 12px; overflow: hidden; width: 100%; }
        .bar-fill { height: 100%; border-radius: 3px; min-width: 2px; }

        /* ─── Color Dots ─── */
        .dot { display: inline-block; width: 6px; height: 6px; border-radius: 50%; vertical-align: middle; margin-right: 3px; }

        /* ─── Rank Badges ─── */
        .rank-badge {
            display: inline-block; width: 16px; height: 16px; line-height: 16px;
            text-align: center; border-radius: 50%; font-size: 7px; font-weight: bold; color: #fff;
        }
        .rank-1 { background: #f59e0b; }
        .rank-2 { background: #94a3b8; }
        .rank-3 { background: #d97706; }
        .rank-default { background: #cbd5e1; color: #475569; }

        /* ─── Variance ─── */
        .variance-pos { color: #059669; font-weight: bold; }
        .variance-neg { color: #dc2626; font-weight: bold; }

        /* ─── Highlight Box ─── */
        .highlight-box {
            background: #f0f7ff; border: 1px solid #bfdbfe; border-radius: 6px;
            padding: 10px 14px; margin-bottom: 16px; text-align: center;
            font-size: 9px; color: #334155; border-left: 4px solid #1e40af;
        }
        .highlight-box strong { color: #1e293b; }

        /* ─── Footer ─── */
        .footer {
            position: fixed; bottom: -20mm; left: 0; right: 0;
            text-align: center; font-size: 7px; color: #94a3b8;
            border-top: 1px solid #e2e8f0; padding-top: 4px;
        }
        .footer .page-number:after { content: "Page " counter(page); }

        /* ─── RTL Support for Arabic ─── */
        .rtl {
            direction: rtl;
            text-align: right;
        }
        .rtl table.data thead th {
            text-align: right;
        }
        .rtl table.data thead th.right {
            text-align: left;
        }
        .rtl table.data tbody td {
            text-align: right;
        }
        .rtl table.data tbody td.right {
            text-align: left;
        }
        .rtl table.data tfoot td {
            text-align: right;
        }
        .rtl table.data tfoot td.right {
            text-align: left;
        }
        .rtl .dot {
            margin-right: 0;
            margin-left: 3px;
        }
        .rtl .highlight-box {
            border-left: none;
            border-right: 4px solid #1e40af;
        }
    </style>
</head>
<body class="{{ app()->getLocale() === 'ar' ? 'rtl' : '' }}">

    <!-- HEADER -->
    <div class="header">
        <h1>{{ __('reports.monthly_report') }}</h1>
        <p class="company-name">{{ $company['name'] }}</p>
        <p class="period">{{ $periodLabel }}</p>
        <div class="meta">
            <span>{{ __('reports.generated_date') }}: {{ now()->format('d/m/Y H:i') }}</span>
            <span>|</span>
            <span>{{ __('reports.currency') }}: {{ $company['currency'] }}</span>
        </div>
    </div>

    <!-- Gradient Line -->
    <div class="gradient-line">
        <table><tr>
            <td style="background: #1e3a8a; width: 33%;"></td>
            <td style="background: #2563eb; width: 34%;"></td>
            <td style="background: #93c5fd; width: 33%;"></td>
        </tr></table>
    </div>

    <!-- EXECUTIVE SUMMARY KPI ROW -->
    <div class="kpi-grid">
        <table><tr>
            <td>
                <div class="kpi-card bg-green">
                    <div class="label">{{ __('reports.total_gains') }}</div>
                    <div class="value" style="color:#059669">{{ formatMoney($gains) }} {{ $company['currency'] }}</div>
                    <div class="sub">
                        {{ __('reports.prev_period') }}: {{ formatMoney($prevGains) }} {{ $company['currency'] }}
                        @php $gainDelta = $gains - $prevGains; @endphp
                        @if($gainDelta != 0)
                            <br><span class="{{ $gainDelta >= 0 ? 'delta-pos' : 'delta-neg' }}">
                                {{ $gainDelta >= 0 ? '+' : '' }}{{ formatMoney($gainDelta) }}
                            </span>
                        @endif
                    </div>
                </div>
            </td>
            <td>
                <div class="kpi-card bg-blue">
                    <div class="label">{{ __('reports.total_expenses') }}</div>
                    <div class="value">{{ formatMoney($total) }} {{ $company['currency'] }}</div>
                    <div class="sub">
                        {{ __('reports.prev_period') }}: {{ formatMoney($prevTotal) }} {{ $company['currency'] }}
                        @php $expDelta = $total - $prevTotal; @endphp
                        @if($expDelta != 0)
                            <br><span class="{{ $expDelta <= 0 ? 'delta-pos' : 'delta-neg' }}">
                                {{ $expDelta >= 0 ? '+' : '' }}{{ formatMoney($expDelta) }}
                            </span>
                        @endif
                    </div>
                </div>
            </td>
            <td>
                <div class="kpi-card bg-amber">
                    <div class="label">{{ __('reports.balance') }}</div>
                    <div class="value" style="color: {{ $balance >= 0 ? '#059669' : '#dc2626' }}">{{ formatMoney($balance) }} {{ $company['currency'] }}</div>
                    <div class="sub">
                        {{ $balance >= 0 ? __('reports.surplus') : __('reports.deficit') }}
                    </div>
                </div>
            </td>
            <td>
                <div class="kpi-card bg-purple">
                    <div class="label">{{ __('reports.expense_count') }}</div>
                    <div class="value">{{ $expenses->count() }}</div>
                    <div class="sub">
                        {{ __('reports.avg') }} {{ formatMoney($dailyAverage) }} {{ $company['currency'] }}/{{ __('reports.day') }}
                        @if($busiestDay && $busiestDay['total'] > 0)
                            <br>{{ __('reports.busiest_day') }}: {{ $busiestDay['date']->translatedFormat('d M') }}
                            <br>({{ formatMoney($busiestDay['total']) }} {{ $company['currency'] }})
                        @endif
                    </div>
                </div>
            </td>
        </tr></table>
    </div>

    <!-- YTD & PERIOD INFO -->
    <div class="highlight-box">
        <strong>{{ __('reports.ytd_cumulative') }} {{ $year }}:</strong> {{ formatMoney($ytdTotal) }} {{ $company['currency'] }}
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <strong>{{ __('reports.daily_avg') }}:</strong> {{ formatMoney($dailyAverage) }} {{ $company['currency'] }}
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <strong>{{ __('reports.period_days') }}:</strong> {{ $dailyBreakdown->count() }} {{ __('reports.days') }}
    </div>

    <!-- DAILY TREND (compact: only active days) -->
    @php
        $activeDays = $dailyBreakdown->filter(fn($d) => $d['total'] > 0);
        $weekly = collect();
        $dailyBreakdown->groupBy(fn($d) => $d['date']->weekOfYear)->each(function($days, $weekNum) use ($weekly) {
            $weekly->push([
                'week' => $weekNum,
                'label' => $days->first()['date']->translatedFormat('d M') . ' - ' . $days->last()['date']->translatedFormat('d M'),
                'total' => $days->sum('total'),
                'count' => $days->sum('count'),
            ]);
        });
        $maxWeekly = $weekly->max('total');
    @endphp
    @if($activeDays->count() > 0)
        <div class="section">
            <h2>{{ __('reports.active_days') }} <span class="badge">{{ $activeDays->count() }}/{{ $dailyBreakdown->count() }} {{ __('reports.days') }}</span></h2>
            <table class="data">
                <thead>
                    <tr>
                        <th style="width:30px">#</th>
                        <th style="width:65px">{{ __('reports.day') }}</th>
                        <th>{{ __('reports.distribution') }}</th>
                        <th class="right" style="width:90px">{{ __('expenses.amount') }} ({{ $company['currency'] }})</th>
                        <th class="center" style="width:30px">Nb</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activeDays as $day)
                        @php $barPct = $maxDaily > 0 ? ($day['total'] / $maxDaily) * 100 : 0; @endphp
                        <tr>
                            <td style="color:#94a3b8;">{{ $loop->iteration }}</td>
                            <td>{{ $day['label'] }}</td>
                            <td class="bar-cell">
                                @if($barPct > 0)
                                    <div class="bar-bg">
                                        <div class="bar-fill" style="width: {{ max($barPct, 1) }}%; background: #3b82f6;"></div>
                                    </div>
                                @endif
                            </td>
                            <td class="right" style="font-weight:bold;">{{ formatMoney($day['total']) }}</td>
                            <td class="center">{{ $day['count'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if($weekly->count() > 0)
        <div class="section">
            <h2>{{ __('reports.weekly_summary') }} <span class="badge">{{ $weekly->count() }} {{ __('reports.weeks') }}</span></h2>
            <table class="data">
                <thead>
                    <tr>
                        <th>{{ __('reports.week') }}</th>
                        <th>{{ __('reports.distribution') }}</th>
                        <th class="right" style="width:90px">{{ __('expenses.amount') }} ({{ $company['currency'] }})</th>
                        <th class="center" style="width:30px">Nb</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($weekly as $w)
                        @php $barPct = $maxWeekly > 0 ? ($w['total'] / $maxWeekly) * 100 : 0; @endphp
                        <tr>
                            <td>{{ $w['label'] }}</td>
                            <td class="bar-cell">
                                @if($barPct > 0)
                                    <div class="bar-bg">
                                        <div class="bar-fill" style="width: {{ max($barPct, 1) }}%; background: #6366f1;"></div>
                                    </div>
                                @endif
                            </td>
                            <td class="right" style="font-weight:bold;">{{ formatMoney($w['total']) }}</td>
                            <td class="center">{{ $w['count'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td>{{ __('reports.total') }}</td>
                        <td></td>
                        <td class="right">{{ formatMoney($total) }} {{ $company['currency'] }}</td>
                        <td class="center">{{ $expenses->count() }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif

    <!-- CATEGORY BREAKDOWN -->
    @if(count($byCategory) > 0)
        @php $categoryColors = ['#ef4444','#3b82f6','#10b981','#f59e0b','#8b5cf6','#ec4899','#06b6d4','#f97316','#14b8a6','#6366f1','#84cc16','#d946ef','#0ea5e9','#eab308','#22c55e']; @endphp
        <div style="page-break-inside: avoid;" class="section">
            <h2>{{ __('dashboard.by_category') }} <span class="badge">{{ count($byCategory) }}</span></h2>
            <table class="data">
                <thead>
                    <tr>
                        <th>{{ __('expenses.category') }}</th>
                        <th>{{ __('reports.distribution') }}</th>
                        <th class="right" style="width:90px">{{ __('expenses.amount') }} ({{ $company['currency'] }})</th>
                        <th class="right" style="width:30px">%</th>
                        <th class="center" style="width:30px">Nb</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $catIdx = 0;
                        $maxCatTotal = $byCategory->first()['total'];
                    @endphp
                    @foreach($byCategory as $cat)
                        @php
                            $color = $categoryColors[$catIdx % count($categoryColors)];
                            $barPct = $maxCatTotal > 0 ? ($cat['total'] / $maxCatTotal) * 100 : 0;
                            $catIdx++;
                        @endphp
                        <tr>
                            <td><span class="dot" style="background:{{ $color }}"></span> {{ $cat['name'] }}</td>
                            <td class="bar-cell">
                                <div class="bar-bg">
                                    <div class="bar-fill" style="width: {{ max($barPct, 1) }}%; background: {{ $color }}"></div>
                                </div>
                            </td>
                            <td class="right" style="font-weight:bold;">{{ formatMoney($cat['total']) }}</td>
                            <td class="right">{{ $cat['percentage'] }}%</td>
                            <td class="center">{{ $cat['count'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td>{{ __('reports.total') }}</td>
                        <td></td>
                        <td class="right">{{ formatMoney($total) }} {{ $company['currency'] }}</td>
                        <td class="right">100%</td>
                        <td class="center">{{ $expenses->count() }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif

    <!-- EMPLOYEE BREAKDOWN -->
    @php
        $visibleEmployees = $byEmployee->filter(fn($e) => $e['name'] !== __('expenses.no_employee'));
    @endphp
    @if($visibleEmployees->count() > 1)
        <div class="section">
            <h2>{{ __('reports.by_employee') }} <span class="badge">{{ $visibleEmployees->count() }}</span></h2>
            <table class="data">
                <thead>
                    <tr>
                        <th>{{ __('employees.name') }}</th>
                        <th>{{ __('reports.distribution') }}</th>
                        <th class="right" style="width:90px">{{ __('expenses.amount') }} ({{ $company['currency'] }})</th>
                        <th class="right" style="width:30px">%</th>
                        <th class="center" style="width:30px">Nb</th>
                    </tr>
                </thead>
                <tbody>
                    @php $maxEmpTotal = $visibleEmployees->first()['total']; @endphp
                    @foreach($visibleEmployees as $emp)
                        @php $barPct = $maxEmpTotal > 0 ? ($emp['total'] / $maxEmpTotal) * 100 : 0; @endphp
                        <tr>
                            <td>{{ $emp['name'] }}</td>
                            <td class="bar-cell">
                                @if($barPct > 0)
                                    <div class="bar-bg">
                                        <div class="bar-fill" style="width: {{ max($barPct, 1) }}%; background: #14b8a6;"></div>
                                    </div>
                                @endif
                            </td>
                            <td class="right" style="font-weight:bold;">{{ formatMoney($emp['total']) }}</td>
                            <td class="right">{{ $emp['percentage'] }}%</td>
                            <td class="center">{{ $emp['count'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- PAYMENT METHODS -->
    @if(count($byPaymentMethod) > 0)
        <div class="section">
            <h2>{{ __('reports.payment_methods') }}</h2>
            <table class="data">
                <thead>
                    <tr>
                        <th>{{ __('expenses.payment_method') }}</th>
                        <th>{{ __('reports.distribution') }}</th>
                        <th class="right" style="width:90px">{{ __('expenses.amount') }} ({{ $company['currency'] }})</th>
                        <th class="center" style="width:30px">Nb</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $pmColors = ['#10b981','#3b82f6','#f59e0b','#8b5cf6','#94a3b8'];
                        $pmIdx = 0;
                        $maxPmTotal = collect($byPaymentMethod)->max('total');
                    @endphp
                    @foreach($byPaymentMethod as $method => $pmData)
                        @php
                            $pmColor = $pmColors[$pmIdx % count($pmColors)];
                            $pmBarPct = $maxPmTotal > 0 ? ($pmData['total'] / $maxPmTotal) * 100 : 0;
                            $pmIdx++;
                        @endphp
                        <tr>
                            <td><span class="dot" style="background:{{ $pmColor }}"></span> {{ __("payment_methods.{$method}") }}</td>
                            <td class="bar-cell">
                                @if($pmBarPct > 0)
                                    <div class="bar-bg">
                                        <div class="bar-fill" style="width: {{ max($pmBarPct, 1) }}%; background: {{ $pmColor }}"></div>
                                    </div>
                                @endif
                            </td>
                            <td class="right" style="font-weight:bold;">{{ formatMoney($pmData['total']) }}</td>
                            <td class="center">{{ $pmData['count'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- BUDGET VS ACTUAL -->
    @if($budgetComparison->count() > 0)
        <div class="section">
            <h2>{{ __('reports.budget_vs_actual') }} <span class="badge">{{ $budgetComparison->count() }}</span></h2>
            <table class="data">
                <thead>
                    <tr>
                        <th>{{ __('expenses.category') }}</th>
                        <th class="right">{{ __('reports.budget') }} ({{ $company['currency'] }})</th>
                        <th class="right">{{ __('reports.actual') }} ({{ $company['currency'] }})</th>
                        <th class="right">{{ __('reports.budget_used') }}</th>
                        <th class="right">{{ __('reports.variance') }} ({{ $company['currency'] }})</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($budgetComparison as $bc)
                        @php
                            $usagePct = $bc['budget'] > 0 ? round(($bc['actual'] / $bc['budget']) * 100, 0) : 0;
                            $usageColor = $usagePct > 100 ? '#dc2626' : ($usagePct > 80 ? '#f59e0b' : '#059669');
                        @endphp
                        <tr>
                            <td>{{ $bc['name'] }}</td>
                            <td class="right">{{ formatMoney($bc['budget']) }}</td>
                            <td class="right">{{ formatMoney($bc['actual']) }}</td>
                            <td class="right" style="color:{{ $usageColor }}; font-weight:bold;">{{ $usagePct }}%</td>
                            <td class="right {{ $bc['variance'] <= 0 ? 'variance-pos' : 'variance-neg' }}">
                                {{ $bc['variance'] >= 0 ? '+' : '' }}{{ formatMoney($bc['variance']) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- TOP 10 EXPENSES -->
    @if($topExpenses->count() > 0)
        <div class="section">
            <h2>{{ __('reports.top_expenses') }} <span class="badge">{{ $topExpenses->count() }}</span></h2>
            <table class="data">
                <thead>
                    <tr>
                        <th style="width:24px">#</th>
                        <th style="width:60px">{{ __('expenses.date') }}</th>
                        <th>{{ __('expenses.description') }}</th>
                        <th>{{ __('expenses.category') }}</th>
                        <th class="right" style="width:90px">{{ __('expenses.amount') }} ({{ $company['currency'] }})</th>
                        <th class="right" style="width:40px">{{ __('reports.of_total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topExpenses as $e)
                        @php
                            $pctOfTotal = $total > 0 ? round(($e->amount / $total) * 100, 1) : 0;
                            $rankClass = $loop->iteration == 1 ? 'rank-1' : ($loop->iteration == 2 ? 'rank-2' : ($loop->iteration == 3 ? 'rank-3' : 'rank-default'));
                        @endphp
                        <tr>
                            <td><span class="rank-badge {{ $rankClass }}">{{ $loop->iteration }}</span></td>
                            <td>{{ $e->date->format('d/m/Y') }}</td>
                            <td>{{ $e->description }}</td>
                            <td>{{ $e->category?->parent?->translated_name ? $e->category->parent->translated_name . ' > ' . $e->category->translated_name : ($e->category?->translated_name ?? $e->category_key) }}</td>
                            <td class="right" style="font-weight:bold;">{{ formatMoney($e->amount) }}</td>
                            <td class="right">{{ $pctOfTotal }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- ALL EXPENSES DETAILED TABLE -->
    <div class="section">
        <h2>{{ __('expenses.list') }} <span class="badge">{{ $expenses->count() }} {{ __('reports.operations') }}</span></h2>
        <table class="data">
            <thead>
                <tr>
                    <th style="width:60px">{{ __('expenses.date') }}</th>
                    <th>{{ __('expenses.description') }}</th>
                    <th>{{ __('expenses.category') }}</th>
                    <th>{{ __('expenses.employee') }}</th>
                    <th>{{ __('expenses.payment_method') }}</th>
                    <th class="right" style="width:85px">{{ __('expenses.amount') }} ({{ $company['currency'] }})</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                    <tr>
                        <td>{{ $expense->date->format('d/m/Y') }}</td>
                        <td>{{ $expense->description }}</td>
                        <td>{{ $expense->category && $expense->category->parent ? $expense->category->parent->translated_name . ' > ' . $expense->category->translated_name : ($expense->category?->translated_name ?? $expense->category_key) }}</td>
                        <td>{{ $expense->employee?->name ?? '-' }}</td>
                        <td>{{ __("payment_methods.{$expense->payment_method}") }}</td>
                        <td class="right" style="font-weight:bold;">{{ formatMoney($expense->amount) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center; padding:15px;">{{ __('reports.no_data') }}</td></tr>
                @endforelse
            </tbody>
            @if($expenses->count() > 0)
                <tfoot>
                    <tr>
                        <td colspan="5" style="text-align:right;">{{ __('reports.total') }}</td>
                        <td class="right">{{ formatMoney($total) }} {{ $company['currency'] }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

    <!-- NOTES & EXPENSES WITH NOTES -->
    @php $expensesWithNotes = $expenses->filter(fn($e) => !empty($e->notes)); @endphp
    @if($expensesWithNotes->count() > 0)
        <div class="section">
            <h2>{{ __('expenses.notes') }} <span class="badge">{{ $expensesWithNotes->count() }}</span></h2>
            <table class="data">
                <thead>
                    <tr>
                        <th style="width:60px">{{ __('expenses.date') }}</th>
                        <th>{{ __('expenses.description') }}</th>
                        <th>{{ __('expenses.notes') }}</th>
                        <th class="right" style="width:85px">{{ __('expenses.amount') }} ({{ $company['currency'] }})</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expensesWithNotes as $e)
                        <tr>
                            <td>{{ $e->date->format('d/m/Y') }}</td>
                            <td>{{ $e->description }}</td>
                            <td style="font-style:italic;color:#64748b;">{{ $e->notes }}</td>
                            <td class="right" style="font-weight:bold;">{{ formatMoney($e->amount) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="footer">
        <span>{{ $company['name'] }} | {{ __('reports.generated_at') }} {{ now()->format('d/m/Y H:i') }} | </span>
        <span class="page-number"></span>
    </div>

</body>
</html>