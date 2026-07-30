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

    <!-- Welcome Header (Colorful Modern UI) -->
    <div x-data="{
             time: '',
             init() {
                 const updateTime = () => {
                     const now = new Date();
                     const h = String(now.getHours()).padStart(2, '0');
                     const m = String(now.getMinutes()).padStart(2, '0');
                     const s = String(now.getSeconds()).padStart(2, '0');
                     this.time = h + ':' + m + ':' + s;
                 };
                 updateTime();
                 setInterval(updateTime, 1000);
             }
         }"
         class="mb-6 sm:mb-8 p-5 sm:p-7 lg:p-8 bg-white dark:bg-slate-900 border-2 border-blue-50 dark:border-slate-800/80 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] flex flex-col xl:flex-row xl:items-center justify-between gap-6 sm:gap-8 relative overflow-hidden transition-colors">
        
        <!-- Colorful decorative background -->
        <div class="absolute -top-24 -right-24 w-64 h-64 bg-gradient-to-br from-blue-400/20 to-indigo-500/20 dark:from-blue-500/10 dark:to-indigo-500/10 rounded-full blur-[80px] pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-gradient-to-br from-amber-400/20 to-orange-500/20 dark:from-amber-500/10 dark:to-orange-500/10 rounded-full blur-[80px] pointer-events-none"></div>

        <div class="flex flex-col sm:flex-row sm:items-center gap-5 sm:gap-6 relative z-10 w-full xl:w-auto">
            <!-- Vibrant Icon Box -->
            <div class="shrink-0 flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20 rounded-[1.25rem] bg-gradient-to-br {{ $greetingGradient }} shadow-lg shadow-[var(--tw-gradient-to)]/30 dark:shadow-[var(--tw-gradient-to)]/20 text-white transition-transform hover:scale-105 duration-300">
                @if($greetingIcon === 'sun')
                    <svg class="w-8 h-8 sm:w-10 sm:h-10 drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                @elseif($greetingIcon === 'cloud-sun')
                    <svg class="w-8 h-8 sm:w-10 sm:h-10 drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path></svg>
                @elseif($greetingIcon === 'sunset')
                    <svg class="w-8 h-8 sm:w-10 sm:h-10 drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path></svg>
                @else
                    <svg class="w-8 h-8 sm:w-10 sm:h-10 drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                @endif
            </div>

            <div class="min-w-0 flex-1">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white font-heading tracking-tight mb-3">
                    {{ $greeting }}, <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400">{{ auth()->user()->name }}</span>
                </h2>
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    <!-- Local Time -->
                    <div class="inline-flex items-center gap-2 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 px-3 py-2 rounded-xl text-xs sm:text-sm font-bold border border-blue-100/50 dark:border-blue-800/30 shadow-sm">
                        <svg class="w-4 h-4 text-blue-500 dark:text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span x-text="time" class="tabular-nums font-mono tracking-wider"></span>
                    </div>
                    <!-- Date -->
                    <div class="inline-flex items-center gap-2 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 px-3 py-2 rounded-xl text-xs sm:text-sm font-bold border border-indigo-100/50 dark:border-indigo-800/30 shadow-sm">
                        <svg class="w-4 h-4 text-indigo-500 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="tracking-wide">{{ $serverDate }}</span>
                    </div>
                    <!-- Period -->
                    <div class="inline-flex items-center gap-1 bg-gradient-to-r {{ $greetingGradient }} px-3 py-2 rounded-xl text-[10px] sm:text-xs font-black uppercase tracking-wider text-white shadow-md shadow-[var(--tw-gradient-to)]/30 dark:shadow-none opacity-90 hover:opacity-100 transition-opacity">
                        {{ $currentPeriodLabel }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Sleek Profile Pill -->
        <div class="flex items-center gap-3 sm:gap-4 bg-slate-50 dark:bg-slate-800/50 p-2 rounded-[2rem] border border-slate-100 dark:border-slate-700/50 relative z-10 w-full sm:w-max hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer xl:ms-auto sm:pe-6 sm:ps-2 shadow-sm">
            <div class="w-10 h-10 sm:w-12 sm:h-12 shrink-0 rounded-full flex items-center justify-center bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 font-black text-base sm:text-lg border border-blue-200/50 dark:border-blue-800/50 shadow-inner group-hover:scale-105 transition-transform">
                {{ mb_substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="text-start flex flex-col justify-center flex-1 sm:flex-initial overflow-hidden">
                <p class="text-xs sm:text-sm font-bold text-slate-800 dark:text-white mb-0.5 truncate">{{ auth()->user()->role?->label_fr ?? auth()->user()->role?->name ?? '' }}</p>
                <p class="text-[10px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 truncate">{{ auth()->user()->email }}</p>
            </div>
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
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
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

    <!-- Growth Rate -->
    <div class="relative overflow-hidden bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200/50 dark:border-slate-800/60 rounded-3xl p-6 shadow-premium dark:shadow-premium-dark transition-all duration-300 hover:-translate-y-1 hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-50 dark:bg-emerald-500/5 rounded-full blur-2xl group-hover:scale-120 transition-transform duration-500"></div>
        <div class="flex items-center gap-5">
            <div class="p-4 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-2xl border border-emerald-100/50 dark:border-emerald-500/20 shrink-0">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </div>
            <div class="flex-1">
                <p class="text-slate-500 dark:text-slate-400 font-bold text-xs uppercase tracking-wider mb-1">{{ __('dashboard.growth') }}</p>
                @if($growthRate !== null)
                    <h3 class="text-3xl font-black {{ $growthRate >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-500' }} font-heading">
                        {{ $growthRate >= 0 ? '+' : '' }}{{ number_format($growthRate, 1) }}%
                    </h3>
                    <p class="{{ $growthRate >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-500' }} font-bold mt-1.5 text-xs flex items-center">
                        <svg class="w-4 h-4 me-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $growthRate >= 0 ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6' }}"></path>
                        </svg>
                        {{ $growthRate >= 0 ? __('dashboard.growth_up') : __('dashboard.growth_down') }}
                    </p>
                @else
                    <h3 class="text-3xl font-black text-slate-300 dark:text-slate-600 font-heading">—</h3>
                    <p class="text-slate-400 dark:text-slate-500 font-bold mt-1.5 text-xs">{{ __('dashboard.growth_na') }}</p>
                @endif
            </div>
        </div>
    </div>

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
                <div wire:key="category-chart" class="relative w-full" style="height: 320px;">
                    <!-- Ligne verticale (séparateur) en inline CSS pour éviter les soucis de compilation Tailwind -->
                    <div style="position: absolute; left: 52%; top: 10%; bottom: 10%; width: 2px; background: linear-gradient(to bottom, transparent, rgba(100, 116, 139, 0.4), transparent); z-index: 0; pointer-events: none;"></div>
                    
                    <div id="categoryChart" class="w-full h-full relative z-10"></div>
                </div>
            @else
                <div wire:key="category-chart-empty" class="flex flex-col items-center justify-center border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl" style="height: 320px;">
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
                    <div wire:key="trend-chart" class="relative w-full" style="height: 320px;">
                        <div id="trendChart" class="w-full h-full"></div>
                    </div>
                @else
                    <div wire:key="trend-chart-empty" class="flex flex-col items-center justify-center border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl" style="height: 320px;">
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
<script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
<script>
let chartInstances = {};
let darkModeObserver = null;

function isDark() {
    return document.documentElement.classList.contains('dark');
}

function destroyCharts() {
    Object.values(chartInstances).forEach(chart => { if (chart) chart.dispose(); });
    chartInstances = {};
}

function initCharts() {
    destroyCharts();
    const dark = isDark();

    @if(count($categoryData) > 0)
    const isMobile = window.innerWidth < 640;
    chartInstances.category = echarts.init(document.getElementById('categoryChart'));
    chartInstances.category.setOption({
        tooltip: {
            trigger: 'item',
            backgroundColor: dark ? 'rgba(15, 23, 42, 0.85)' : 'rgba(255, 255, 255, 0.9)',
            borderColor: dark ? 'rgba(51, 65, 85, 0.5)' : 'rgba(226, 232, 240, 0.8)',
            borderWidth: 1,
            padding: [16, 20],
            textStyle: { color: dark ? '#f8fafc' : '#1e293b', fontSize: 13, fontWeight: 500 },
            extraCssText: 'border-radius: 16px; backdrop-filter: blur(16px); box-shadow: 0 10px 40px -10px rgba(0,0,0,' + (dark ? '0.5' : '0.15') + ');',
            formatter: function(params) {
                return '<div style="font-size:13px;font-weight:600;color:' + (dark ? '#94a3b8' : '#64748b') + ';margin-bottom:8px;text-transform:uppercase;letter-spacing:0.5px">' + params.marker + ' ' + params.name + '</div>' +
                       '<div style="font-size:20px;font-weight:800;color:' + (dark ? '#fff' : '#0f172a') + '; letter-spacing:-0.5px;">' + new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2 }).format(params.value) + ' <span style="font-size:14px;font-weight:600">{{ getCurrency() }}</span></div>' +
                       '<div style="font-size:13px;font-weight:600;color:' + params.color + ';margin-top:4px;">' + params.percent + '% du total</div>';
            }
        },
        legend: {
            type: 'scroll',
            orient: isMobile ? 'horizontal' : 'vertical',
            right: isMobile ? 'center' : '2%',
            bottom: isMobile ? 0 : 'center',
            top: isMobile ? 'auto' : 'center',
            icon: 'circle',
            itemWidth: 12,
            itemHeight: 12,
            itemGap: 16,
            textStyle: { fontSize: 12, fontWeight: 600, color: dark ? '#cbd5e1' : '#475569' },
            pageIconColor: '#3b82f6',
            pageTextStyle: { color: dark ? '#cbd5e1' : '#475569' }
        },
        animationDuration: 1500,
        animationEasing: 'cubicOut',
        series: [{
            type: 'pie',
            radius: ['45%', '75%'],
            center: isMobile ? ['50%', '40%'] : ['26%', '50%'],
            avoidLabelOverlap: true,
            itemStyle: {
                borderRadius: 12,
                borderColor: dark ? '#0f172a' : '#ffffff',
                borderWidth: 4,
                shadowBlur: 15,
                shadowColor: 'rgba(0, 0, 0, 0.1)',
                shadowOffsetX: 0,
                shadowOffsetY: 5
            },
            emphasis: {
                scale: true,
                scaleSize: 10,
                itemStyle: {
                    shadowBlur: 25,
                    shadowOffsetX: 0,
                    shadowOffsetY: 10,
                    shadowColor: 'rgba(0, 0, 0, 0.3)'
                }
            },
            label: { show: false },
            data: [
                @foreach($categoryData as $cat)
                { value: {{ $cat['total'] }}, name: '{!! addslashes($cat['label']) !!}', itemStyle: { color: '{{ $cat['color'] }}' } },
                @endforeach
            ].sort(function (a, b) { return b.value - a.value; })
        }]
    });
    @endif

    @if(count($monthlyTrend) > 0)
    chartInstances.trend = echarts.init(document.getElementById('trendChart'));
    chartInstances.trend.setOption({
        tooltip: {
            trigger: 'axis',
            backgroundColor: dark ? 'rgba(15, 23, 42, 0.92)' : 'rgba(255, 255, 255, 0.96)',
            borderColor: dark ? 'rgba(51, 65, 85, 0.5)' : 'rgba(226, 232, 240, 0.8)',
            borderWidth: 1,
            padding: [12, 16],
            textStyle: { color: dark ? '#e2e8f0' : '#334155', fontSize: 13, fontWeight: 500 },
            extraCssText: 'border-radius: 12px; backdrop-filter: blur(12px); box-shadow: 0 8px 32px rgba(0,0,0,' + (dark ? '0.4' : '0.12') + ');',
            formatter: function(params) {
                let res = '<div style="font-size:14px;font-weight:700;margin-bottom:6px">' + params[0].axisValueLabel + '</div>';
                params.forEach(function(p) {
                    res += '<div style="font-size:13px;margin-top:4px">' + p.marker + ' ' + p.seriesName + ': <strong style="float:right;margin-left:16px;color:' + (dark ? '#f8fafc' : '#0f172a') + '">' + new Intl.NumberFormat().format(p.value) + ' {{ getCurrency() }}</strong></div>';
                });
                return res;
            },
            axisPointer: {
                lineStyle: { color: dark ? '#6366f1' : '#818cf8', type: 'dashed' }
            }
        },
        legend: {
            bottom: 0,
            icon: 'circle',
            itemWidth: 10,
            itemHeight: 10,
            textStyle: { fontSize: 11, fontWeight: 600, color: dark ? '#94a3b8' : '#64748b' }
        },
        grid: { left: '3%', right: '4%', bottom: '18%', top: '8%', containLabel: true },
        xAxis: {
            type: 'category',
            data: {!! json_encode(array_column($monthlyTrend, 'month'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!},
            axisLine: { show: false },
            axisTick: { show: false },
            axisLabel: { fontSize: 11, color: dark ? '#475569' : '#94a3b8', fontWeight: 600 }
        },
        yAxis: {
            type: 'value',
            splitLine: { lineStyle: { color: dark ? 'rgba(51, 65, 85, 0.3)' : '#f1f5f9', type: 'dashed' } },
            axisLabel: {
                fontSize: 11,
                color: dark ? '#475569' : '#94a3b8',
                fontWeight: 500,
                formatter: function(v) { return new Intl.NumberFormat('en', { notation: 'compact' }).format(v); }
            }
        },
        animationDuration: 1000,
        animationEasing: 'cubicOut',
        series: [
            {
                name: '{{ __('dashboard.monthly_total') }}',
                type: 'bar',
                barWidth: '20%',
                data: {!! json_encode(array_column($monthlyTrend, 'expenses'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!},
                itemStyle: { 
                    color: {
                        type: 'linear', x: 0, y: 0, x2: 0, y2: 1,
                        colorStops: [{ offset: 0, color: 'rgba(239, 68, 68, 0.95)' }, { offset: 1, color: 'rgba(239, 68, 68, 0.65)' }]
                    },
                    borderRadius: [4, 4, 0, 0] 
                }
            },
            {
                name: '{{ __('common.gains') }}',
                type: 'bar',
                barWidth: '20%',
                data: {!! json_encode(array_column($monthlyTrend, 'gains'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!},
                itemStyle: { 
                    color: {
                        type: 'linear', x: 0, y: 0, x2: 0, y2: 1,
                        colorStops: [{ offset: 0, color: 'rgba(16, 185, 129, 0.95)' }, { offset: 1, color: 'rgba(16, 185, 129, 0.65)' }]
                    },
                    borderRadius: [4, 4, 0, 0] 
                }
            },
            {
                name: '{{ __('common.balance') }}',
                type: 'line',
                smooth: true,
                symbol: 'none',
                data: {!! json_encode(array_column($monthlyTrend, 'balance'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!},
                lineStyle: { color: '#6366F1', width: 3.5, shadowColor: 'rgba(99, 102, 241, 0.5)', shadowBlur: 10 },
                itemStyle: { color: '#6366F1' },
                areaStyle: { 
                    color: {
                        type: 'linear', x: 0, y: 0, x2: 0, y2: 1,
                        colorStops: [{ offset: 0, color: 'rgba(99, 102, 241, 0.2)' }, { offset: 1, color: 'rgba(99, 102, 241, 0.01)' }]
                    }
                },
                emphasis: { focus: 'series' }
            }
        ]
    });
    @endif
    
    // Setup dark mode observer if not already done
    if (!darkModeObserver) {
        darkModeObserver = new MutationObserver(() => {
            initCharts();
        });
        darkModeObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
    }
}

document.addEventListener('DOMContentLoaded', initCharts);
document.addEventListener('livewire:init', function() {
    Livewire.hook('morph.updated', initCharts);
});

let resizeTimer;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
        Object.values(chartInstances).forEach(chart => { 
            if (chart) chart.resize({ animation: { duration: 300, easing: 'cubicOut' } }); 
        });
    }, 150);
});
</script>
@endpush

