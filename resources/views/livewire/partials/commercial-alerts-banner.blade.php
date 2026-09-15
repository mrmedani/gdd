{{-- Modal des alertes (composant dashboard-commercial) --}}
<div x-show="showAlerts"
     x-cloak
     x-transition:enter="transition-all duration-300 ease-out"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-all duration-200 ease-in"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div x-show="showAlerts" x-transition.opacity class="absolute inset-0 bg-slate-900/60" @click="showAlerts = false"></div>
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
                    <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    {{ __('dashboard.alerts') }}
                </h3>
                <div class="flex items-center gap-2">
                    @if($unreadAlerts > 0)
                        <button wire:click="markAllAlertsRead" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 bg-blue-50 dark:bg-blue-900/20 px-3 py-1.5 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors cursor-pointer">
                            {{ __('settings.mark_read') }}
                        </button>
                    @endif
                    <button @click="showAlerts = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            {{-- Filtres par type et sévérité --}}
            <div class="flex flex-wrap gap-2">
                @foreach($this->alertTypes as $type)
                    <button wire:click="filterByType('{{ $type }}')"
                            class="text-[11px] font-bold px-2.5 py-1 rounded-full transition-colors cursor-pointer {{ $alertFilterType === $type ? 'bg-blue-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                        {{ __('alerts.type.' . $type) }}
                    </button>
                @endforeach
                <span class="w-1"></span>
                @foreach($this->alertSeverities as $sev)
                    <button wire:click="filterBySeverity('{{ $sev }}')"
                            class="text-[11px] font-bold px-2.5 py-1 rounded-full transition-colors cursor-pointer {{ $alertFilterSeverity === $sev ? 'bg-purple-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                        {{ __('alerts.severity.' . $sev) }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="flex-1 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60">
            @forelse($alertsPaginated as $alert)
                @php
                    $actionUrl = $alert->data['action_url'] ?? null;
                    $actionLabel = $alert->action_label ?? null;
                @endphp
                <div class="p-4 hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                    <div class="flex items-start gap-3">
                        <div class="shrink-0 mt-0.5">
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
                            <div class="flex items-center gap-2 mt-1.5">
                                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500">{{ $alert->created_at->diffForHumans() }}</span>
                                @if($actionUrl)
                                    <a href="{{ $actionUrl }}" class="text-blue-600 dark:text-blue-400 hover:underline ml-auto text-xs font-bold" target="_blank">{{ $actionLabel ?? __('dashboard.view') }}</a>
                                @endif
                            </div>
                        </div>
                        @if(!$alert->is_read)
                            <button wire:click="markAlertRead({{ $alert->id }})" class="shrink-0 text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors cursor-pointer" title="Marquer comme lu">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 dark:text-slate-500">
                    <p class="font-semibold text-sm">{{ __('settings.no_alerts') }}</p>
                </div>
            @endforelse
        </div>
        @if($alertsPaginated->hasPages())
        <div class="shrink-0 px-4 py-3 border-t border-slate-100 dark:border-slate-800/60">
            {{ $alertsPaginated->links() }}
        </div>
        @endif
    </div>
</div>
