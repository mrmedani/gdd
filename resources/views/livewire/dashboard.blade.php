<div wire:poll.60s="loadUnreadCount"
     x-data="{ showAlerts: false, _alerts: @entangle('showAlertsModal') }"
     x-init="$watch('_alerts', v => { if (v !== undefined) showAlerts = v })">
<div class="space-y-8 animate-fade-in">
    <div class="flex justify-between items-center mb-2">
        <div>
            <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-700 to-indigo-600 dark:from-blue-400 dark:to-indigo-300 tracking-tight font-heading">{{ __('nav.dashboard') }}</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1.5 font-medium">{{ __('dashboard.subtitle') }}</p>
        </div>
        <div class="flex gap-2">
            @if($unreadAlerts > 0)
                <button @click="showAlerts = true" class="flex items-center px-4 py-2 bg-gradient-to-r from-red-500 to-rose-600 text-white rounded-full text-xs font-bold shadow-lg animate-alert-glow hover:from-red-600 hover:to-rose-700 transition-all cursor-pointer group">
                    <svg class="w-4.5 h-4.5 me-2 animate-alert-ring group-hover:animate-none group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    {{ __('dashboard.alerts') }}: {{ $unreadAlerts }}
                </button>
            @endif
        </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Monthly Total -->
        <a href="{{ route('expenses.index', ['searchDateFrom' => $periodStartDate, 'searchDateTo' => $periodEndDate]) }}" class="relative overflow-hidden block bg-gradient-to-br from-blue-600 to-indigo-700 rounded-3xl p-6 shadow-premium dark:shadow-premium-dark text-white transition-all duration-300 hover:-translate-y-1 hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover group cursor-pointer">
            <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-white opacity-10 blur-2xl group-hover:scale-110 transition-transform duration-500"></div>
            <div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-24 h-24 rounded-full bg-blue-400 opacity-20 blur-xl"></div>
            
            <div class="relative z-10 flex flex-col h-full justify-between">
                <div class="flex justify-between items-start mb-6">
                    <div class="p-3 bg-white/15 rounded-2xl backdrop-blur-md border border-white/10">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-blue-100 font-bold text-xs uppercase tracking-wider">{{ __('dashboard.monthly_total') }}</span>
                </div>
                <div>
                    <h3 class="text-3xl font-black tracking-tight font-heading">{{ formatMoney($monthlyTotal) }}</h3>
                    <p class="text-blue-100/90 font-semibold mt-1.5 text-xs flex items-center gap-1.5"><span class="bg-white/20 px-2 py-0.5 rounded">{{ getCurrency() }}</span> &bull; {{ $monthlyCount }} {{ __('dashboard.expenses_count') }}</p>
                </div>
            </div>
        </a>

        <!-- Today's Expenses -->
        <a href="{{ route('expenses.index', ['searchDateFrom' => today()->format('Y-m-d'), 'searchDateTo' => today()->format('Y-m-d')]) }}" class="relative overflow-hidden block bg-gradient-to-br from-amber-500 to-orange-600 rounded-3xl p-6 shadow-premium dark:shadow-premium-dark text-white transition-all duration-300 hover:-translate-y-1 hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover group cursor-pointer">
            <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-white opacity-10 blur-2xl group-hover:scale-110 transition-transform duration-500"></div>
            <div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-24 h-24 rounded-full bg-amber-400 opacity-20 blur-xl"></div>
            
            <div class="relative z-10 flex flex-col h-full justify-between">
                <div class="flex justify-between items-start mb-6">
                    <div class="p-3 bg-white/15 rounded-2xl backdrop-blur-md border border-white/10">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-amber-100 font-bold text-xs uppercase tracking-wider">{{ __('dashboard.today') }}</span>
                </div>
                <div>
                    <h3 class="text-3xl font-black tracking-tight font-heading">{{ formatMoney($dailyTotal) }}</h3>
                    <p class="text-amber-100/90 font-semibold mt-1.5 text-xs flex items-center gap-1.5"><span class="bg-white/20 px-2 py-0.5 rounded">{{ getCurrency() }}</span> &bull; {{ __('dashboard.daily_total') }}</p>
                </div>
            </div>
        </a>

        <!-- Daily Average -->
        <div class="relative overflow-hidden bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200/50 dark:border-slate-800/60 rounded-3xl p-6 shadow-premium dark:shadow-premium-dark transition-all duration-300 hover:-translate-y-1 hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-green-50 dark:bg-green-500/5 rounded-full blur-2xl group-hover:scale-120 transition-transform duration-500"></div>
            <div class="flex justify-between items-start mb-6">
                <div class="p-3 bg-green-50 dark:bg-green-500/10 text-green-600 dark:text-green-400 rounded-2xl border border-green-100/50 dark:border-green-500/20 group-hover:bg-green-600 group-hover:text-white transition-colors duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
                <span class="text-slate-400 dark:text-slate-500 font-bold text-xs uppercase tracking-wider">{{ __('dashboard.daily_average') }}</span>
            </div>
            <div>
                <h3 class="text-3xl font-black text-slate-800 dark:text-white font-heading">{{ formatMoney($averagePerDay) }} <span class="text-sm text-slate-400 font-bold">{{ getCurrency() }}</span></h3>
                <p class="text-green-600 dark:text-green-400 font-bold mt-1.5 text-xs flex items-center">
                    <svg class="w-4 h-4 me-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ __('dashboard.healthy_average') }}
                </p>
            </div>
        </div>

        <!-- Current Month -->
        <div class="relative overflow-hidden bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200/50 dark:border-slate-800/60 rounded-3xl p-6 shadow-premium dark:shadow-premium-dark transition-all duration-300 hover:-translate-y-1 hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-purple-50 dark:bg-purple-500/5 rounded-full blur-2xl group-hover:scale-120 transition-transform duration-500"></div>
            <div class="flex justify-between items-start mb-6">
                <div class="p-3 bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 rounded-2xl border border-purple-100/50 dark:border-purple-500/20 group-hover:bg-purple-600 group-hover:text-white transition-colors duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <span class="text-slate-400 dark:text-slate-500 font-bold text-xs uppercase tracking-wider">{{ __('dashboard.this_month') }}</span>
            </div>
            <div>
                <h3 class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-500 dark:from-purple-400 dark:to-pink-300 font-heading">{{ $currentPeriodLabel }}</h3>
                <p class="text-slate-400 dark:text-slate-400 font-semibold mt-1.5 text-xs flex items-center gap-1.5">
                    <span class="bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">{{ $remainingDays }} {{ __('dashboard.remaining_days') }}</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Cash Deficit -->
    @if($cashDeficit > 0 && auth()->user()->hasPermission('view-deficit'))
        <div class="relative overflow-hidden bg-gradient-to-br from-red-600 to-rose-700 rounded-3xl p-6 shadow-premium dark:shadow-premium-dark text-white transition-all duration-300 hover:-translate-y-1 hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover group">
            <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-white opacity-10 blur-2xl group-hover:scale-110 transition-transform duration-500"></div>
            <div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-24 h-24 rounded-full bg-rose-400 opacity-20 blur-xl"></div>
            <div class="relative z-10 flex items-center gap-5">
                <div class="p-4 bg-white/15 rounded-2xl backdrop-blur-md border border-white/10 shrink-0">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="flex-1">
                    <p class="text-red-100 font-bold text-xs uppercase tracking-wider mb-1">{{ __('settings.cash_deficit') }}</p>
                    <h3 class="text-3xl font-black tracking-tight font-heading">{{ formatMoney($cashDeficit) }}</h3>
                    <p class="text-red-100/90 font-semibold mt-1.5 text-xs flex items-center gap-1.5"><span class="bg-white/20 px-2 py-0.5 rounded">{{ getCurrency() }}</span> &bull; {{ __('dashboard.deficit_desc') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Admin Stats -->
    @if(auth()->user()->isAdmin())
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-gradient-to-r from-blue-50/50 to-indigo-50/20 dark:from-slate-900/60 dark:to-indigo-950/20 border border-blue-100/80 dark:border-slate-800/60 rounded-2xl p-5 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-500 text-white rounded-xl shadow-md shadow-blue-500/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider">{{ __('dashboard.total_users') }}</p>
                    <p class="text-2xl font-black text-blue-900 dark:text-white mt-1 font-heading">{{ $totalUsers }}</p>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-orange-50/50 to-amber-50/20 dark:from-slate-900/60 dark:to-amber-950/20 border border-orange-100/80 dark:border-slate-800/60 rounded-2xl p-5 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-orange-500 text-white rounded-xl shadow-md shadow-orange-500/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('dashboard.total_audit_logs') }}</p>
                    <p class="text-2xl font-black text-orange-900 dark:text-white mt-1 font-heading">{{ $totalAuditLogs }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 p-8 hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
            <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-6 flex items-center font-heading">
                <span class="w-2.5 h-6 bg-blue-500 rounded-full me-3"></span>
                {{ __('dashboard.by_category') }}
            </h2>
            @if(count($categoryData) > 0)
                <div wire:key="category-chart" class="relative h-64 w-full flex items-center justify-center">
                    <canvas id="categoryChart"></canvas>
                </div>
            @else
                <div wire:key="category-chart-empty" class="h-64 flex flex-col items-center justify-center border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl">
                    <p class="text-slate-400 dark:text-slate-500 font-semibold text-sm">{{ __('dashboard.no_data') }}</p>
                </div>
            @endif
        </div>

        @if(auth()->user()?->hasPermission('statistics'))
            <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 p-8 hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
                <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-6 flex items-center font-heading">
                    <span class="w-2.5 h-6 bg-purple-500 rounded-full me-3"></span>
                    {{ __('dashboard.monthly_trend') }}
                </h2>
                @if(count($monthlyTrend) > 0)
                    <div wire:key="trend-chart" class="relative h-64 w-full">
                        <canvas id="trendChart"></canvas>
                    </div>
                @else
                    <div wire:key="trend-chart-empty" class="h-64 flex flex-col items-center justify-center border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl">
                        <p class="text-slate-400 dark:text-slate-500 font-semibold text-sm">{{ __('dashboard.no_data') }}</p>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Recent Expenses Table -->
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 overflow-hidden hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
        <div class="flex justify-between items-center p-6 border-b border-slate-100 dark:border-slate-800/60 bg-slate-50/50 dark:bg-slate-950/20">
            <h2 class="text-lg font-bold text-slate-800 dark:text-white flex items-center font-heading">
                <svg class="w-5.5 h-5.5 text-blue-500 me-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                {{ __('dashboard.recent_expenses') }}
            </h2>
            <a href="{{ route('expenses.index') }}" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-slate-700/60 transition shadow-sm">
                {{ __('dashboard.view_all') }}
            </a>
        </div>
        @if(count($recentExpenses) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-start {{ session('locale', 'ar') === 'ar' ? 'rtl:text-right' : 'ltr:text-left' }}">
                    <thead class="bg-slate-50/40 dark:bg-slate-950/30 text-slate-400 dark:text-slate-500 font-bold text-xs uppercase tracking-wider border-b border-slate-100 dark:border-slate-800/60">
                        <tr>
                            <th class="py-4 px-6">{{ __('expenses.date') }}</th>
                            <th class="py-4 px-6">{{ __('expenses.description') }}</th>
                            <th class="py-4 px-6">{{ __('expenses.category') }}</th>
                            <th class="py-4 px-6">{{ __('expenses.amount') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50 bg-transparent">
                        @foreach($recentExpenses as $expense)
                            <tr class="hover:bg-blue-50/30 dark:hover:bg-slate-800/35 transition-colors group">
                                <td class="py-4 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 group-hover:text-slate-800 dark:group-hover:text-slate-200 whitespace-nowrap">{{ $expense['date'] }}</td>
                                <td class="py-4 px-6 font-semibold text-slate-700 dark:text-slate-300">{{ $expense['description'] }}</td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 border border-blue-100/30 dark:border-blue-800/30">
                                        {{ $expense['category'] }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-900 dark:text-white whitespace-nowrap">{{ formatMoney($expense['amount']) }} <span class="text-slate-400 dark:text-slate-500 font-normal text-xs">{{ getCurrency() }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-12 flex flex-col items-center justify-center">
                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800/60 rounded-full flex items-center justify-center mb-4 border border-slate-200/50 dark:border-slate-700/50">
                    <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                </div>
                <p class="text-slate-500 dark:text-slate-400 font-semibold text-sm">{{ __('dashboard.no_expenses') }}</p>
            </div>
        @endif
    </div>

{{-- Alerts Modal --}}
<div x-show="showAlerts"
     x-cloak
     x-transition:enter="transition-all duration-300 ease-out"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-all duration-200 ease-in"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 sm:p-4"
     style="padding-bottom: calc(env(safe-area-inset-bottom, 0px) + 0rem); padding-top: calc(env(safe-area-inset-top, 0px) + 0rem);">
    <div x-show="showAlerts"
         x-transition:enter="transition-all duration-300 ease-out"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-all duration-200 ease-in"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="absolute inset-0 bg-slate-900/60"
         @click="showAlerts = false"></div>
    <div x-show="showAlerts"
         x-transition:enter="transition-all duration-300 ease-out"
         x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:translate-y-0 sm:scale-100"
         x-transition:leave="transition-all duration-200 ease-in"
         x-transition:leave-start="opacity-100 translate-y-0 sm:translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
         class="relative bg-white dark:bg-slate-900 rounded-t-3xl sm:rounded-3xl shadow-2xl w-full sm:w-auto sm:min-w-[480px] sm:max-w-lg border border-slate-200/50 dark:border-slate-800/60 overflow-hidden max-h-[90vh] sm:max-h-[70vh] flex flex-col">
        <div class="shrink-0 p-6 border-b border-slate-100 dark:border-slate-800/60">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    {{ __('dashboard.alerts') }}
                </h3>
                <div class="flex items-center gap-2">
                    @if($unreadAlerts > 0)
                        <button wire:click="markAllAlertsRead" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 bg-blue-50 dark:bg-blue-900/20 px-3 py-1.5 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors cursor-pointer">
                            {{ __('settings.mark_read') }}
                        </button>
                    @endif
                    <button @click="showAlerts = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>

            {{-- Filters --}}
            <div class="flex flex-wrap gap-2">
                @foreach($this->alertTypes as $type)
                    <button wire:click="filterByType('{{ $type }}')"
                            class="px-2.5 py-1 rounded-lg text-xs font-bold border transition-colors cursor-pointer
                            {{ $alertFilterType === $type ? 'bg-blue-600 text-white border-blue-600' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                        {{ __("alerts.type.{$type}") }}
                    </button>
                @endforeach
                @foreach($this->alertSeverities as $sev)
                    <button wire:click="filterBySeverity('{{ $sev }}')"
                            class="px-2.5 py-1 rounded-lg text-xs font-bold border transition-colors cursor-pointer
                            {{ $alertFilterSeverity === $sev ? 'bg-blue-600 text-white border-blue-600' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                        {{ __("alerts.severity.{$sev}") }}
                    </button>
                @endforeach
                @if($alertFilterType || $alertFilterSeverity)
                    <button wire:click="resetFilters" class="px-2.5 py-1 rounded-lg text-xs font-bold text-red-600 border border-red-200 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors cursor-pointer">
                        ✕ {{ __('common.reset') }}
                    </button>
                @endif
            </div>
        </div>

        <div class="flex-1 p-4 overflow-y-auto">
            @forelse($alertsPaginated as $alert)
                @php
                    $locale = app()->getLocale();
                    $message = $alert->{'message_' . $locale} ?? $alert->message_fr ?? $alert->message_ar;
                    $actionUrl = $alert->data['action_url'] ?? null;
                    $actionLabel = $alert->data['action_label'] ?? null;
                @endphp
                <div class="flex items-start gap-3 p-4 rounded-2xl mb-2 transition-colors {{ $alert->is_read ? 'bg-transparent' : 'bg-sky-50/60 dark:bg-sky-900/10 border border-sky-200/20 dark:border-sky-800/30' }}">
                    <div class="shrink-0 mt-0.5">
                        @if($alert->severity === 'warning')
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        @elseif($alert->severity === 'error')
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        @elseif($alert->severity === 'success')
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        @else
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold border {{ $alert->is_read ? 'bg-slate-100 dark:bg-slate-800 text-slate-500 border-slate-200/30 dark:border-slate-700/50' : 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 border-blue-200/30 dark:border-blue-800/30' }}">
                                {{ __("alerts.type.{$alert->type}") }}
                            </span>
                            @if(!$alert->is_read)
                                <span class="px-1.5 py-0.5 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-[10px] font-bold rounded">{{ __('settings.new_alert') }}</span>
                            @endif
                        </div>
                        @if($actionUrl)
                            <a href="{{ $actionUrl }}" class="text-sm font-semibold text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-wrap hover:text-blue-600 dark:hover:text-blue-400 transition-colors" target="_blank">
                                {{ $message }}
                            </a>
                        @else
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-wrap">{{ $message }}</p>
                        @endif
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1.5 flex items-center gap-2">
                            <span>{{ $alert->created_at->diffForHumans() }}</span>
                            @if($actionLabel && $actionUrl)
                                <a href="{{ $actionUrl }}" class="text-blue-600 dark:text-blue-400 hover:underline ml-auto" target="_blank">{{ $actionLabel }}</a>
                            @endif
                        </p>
                    </div>
                    @if(!$alert->is_read)
                        <button wire:click="markAlertRead({{ $alert->id }})" class="shrink-0 min-w-[44px] min-h-[44px] p-1.5 text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer inline-flex items-center justify-center" title="Marquer comme lu">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </button>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 dark:text-slate-500">
                    <p class="font-semibold text-sm">{{ __('settings.no_alerts') }}</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($alertsPaginated->hasPages())
            <div class="shrink-0 px-4 py-3 border-t border-slate-100 dark:border-slate-800/60">
                {{ $alertsPaginated->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4" defer></script>
<script>
let chartInstances = {};

function destroyCharts() {
    Object.values(chartInstances).forEach(chart => { if (chart) chart.destroy(); });
    chartInstances = {};
}

function initCharts() {
    destroyCharts();

    Chart.defaults.font.family = "'Inter', 'Outfit', system-ui, sans-serif";
    Chart.defaults.color = '#64748b';

    @if(count($categoryData) > 0)
    const isMobile = window.innerWidth < 640;
    chartInstances.category = new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_column($categoryData, 'label'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!},
            datasets: [{
                data: {!! json_encode(array_column($categoryData, 'total'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!},
                backgroundColor: {!! json_encode(array_column($categoryData, 'color'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!},
                borderWidth: 0,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: isMobile ? 'bottom' : 'right',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: { size: isMobile ? 10 : 12, weight: '500' },
                        boxWidth: isMobile ? 10 : 12
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#1e293b',
                    bodyColor: '#334155',
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    padding: 12,
                    boxPadding: 6,
                    usePointStyle: true,
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) { label += ': '; }
                            if (context.parsed !== null) {
                                label += new Intl.NumberFormat().format(context.parsed) + ' {{ getCurrency() }}';
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
    @endif

    @if(count($monthlyTrend) > 0)
    let ctx = document.getElementById('trendChart').getContext('2d');

    chartInstances.trend = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($monthlyTrend, 'month'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!},
            datasets: [
                {
                    label: '{{ __('dashboard.monthly_total') }}',
                    data: {!! json_encode(array_column($monthlyTrend, 'expenses'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!},
                    backgroundColor: '#EF4444',
                    borderRadius: 4,
                    barPercentage: 0.3,
                },
                {
                    label: '{{ __('common.gains') }}',
                    data: {!! json_encode(array_column($monthlyTrend, 'gains'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!},
                    backgroundColor: '#10B981',
                    borderRadius: 4,
                    barPercentage: 0.3,
                },
                {
                    label: '{{ __('common.balance') }}',
                    data: {!! json_encode(array_column($monthlyTrend, 'balance'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!},
                    borderColor: '#6366F1',
                    borderWidth: 3,
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#6366F1',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    type: 'line',
                    order: 0,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 16,
                        font: { size: 11, weight: '500' },
                        boxWidth: 10
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleColor: '#fff',
                    bodyColor: '#cbd5e1',
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + new Intl.NumberFormat().format(context.parsed.y) + ' {{ getCurrency() }}';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9', drawBorder: false },
                    border: { display: false },
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat().format(value);
                        }
                    }
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

document.addEventListener('DOMContentLoaded', initCharts);
document.addEventListener('livewire:init', function() {
    Livewire.hook('morph.updated', initCharts);
});
</script>
@endpush

