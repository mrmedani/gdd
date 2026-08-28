<div class="max-w-6xl mx-auto space-y-8 animate-fade-in">
    <div class="mb-4">
        <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-700 to-indigo-600 dark:from-blue-400 dark:to-indigo-300 tracking-tight font-heading">
            {{ __('settings.backup_title') }}
        </h1>
        <p class="text-slate-500 dark:text-slate-500 text-sm mt-1.5 font-medium">{{ __('settings.backup_desc') }}</p>
    </div>

    <!-- Create Backup Card -->
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 p-8 relative overflow-hidden group hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
        <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50/20 dark:bg-indigo-500/5 rounded-full blur-3xl -mr-16 -mt-16 z-0 pointer-events-none"></div>
        
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 relative z-10">
            <div>
                <h2 class="text-lg font-bold text-slate-800 dark:text-white font-heading">{{ __('settings.backup_create') }}</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 font-medium">{{ __('settings.backup_create_desc') }}</p>
            </div>
            <button wire:click="createBackup" wire:loading.attr="disabled" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer text-sm whitespace-nowrap">
                <svg wire:loading.remove wire:target="createBackup" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <svg wire:loading wire:target="createBackup" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span wire:loading.remove wire:target="createBackup">{{ __('settings.backup_create_btn') }}</span>
                <span wire:loading wire:target="createBackup">{{ __('common.loading') }}...</span>
            </button>
        </div>
    </div>

    <!-- Status Message -->
    @if($statusMessage)
        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200/50 dark:border-emerald-800/30 rounded-2xl p-4 text-emerald-700 dark:text-emerald-400 text-sm font-semibold">
            {{ $statusMessage }}
        </div>
    @endif

    <!-- Backups List -->
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 overflow-hidden relative z-10 hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
        <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800/60">
            <h2 class="text-lg font-bold text-slate-800 dark:text-white font-heading">{{ __('settings.backup_list') }}</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5 font-medium">{{ __('settings.backup_list_desc', ['count' => count($backups)]) }}</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50/50 dark:bg-slate-950/30 text-slate-400 dark:text-slate-500 font-bold text-xs uppercase tracking-wider border-b border-slate-100 dark:border-slate-800/60">
                    <tr>
                        <th class="py-4 px-6">{{ __('settings.backup_filename') }}</th>
                        <th class="py-4 px-6">{{ __('settings.backup_date') }}</th>
                        <th class="py-4 px-6">{{ __('settings.backup_size') }}</th>
                        <th class="py-4 px-6 text-center">{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50 bg-transparent">
                    @forelse($backups as $backup)
                        <tr class="hover:bg-blue-50/20 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-bold {{ $backup['extension'] === 'sql' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' }}">
                                        {{ strtoupper($backup['extension']) }}
                                    </div>
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $backup['name'] }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-slate-600 dark:text-slate-400 font-medium">{{ $backup['date'] }}</td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                    {{ $backup['size'] }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="restoreBackup('{{ $backup['name'] }}')" wire:confirm="{{ __('settings.confirm_restore_backup') }}" wire:loading.attr="disabled" class="inline-flex items-center gap-1.5 px-3 py-2 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 border border-blue-200/50 dark:border-blue-800/30 rounded-xl font-bold hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-all text-xs cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                        {{ __('settings.backup_restore_btn') }}
                                    </button>
                                    <a href="{{ route('settings.backup.download', ['filename' => $backup['name']]) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/30 rounded-xl font-bold hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-all text-xs cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        {{ __('common.download') }}
                                    </a>
                                    <button wire:click="deleteBackup('{{ $backup['name'] }}')" wire:confirm="{{ __('settings.confirm_delete_backup') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border border-red-200/50 dark:border-red-800/30 rounded-xl font-bold hover:bg-red-100 dark:hover:bg-red-900/40 transition-all text-xs cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        {{ __('common.delete') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900/50 rounded-full flex items-center justify-center mb-3 border border-slate-200/50 dark:border-slate-800/60">
                                        <svg class="w-8 h-8 text-slate-300 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" /></svg>
                                    </div>
                                    <p class="text-slate-500 dark:text-slate-400 font-bold text-lg">{{ __('settings.backup_empty') }}</p>
                                    <p class="text-slate-400 dark:text-slate-500 text-sm mt-1 font-semibold">{{ __('settings.backup_empty_desc') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
