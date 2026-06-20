<div class="max-w-7xl mx-auto space-y-8 animate-fade-in">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
        <div>
            <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-700 to-indigo-600 dark:from-blue-400 dark:to-indigo-300 tracking-tight font-heading">
                {{ __('nav.statistics') }}
            </h1>
            <p class="text-slate-500 dark:text-slate-500 text-sm mt-1.5 font-medium">{{ __('statistics.subtitle') }}</p>
        </div>
        <div class="w-full md:w-72">
            <div x-data="{ open: false, yr: 0, positionPopup(container) { const popup = container.querySelector('.period-popup'); const btn = container.querySelector('button'); if (!popup || !btn) return; const r = btn.getBoundingClientRect(); popup.style.position = 'fixed'; popup.style.top = (r.bottom + 8) + 'px'; popup.style.left = r.left + 'px'; popup.style.minWidth = Math.max(280, r.width) + 'px'; popup.style.zIndex = '9999'; } }"
                 x-init="yr = parseInt($wire.period.split('-')[0]) || (new Date).getFullYear()"
                 class="relative">
                <button type="button" @click="open = !open; if (open) { const c = $el.parentElement; $nextTick(() => positionPopup(c)) }"
                        class="w-full flex items-center gap-3 px-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl font-semibold dark:text-white cursor-pointer text-left hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors shadow-sm">
                    <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>{{ formatPeriodLabel($period) }}</span>
                </button>
                <div x-show="open"
                     @click.outside="open = false"
                     class="period-popup bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 p-4"
                     style="display:none">
                    <div class="flex items-center justify-between mb-3">
                        <button type="button" @click="yr--" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <span class="font-bold text-slate-800 dark:text-white text-sm" x-text="yr"></span>
                        <button type="button" @click="yr++" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-3 gap-2 min-w-[240px]">
                        @foreach (['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'] as $num)
                            <button type="button"
                                    @click="
                                        $wire.set('period', yr + '-' + '{{ $num }}');
                                        open = false;
                                    "
                                    :class="$wire.period === (yr + '-' + '{{ $num }}') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50'"
                                    class="px-2 py-2.5 rounded-lg text-sm font-medium transition-colors">
                                {{ Carbon\Carbon::createFromFormat('!m', $num)->translatedFormat('M') }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="relative overflow-hidden bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-5 text-white shadow-lg">
            <div class="absolute top-0 right-0 -mr-6 -mt-6 w-20 h-20 rounded-full bg-white opacity-10 blur-2xl"></div>
            <p class="text-blue-100 text-xs font-bold uppercase tracking-wider mb-1">{{ __('statistics.expenses') }}</p>
            <h3 class="text-2xl font-black">{{ formatMoney($totalExpenses) }}</h3>
            <div class="flex items-center gap-2 mt-2">
                <span class="text-[11px] text-blue-200 font-semibold">{{ $expenseCount }} {{ __('statistics.operations') }}</span>
                <span class="text-[11px] text-blue-200 font-semibold">- {{ __('statistics.avg_day') }} {{ formatMoney($averagePerDay) }}</span>
            </div>
        </div>
        <div class="relative overflow-hidden bg-gradient-to-br from-emerald-600 to-teal-600 rounded-2xl p-5 text-white shadow-lg">
            <div class="absolute top-0 right-0 -mr-6 -mt-6 w-20 h-20 rounded-full bg-white opacity-10 blur-2xl"></div>
            <p class="text-emerald-100 text-xs font-bold uppercase tracking-wider mb-1">{{ __('statistics.gains') }}</p>
            <h3 class="text-2xl font-black">{{ formatMoney($totalGains) }}</h3>
            <div class="mt-2">
                <span class="text-[11px] text-emerald-200 font-semibold">{{ __('statistics.balance') }}: {{ formatMoney($balance) }}</span>
            </div>
        </div>
        <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200/50 dark:border-slate-800/60 rounded-2xl p-5 shadow-lg">
            <p class="text-slate-400 dark:text-slate-500 text-xs font-bold uppercase tracking-wider mb-1">{{ __('statistics.prev_period') }}</p>
            <h3 class="text-2xl font-black text-slate-800 dark:text-white">{{ formatMoney($previousExpenses) }}</h3>
            @if($previousExpenses > 0)
            <div class="flex items-center gap-1.5 mt-2">
                @if($expensesChange > 0)
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    <span class="text-rose-500 text-xs font-bold">+{{ $expensesChange }}%</span>
                    <span class="text-slate-400 dark:text-slate-500 text-[11px]">{{ __('statistics.vs_previous') }}</span>
                @elseif($expensesChange < 0)
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                    <span class="text-emerald-500 text-xs font-bold">{{ $expensesChange }}%</span>
                    <span class="text-slate-400 dark:text-slate-500 text-[11px]">{{ __('statistics.vs_previous') }}</span>
                @else
                    <span class="text-slate-400 dark:text-slate-500 text-xs font-bold">0%</span>
                    <span class="text-slate-400 dark:text-slate-500 text-[11px]">{{ __('statistics.vs_previous') }}</span>
                @endif
            </div>
            @endif
        </div>
        <div class="relative bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200/50 dark:border-slate-800/60 rounded-2xl p-5 shadow-lg overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-indigo-50 dark:bg-indigo-500/5 rounded-full blur-2xl -mr-6 -mt-6"></div>
            <p class="text-slate-400 dark:text-slate-500 text-xs font-bold uppercase tracking-wider mb-1">{{ __('statistics.period_label') }}</p>
            <h3 class="text-lg font-black text-slate-800 dark:text-white leading-tight">{{ formatPeriodLabel($period) }}</h3>
            <div class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-1">
                <span class="inline-flex items-center gap-1 text-[11px] text-slate-500 dark:text-slate-400 font-semibold bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-lg">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    {{ __('statistics.avg_day') }} {{ formatMoney($averagePerDay) }}
                </span>
                <span class="inline-flex items-center gap-1 text-[11px] text-slate-500 dark:text-slate-400 font-semibold bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-lg">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    {{ $expenseCount }} {{ __('statistics.operations') }}
                </span>
            </div>
            @if(count($expensesByCategory) > 0)
            @php $topCat = $expensesByCategory[0]; @endphp
            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800/40">
                <div class="flex items-center justify-between text-[11px]">
                    <span class="text-slate-400 dark:text-slate-500 font-medium">{{ __('statistics.top_category') }}</span>
                    <span class="font-bold text-slate-700 dark:text-slate-300 truncate max-w-[140px]">{{ $topCat['label'] }}</span>
                    <span class="font-bold text-slate-800 dark:text-slate-100">{{ $topCat['pct'] }}%</span>
                </div>
                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 mt-1.5 overflow-hidden">
                    <div class="bg-indigo-500 h-full rounded-full" style="width: {{ $topCat['pct'] }}%"></div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Today's Expenses -->
    <a href="{{ route('expenses.index', ['searchDateFrom' => today()->format('Y-m-d'), 'searchDateTo' => today()->format('Y-m-d')]) }}" class="relative overflow-hidden block bg-gradient-to-br from-amber-500 to-orange-600 rounded-3xl p-6 shadow-premium dark:shadow-premium-dark text-white transition-all duration-300 hover:-translate-y-0.5 hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover group cursor-pointer">
        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-white opacity-10 blur-2xl group-hover:scale-110 transition-transform duration-500"></div>
        <div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-24 h-24 rounded-full bg-amber-400 opacity-20 blur-xl"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-white/15 rounded-2xl backdrop-blur-md border border-white/10">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-amber-100 font-bold text-xs uppercase tracking-wider mb-1">{{ __('dashboard.today') }}</p>
                    <h3 class="text-3xl font-black tracking-tight font-heading">{{ formatMoney($dailyTotal) }}</h3>
                </div>
            </div>
            <p class="text-amber-100/90 font-semibold text-xs flex items-center gap-1.5">
                <span class="bg-white/20 px-2 py-0.5 rounded">{{ getCurrency() }}</span>
                &bull;
                <span>{{ __('dashboard.daily_total') }}</span>
            </p>
        </div>
    </a>

    <!-- Cash Deficit -->
    @if($cashDeficit > 0 && auth()->user()->hasPermission('view-deficit'))
    <div class="relative overflow-hidden bg-gradient-to-br from-red-600 to-rose-700 rounded-3xl p-6 shadow-premium dark:shadow-premium-dark text-white transition-all duration-300 hover:-translate-y-0.5 hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover group">
        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-white opacity-10 blur-2xl group-hover:scale-110 transition-transform duration-500"></div>
        <div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-24 h-24 rounded-full bg-red-400 opacity-20 blur-xl"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-white/15 rounded-2xl backdrop-blur-md border border-white/10">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-red-100 font-bold text-xs uppercase tracking-wider mb-1">{{ __('settings.cash_deficit') }}</p>
                    <h3 class="text-3xl font-black tracking-tight font-heading">{{ formatMoney($cashDeficit) }}</h3>
                </div>
            </div>
            <p class="text-red-100/90 font-semibold text-xs flex items-center gap-1.5">
                <span class="bg-white/20 px-2 py-0.5 rounded">{{ getCurrency() }}</span>
                &bull;
                <span>{{ __('dashboard.deficit_desc') }}</span>
            </p>
        </div>
    </div>
    @endif

    <!-- Row 1: Category Chart + Payment Methods -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Category Doughnut + Table -->
        <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 p-6 md:p-8 hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
            <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-6 flex items-center font-heading">
                <span class="w-2.5 h-6 bg-blue-500 rounded-full me-3"></span>
                {{ __('statistics.by_category') }}
            </h2>
            @if(count($expensesByCategory) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div wire:key="cat-chart" class="relative h-64 flex items-center justify-center">
                        <canvas id="catChart"></canvas>
                    </div>
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @foreach($expensesByCategory as $i => $cat)
                        <div class="flex items-center justify-between p-2.5 bg-slate-50/50 dark:bg-slate-950/30 rounded-xl border border-slate-100/50 dark:border-slate-800/40 hover:bg-slate-100 dark:hover:bg-slate-800/50 transition-colors">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="flex items-center justify-center w-5 h-5 rounded-full text-[10px] font-extrabold {{ $i === 0 ? 'bg-yellow-400 text-yellow-900' : ($i === 1 ? 'bg-slate-300 text-slate-700' : ($i === 2 ? 'bg-amber-700 text-amber-100' : 'bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400')) }}" style="{{ $i >= 3 ? '' : '' }}">
                                    {{ $i + 1 }}
                                </span>
                                <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background: {{ $cat['color'] }}"></span>
                                <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 truncate">{{ $cat['label'] }}</span>
                            </div>
                            <div class="text-right shrink-0 ml-3">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-100 block">{{ formatMoney($cat['total']) }}</span>
                                <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">{{ $cat['pct'] }}% ({{ $cat['count'] }})</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @if(count($expensesByCategory) > 0)
                    <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800/40 text-center">
                        <span class="text-[11px] text-slate-400 dark:text-slate-500 font-medium">
                            {{ __('statistics.total') }}: <strong class="text-slate-700 dark:text-slate-300">{{ formatMoney($totalExpenses) }}</strong>
                            &middot; {{ $expenseCount }} {{ __('statistics.operations') }}
                        </span>
                    </div>
                    @endif
                </div>
            @else
                <div class="h-64 flex flex-col items-center justify-center border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl">
                    <p class="text-slate-400 dark:text-slate-500 font-semibold text-sm">{{ __('statistics.no_data') }}</p>
                </div>
            @endif
        </div>

        <!-- Payment Methods -->
        <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 p-6 md:p-8 hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
            <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-6 flex items-center font-heading">
                <span class="w-2.5 h-6 bg-emerald-500 rounded-full me-3"></span>
                {{ __('statistics.payment_methods') }}
            </h2>
            @if(count($paymentMethodData) > 0)
                @php
                    $pmColors = ['cash' => '#10B981', 'card' => '#6366F1', 'check' => '#F59E0B', 'transfer' => '#3B82F6', 'other' => '#8B5CF6'];
                @endphp
                <div class="space-y-4">
                    @foreach($paymentMethodData as $pm)
                    @php $color = $pmColors[$pm['label']] ?? '#64748b'; @endphp
                    <div class="bg-slate-50/40 dark:bg-slate-950/30 rounded-xl p-3 border border-slate-100/50 dark:border-slate-800/40">
                        <div class="flex justify-between items-center mb-2">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full" style="background: {{ $color }}"></span>
                                <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">{{ $pm['label'] }}</span>
                            </div>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ formatMoney($pm['total']) }}</span>
                        </div>
                        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500" style="width: {{ $pm['pct'] }}%; background: {{ $color }}"></div>
                        </div>
                        <div class="flex justify-between mt-1">
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">{{ $pm['pct'] }}%</span>
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">{{ $pm['count'] }} {{ __('statistics.operations') }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="h-64 flex flex-col items-center justify-center border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl">
                    <p class="text-slate-400 dark:text-slate-500 font-semibold text-sm">{{ __('statistics.no_data') }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Row 2: Trend Chart + Period Expenses Table -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Trend -->
        <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 p-6 md:p-8 hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
            <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-6 flex items-center font-heading">
                <span class="w-2.5 h-6 bg-indigo-500 rounded-full me-3"></span>
                {{ __('statistics.trend') }}
            </h2>
            @if(count($monthlyTrend) > 0)
                <div wire:key="trend-chart-stat" class="relative h-72 w-full">
                    <canvas id="trendChartStat"></canvas>
                </div>
            @else
                <div class="h-72 flex flex-col items-center justify-center border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl">
                    <p class="text-slate-400 dark:text-slate-500 font-semibold text-sm">{{ __('statistics.no_data') }}</p>
                </div>
            @endif
        </div>

        <!-- Category Table -->
        <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 p-6 md:p-8 overflow-hidden hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
            <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center font-heading">
                <span class="w-2.5 h-6 bg-amber-500 rounded-full me-3"></span>
                {{ __('statistics.category_detail') }}
            </h2>
            @if(count($expensesByCategory) > 0)
            <div class="overflow-x-auto -mx-6 md:-mx-8">
                <table class="min-w-full text-sm text-start {{ session('locale', 'ar') === 'ar' ? 'rtl:text-right' : 'ltr:text-left' }}">
                    <thead class="text-slate-400 dark:text-slate-500 font-bold text-xs uppercase tracking-wider border-b border-slate-100 dark:border-slate-800/60">
                        <tr>
                            <th class="py-3 px-6">{{ __('expenses.category') }}</th>
                            <th class="py-3 px-6 text-right">{{ __('expenses.amount') }}</th>
                            <th class="py-3 px-6 text-center">{{ __('statistics.percent') }}</th>
                            <th class="py-3 px-6 text-center">{{ __('statistics.count') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                        @foreach($expensesByCategory as $cat)
                        <tr class="hover:bg-slate-50/30 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="py-3 px-6">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background: {{ $cat['color'] }}"></span>
                                    <span class="font-semibold text-slate-700 dark:text-slate-300 text-xs">{{ $cat['label'] }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-6 text-right font-bold text-slate-800 dark:text-slate-200 whitespace-nowrap">{{ formatMoney($cat['total']) }}</td>
                            <td class="py-3 px-6 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400">{{ $cat['pct'] }}%</span>
                            </td>
                            <td class="py-3 px-6 text-center font-semibold text-slate-500 dark:text-slate-400">{{ $cat['count'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t-2 border-slate-200 dark:border-slate-700">
                        <tr>
                            <td class="py-3 px-6 font-bold text-slate-800 dark:text-slate-200 text-xs">{{ __('statistics.total') }}</td>
                            <td class="py-3 px-6 text-right font-black text-slate-900 dark:text-white whitespace-nowrap">{{ formatMoney($totalExpenses) }}</td>
                            <td class="py-3 px-6 text-center font-bold text-slate-500 dark:text-slate-400">100%</td>
                            <td class="py-3 px-6 text-center font-bold text-slate-500 dark:text-slate-400">{{ $expenseCount }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
                <div class="h-64 flex flex-col items-center justify-center border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl">
                    <p class="text-slate-400 dark:text-slate-500 font-semibold text-sm">{{ __('statistics.no_data') }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Expenses List for this Period -->
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 overflow-hidden hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800/60 bg-slate-50/20 dark:bg-slate-950/20">
            <h2 class="text-lg font-bold text-slate-800 dark:text-white flex items-center font-heading">
                <svg class="w-5 h-5 text-blue-500 me-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                {{ __('statistics.period_expenses') }}
            </h2>
        </div>
        @if($expenses->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-start {{ session('locale', 'ar') === 'ar' ? 'rtl:text-right' : 'ltr:text-left' }}">
                    <thead class="bg-slate-50/50 dark:bg-slate-950/30 text-slate-400 dark:text-slate-500 font-bold text-xs uppercase tracking-wider border-b border-slate-100 dark:border-slate-800/60">
                        <tr>
                            <th class="py-3 px-6">{{ __('expenses.date') }}</th>
                            <th class="py-3 px-6">{{ __('expenses.description') }}</th>
                            <th class="py-3 px-6">{{ __('expenses.category') }}</th>
                            <th class="py-3 px-6 hidden md:table-cell">{{ __('expenses.payment_method') }}</th>
                            <th class="py-3 px-6 hidden md:table-cell">{{ __('expenses.employee') }}</th>
                            <th class="py-3 px-6 text-right">{{ __('expenses.amount') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                        @foreach($expenses as $expense)
                        <tr class="hover:bg-blue-50/20 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 whitespace-nowrap">{{ $expense->date->format('d/m/Y') }}</td>
                            <td class="py-3 px-6 font-semibold text-slate-700 dark:text-slate-300 max-w-[200px] truncate">{{ $expense->description }}</td>
                            <td class="py-3 px-6">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 border border-blue-100/30 dark:border-blue-800/30">
                                    {{ $expense->category?->translated_name ?? $expense->category_key }}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 hidden md:table-cell">{{ __('payment_methods.' . $expense->payment_method) }}</td>
                            <td class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 hidden md:table-cell">{{ $expense->employee?->name ?? '-' }}</td>
                            <td class="py-3 px-6 text-right font-bold text-slate-900 dark:text-white whitespace-nowrap">{{ formatMoney($expense->amount) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($expenses->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800/50 bg-slate-50/30 dark:bg-slate-900/30">
                {{ $expenses->links() }}
            </div>
            @endif
        @else
            <div class="p-12 flex flex-col items-center justify-center">
                <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900/50 rounded-full flex items-center justify-center mb-4 border border-slate-200/50 dark:border-slate-800/60">
                    <svg class="w-8 h-8 text-slate-300 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                </div>
                <p class="text-slate-500 dark:text-slate-400 font-semibold text-sm">{{ __('statistics.no_expenses_period') }}</p>
            </div>
        @endif
    </div>

    <!-- Closure History -->
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 overflow-hidden hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800/60 bg-slate-50/20 dark:bg-slate-950/20">
            <h2 class="text-lg font-bold text-slate-800 dark:text-white flex items-center font-heading">
                <svg class="w-5 h-5 text-emerald-500 me-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ __('statistics.closures') }}
            </h2>
        </div>
        @if(count($closureHistory) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-6">
                @foreach($closureHistory as $c)
                @php
                    $gainsPct = $c['gains'] > 0 ? round($c['gains'] / ($c['gains'] + $c['expenses']) * 100, 1) : 0;
                    $expPct = $c['expenses'] > 0 ? round($c['expenses'] / ($c['gains'] + $c['expenses']) * 100, 1) : 0;
                @endphp
                <div class="bg-slate-50/50 dark:bg-slate-950/30 rounded-2xl border border-slate-200/50 dark:border-slate-800/40 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <span class="font-bold text-slate-800 dark:text-slate-200 text-sm">{{ $c['label'] }}</span>
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">{{ $c['date'] }}</span>
                    </div>
                    <div class="flex items-center gap-3 mb-3">
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ __('caisse.closed_by') }}:</span>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $c['closed_by'] }}</span>
                    </div>
                    <div class="space-y-2 mb-3">
                        <div class="flex justify-between text-xs">
                            <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ __('caisse.gains') }}: {{ formatMoney($c['gains']) }}</span>
                            <span class="text-slate-400">{{ $gainsPct }}%</span>
                        </div>
                        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2 overflow-hidden">
                            <div class="bg-emerald-500 h-full rounded-full" style="width: {{ $gainsPct }}%"></div>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="font-semibold text-rose-500">{{ __('caisse.expenses') }}: {{ formatMoney($c['expenses']) }}</span>
                            <span class="text-slate-400">{{ $expPct }}%</span>
                        </div>
                        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2 overflow-hidden">
                            <div class="bg-rose-500 h-full rounded-full" style="width: {{ $expPct }}%"></div>
                        </div>
                    </div>
                    <div class="pt-3 border-t border-slate-200/50 dark:border-slate-800/40 flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400">{{ __('caisse.balance') }}:</span>
                        <span class="text-sm font-extrabold {{ $c['balance'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                            @if($c['balance'] >= 0)
                            <svg class="w-4 h-4 inline me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            @else
                            <svg class="w-4 h-4 inline me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                            @endif
                            {{ formatMoney($c['balance']) }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="p-12 flex flex-col items-center justify-center">
                <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900/50 rounded-full flex items-center justify-center mb-4 border border-slate-200/50 dark:border-slate-800/60">
                    <svg class="w-8 h-8 text-slate-300 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
                <p class="text-slate-500 dark:text-slate-400 font-semibold text-sm">{{ __('statistics.no_closures') }}</p>
            </div>
        @endif
    </div>
</div>

    <!-- Deficit History -->
    @if(auth()->user()->hasPermission('view-deficit') && count($deficitHistory) > 0)
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 overflow-hidden hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800/60 bg-slate-50/20 dark:bg-slate-950/20">
            <h2 class="text-lg font-bold text-slate-800 dark:text-white flex items-center font-heading">
                <svg class="w-5 h-5 text-red-500 me-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ __('statistics.deficit_history') }}
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-start {{ session('locale', 'ar') === 'ar' ? 'rtl:text-right' : 'ltr:text-left' }}">
                <thead class="bg-slate-50/50 dark:bg-slate-950/30 text-slate-500 dark:text-slate-500 font-bold text-xs uppercase tracking-wider border-b border-slate-100 dark:border-slate-800/60">
                    <tr>
                        <th class="py-4 px-6">{{ __('caisse.month') }}</th>
                        <th class="py-4 px-6 text-center" style="width:80px">{{ __('common.type') }}</th>
                        <th class="py-4 px-6 text-right">{{ __('expenses.amount') }}</th>
                        <th class="py-4 px-6 text-center">{{ __('statistics.remaining') }}</th>
                        <th class="py-4 px-6 text-center">{{ __('caisse.close_date') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                    @foreach($deficitHistory as $d)
                    @php
                        $maxAmount = collect($deficitHistory)->pluck('amount')->max() ?: 1;
                        $barWidth = ($d['amount'] / $maxAmount) * 100;
                    @endphp
                    <tr class="hover:bg-slate-50/30 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="py-4 px-6 font-bold text-slate-800 dark:text-slate-200 text-xs">{{ $d['month'] }}</td>
                        <td class="py-4 px-6 text-center">
                            @if($d['type'] === 'deficit_increased')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-full text-xs font-bold border border-red-100/30 dark:border-red-800/30">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                    {{ __('statistics.increase') }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 rounded-full text-xs font-bold border border-amber-100/30 dark:border-amber-800/30">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                    {{ __('statistics.deduction') }}
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex flex-col items-end">
                                <span class="font-bold whitespace-nowrap {{ $d['type'] === 'deficit_increased' ? 'text-red-600 dark:text-red-400' : 'text-amber-600 dark:text-amber-400' }}">{{ formatMoney($d['amount']) }}</span>
                                <div class="w-24 bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 mt-1 overflow-hidden">
                                    <div class="{{ $d['type'] === 'deficit_increased' ? 'bg-red-500' : 'bg-amber-500' }} h-full rounded-full" style="width: {{ $barWidth }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $d['remaining'] > 0 ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-100/30 dark:border-red-800/30' : 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 border border-emerald-100/30 dark:border-emerald-800/30' }}">
                                {{ formatMoney($d['remaining']) }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-center font-semibold text-slate-400 dark:text-slate-500 text-xs">{{ $d['date'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4" defer></script>
<script>
let statCharts = {};

function destroyStatCharts() {
    Object.values(statCharts).forEach(chart => { if (chart) chart.destroy(); });
    statCharts = {};
}

function initStatCharts() {
    destroyStatCharts();

    Chart.defaults.font.family = "'Inter', 'Outfit', system-ui, sans-serif";
    Chart.defaults.color = '#64748b';

    @if(count($expensesByCategory) > 0)
    const isMobile = window.innerWidth < 640;
    statCharts.category = new Chart(document.getElementById('catChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_column($expensesByCategory, 'label'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!},
            datasets: [{
                data: {!! json_encode(array_column($expensesByCategory, 'total'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!},
                backgroundColor: {!! json_encode(array_column($expensesByCategory, 'color'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!},
                borderWidth: 0,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    titleColor: '#1e293b',
                    bodyColor: '#334155',
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    padding: 12,
                    boxPadding: 6,
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const pct = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                            return context.label + ': ' + new Intl.NumberFormat().format(context.parsed) + ' {{ getCurrency() }} (' + pct + '%)';
                        }
                    }
                }
            }
        }
    });
    @endif

    @if(count($monthlyTrend) > 0)
    let ctx = document.getElementById('trendChartStat').getContext('2d');
    let gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(99, 102, 241, 0.5)');
    gradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

    statCharts.trend = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($monthlyTrend, 'month'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!},
            datasets: [{
                label: '{{ __('statistics.expenses') }}',
                data: {!! json_encode(array_column($monthlyTrend, 'total'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!},
                borderColor: '#6366F1',
                borderWidth: 3,
                backgroundColor: gradient,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#6366F1',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleColor: '#fff',
                    bodyColor: '#cbd5e1',
                    padding: 12,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return new Intl.NumberFormat().format(context.parsed.y) + ' {{ getCurrency() }}';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9', drawBorder: false },
                    border: { display: false }
                },
                x: {
                    grid: { display: false },
                    border: { display: false }
                }
            }
        }
    });
    @endif
}

document.addEventListener('DOMContentLoaded', initStatCharts);
document.addEventListener('livewire:init', function() {
    Livewire.hook('morph.updated', initStatCharts);
});
</script>
@endpush
