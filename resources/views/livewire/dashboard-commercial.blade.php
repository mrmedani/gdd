<div>
<div class="space-y-8 animate-fade-in">
    <div class="flex justify-between items-center mb-2">
        <div>
            <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-700 to-indigo-600 dark:from-blue-400 dark:to-indigo-300 tracking-tight font-heading">{{ __('nav.dashboard') }}</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1.5 font-medium">{{ __('dashboard.commercial_subtitle') }}</p>
        </div>
        @if($unreadAlerts > 0)
            <span class="flex items-center px-4 py-2 bg-gradient-to-r from-red-500 to-rose-600 text-white rounded-full text-xs font-bold shadow-lg">
                {{ __('dashboard.alerts') }}: {{ $unreadAlerts }}
            </span>
        @endif
    </div>

    {{-- Stats de contrats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $statsConfig = [
                'total' => ['label' => __('contracts.stat_total'), 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'grad' => 'from-blue-500 to-indigo-600'],
                'active' => ['label' => __('contracts.stat_active'), 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'grad' => 'from-emerald-500 to-green-600'],
                'expiring_soon' => ['label' => __('contracts.stat_expiring'), 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'grad' => 'from-amber-400 to-orange-500'],
                'expired' => ['label' => __('contracts.stat_expired'), 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z', 'grad' => 'from-slate-400 to-slate-600'],
            ];
        @endphp
        @foreach($statsConfig as $key => $cfg)
        <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl text-white bg-gradient-to-br {{ $cfg['grad'] }} shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $cfg['icon'] }}"/></svg>
                </div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $cfg['label'] }}</span>
            </div>
            <p class="text-3xl font-black text-slate-900 dark:text-white">{{ $contractStats[$key] ?? 0 }}</p>
        </div>
        @endforeach
    </div>

    {{-- Widget Contrats arrivant à échéance --}}
    @if(count($expiringContracts) > 0)
    @php $hasUrgentC = collect($expiringContracts)->contains(fn($u) => $u['urgent']); @endphp
    <div class="overflow-hidden rounded-2xl border {{ $hasUrgentC ? 'border-red-200 dark:border-red-800/60' : 'border-amber-200/50 dark:border-amber-900/40' }} bg-white dark:bg-slate-900 shadow-sm">
        <div class="h-1 w-full {{ $hasUrgentC ? 'bg-gradient-to-r from-rose-500 via-red-500 to-pink-400' : 'bg-gradient-to-r from-amber-400 via-orange-400 to-amber-500' }}"></div>
        <div class="p-5 sm:p-6">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                <div class="flex items-center gap-3">
                    <div class="relative flex h-11 w-11 items-center justify-center rounded-xl text-white shadow-sm {{ $hasUrgentC ? 'bg-gradient-to-br from-rose-500 to-red-600' : 'bg-gradient-to-br from-amber-400 to-orange-500' }}">
                        @if($hasUrgentC)
                        <span class="absolute -top-1 -end-1 flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-600 border-2 border-white dark:border-slate-900"></span>
                        </span>
                        @endif
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900 dark:text-white">{{ __('contracts.widget_title') }}</h2>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('contracts.subtitle') }}</p>
                    </div>
                </div>
                <a href="{{ route('contracts.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline whitespace-nowrap">
                    {{ __('alerts.view_all') }}
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            <div class="space-y-2.5">
                @foreach($expiringContracts as $ec)
                @php
                    $dueC = \Carbon\Carbon::createFromFormat('d/m/Y', $ec['end_date']);
                    $toneC = $ec['urgent'] ? 'red' : 'amber';
                    $pillC = $ec['days_left'] === 0 ? __('alerts.today') : ($ec['days_left'] === 1 ? __('alerts.tomorrow') : 'J-' . $ec['days_left']);
                @endphp
                <div class="group flex items-center gap-3 sm:gap-4 rounded-xl border px-3 py-2.5 transition-all hover:shadow-sm {{ $toneC === 'red' ? 'border-red-300/60 dark:border-red-800/60 bg-red-50/60 dark:bg-red-950/20' : 'border-amber-300/60 dark:border-amber-700/50 bg-amber-50/50 dark:bg-amber-900/15' }}">
                    <div class="flex shrink-0 flex-col items-center justify-center rounded-lg border py-1.5 px-2 {{ $toneC === 'red' ? 'border-red-300 dark:border-red-700/60 bg-white dark:bg-red-950/40 text-red-600 dark:text-red-300' : 'border-amber-300 dark:border-amber-600/60 bg-white dark:bg-amber-900/30 text-amber-600 dark:text-amber-300' }}" style="min-width:3.25rem;min-height:3.25rem">
                        <span class="text-lg font-black leading-none">{{ $dueC->format('d') }}</span>
                        <span class="text-[10px] font-bold uppercase leading-tight">{{ $dueC->translatedFormat('M') }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-100 truncate">📄 {{ $ec['title'] }}</p>
                        <p class="text-xs text-slate-600 dark:text-slate-300 truncate">
                            {{ $ec['party'] ?? '' }}{{ $ec['party'] ? ' · ' : '' }}{{ $dueC->translatedFormat('l d F Y') }}
                        </p>
                    </div>
                    <span class="shrink-0 inline-flex items-center px-3 py-1.5 rounded-full text-xs font-black {{ $toneC === 'red' ? 'bg-red-600 text-white animate-pulse shadow-sm' : 'bg-amber-400 text-amber-950 dark:bg-amber-500/80 dark:text-amber-950 shadow-sm' }}">{{ $pillC }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Hero de bienvenue --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200/70 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
        <div class="p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-5">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl text-white bg-gradient-to-br {{ $greetingGradient }} shadow-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ $greeting }}</p>
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white font-heading">{{ auth()->user()->name }}</h2>
                    @if($roleLabel)
                    <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-black bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">{{ $roleLabel }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Alertes (liste simple en lecture) --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/50 dark:border-slate-800/60 overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800/60">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                {{ __('settings.recent_alerts') }}
            </h3>
        </div>
        <div class="divide-y divide-slate-100 dark:divide-slate-800/60">
            @forelse($alertsPaginated as $alert)
                <div class="p-4 hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                    <div class="flex items-start gap-3">
                        <div class="shrink-0 mt-1.5">
                            @if($alert->severity === 'warning')
                            <span class="block w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            @elseif($alert->severity === 'error')
                            <span class="block w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                            @elseif($alert->severity === 'success')
                            <span class="block w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            @else
                            <span class="block w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ app()->isLocale('ar') ? $alert->message_ar : (app()->isLocale('en') ? ($alert->message_en ?? $alert->message_fr) : $alert->message_fr) }}</p>
                            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500">{{ $alert->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 dark:text-slate-500">
                    <p class="font-semibold text-sm">{{ __('settings.no_alerts') }}</p>
                </div>
            @endforelse
        </div>
        <div class="px-6 py-4">
            {{ $alertsPaginated->links() }}
        </div>
    </div>

</div>
</div>
