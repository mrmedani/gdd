<div class="max-w-6xl mx-auto space-y-8 animate-fade-in">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
        <div>
            <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-700 to-indigo-600 dark:from-blue-400 dark:to-indigo-300 tracking-tight font-heading">
                {{ __('settings.audit_logs') }}
            </h1>
            <p class="text-slate-500 dark:text-slate-500 text-sm mt-1.5 font-medium">{{ __('audit.subtitle') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" wire:click="exportCsv" class="inline-flex items-center justify-center bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/30 px-4 py-2.5 rounded-xl font-bold hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-all duration-200 shadow-sm cursor-pointer text-sm">
                <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                CSV
            </button>
            <a href="{{ route('settings.index') }}" class="inline-flex items-center justify-center bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 px-6 py-2.5 rounded-xl font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-300 shadow-sm dark:shadow-none">
                {{ __('common.back') }}
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 p-6 relative overflow-hidden group hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50/20 dark:bg-blue-500/5 rounded-full blur-3xl -mr-16 -mt-16 z-0 pointer-events-none"></div>
        <div class="relative z-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('audit.action') }}</label>
                <div class="relative">
                    <select wire:model.live="searchAction" class="w-full px-4 py-2.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all dark:text-slate-100 appearance-none shadow-inner dark:shadow-none font-semibold">
                        <option value="">{{ __('common.all') }}</option>
                        <option value="created">{{ __('audit.created') }}</option>
                        <option value="updated">{{ __('audit.updated') }}</option>
                        <option value="deleted">{{ __('audit.deleted') }}</option>
                        <option value="login">{{ __('audit.login') }}</option>
                        <option value="logout">{{ __('audit.logout') }}</option>
                    </select>
                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 absolute {{ session('locale', 'ar') === 'ar' ? 'left-4' : 'right-4' }} top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('audit.entity_type') }}</label>
                <div class="relative">
                    <select wire:model.live="searchEntityType" class="w-full px-4 py-2.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all dark:text-slate-100 appearance-none shadow-inner dark:shadow-none font-semibold">
                        <option value="">{{ __('common.all') }}</option>
                        @foreach($entityTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 absolute {{ session('locale', 'ar') === 'ar' ? 'left-4' : 'right-4' }} top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('audit.user') }}</label>
                <input type="text" wire:model.live="searchUser" placeholder="{{ __('common.search') }}..." class="w-full px-4 py-2.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all dark:text-slate-100 shadow-inner dark:shadow-none placeholder:text-slate-300 dark:placeholder:text-slate-600">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('audit.entity_id') }}</label>
                <input type="number" wire:model.live="searchEntityId" placeholder="#" class="w-full px-4 py-2.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all dark:text-slate-100 shadow-inner dark:shadow-none placeholder:text-slate-300 dark:placeholder:text-slate-600">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('audit.date_from') }}</label>
                <input type="date" wire:model.live="searchDateFrom" class="w-full px-4 py-2.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all dark:text-slate-100 shadow-inner dark:shadow-none dark:[color-scheme:dark]">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('audit.date_to') }}</label>
                <input type="date" wire:model.live="searchDateTo" class="w-full px-4 py-2.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all dark:text-slate-100 shadow-inner dark:shadow-none dark:[color-scheme:dark]">
            </div>
            <div class="flex items-end">
                <button type="button" wire:click="$set('searchAction', ''); $set('searchEntityType', ''); $set('searchUser', ''); $set('searchEntityId', ''); $set('searchDateFrom', ''); $set('searchDateTo', '')" class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-bold rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors border border-slate-200 dark:border-slate-700 cursor-pointer text-sm">
                    {{ __('common.reset') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 overflow-hidden relative z-10 hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-start {{ session('locale', 'ar') === 'ar' ? 'rtl:text-right' : 'ltr:text-left' }}">
                <thead class="bg-slate-50/50 dark:bg-slate-950/30 text-slate-500 dark:text-slate-500 font-bold text-xs uppercase tracking-wider border-b border-slate-100 dark:border-slate-800/60">
                    <tr>
                        <th class="py-4 px-6">{{ __('audit.user') }}</th>
                        <th class="py-4 px-6">{{ __('audit.action') }}</th>
                        <th class="py-4 px-6">{{ __('audit.entity') }}</th>
                        <th class="py-4 px-6">{{ __('audit.date') }}</th>
                        <th class="py-4 px-6 text-center">{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50 bg-transparent">
                    @forelse($logs as $log)
                        <tr class="hover:bg-blue-50/20 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="py-4 px-6 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-slate-400 to-slate-500 dark:from-slate-600 dark:to-slate-700 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                                        {{ strtoupper(substr($log->user?->name ?? '?', 0, 1)) }}
                                    </div>
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $log->user?->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border
                                    {{ $log->action === 'created' ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border-emerald-100/30 dark:border-emerald-800/30' : '' }}
                                    {{ $log->action === 'deleted' ? 'bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-500 border-rose-100/30 dark:border-rose-800/30' : '' }}
                                    {{ $log->action === 'updated' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 border-blue-100/30 dark:border-blue-800/30' : '' }}
                                    {{ !in_array($log->action, ['created', 'deleted', 'updated']) ? 'bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-400 border-violet-100/30 dark:border-violet-800/30' : '' }}
                                ">
                                    {{ __('audit.' . $log->action) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-slate-500 dark:text-slate-500 font-semibold whitespace-nowrap">{{ $log->entity_type }} <span class="text-slate-300 dark:text-slate-700">#</span>{{ $log->entity_id }}</td>
                            <td class="py-4 px-6 font-medium text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ $log->created_at->format('Y-m-d H:i') }}
                                </div>
                            </td>
                            <td class="py-4 px-6 text-center whitespace-nowrap">
                                <button type="button" wire:click="showDetails({{ $log->id }})" class="min-w-[44px] min-h-[44px] p-2 text-slate-400 dark:text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer inline-flex items-center justify-center" title="{{ __('audit.view_details') }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 bg-slate-50 dark:bg-slate-900/50 rounded-full flex items-center justify-center mb-4 border border-slate-200/50 dark:border-slate-800/60">
                                        <svg class="w-10 h-10 text-slate-300 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <p class="text-slate-500 dark:text-slate-400 font-bold text-lg">{{ __('audit.no_records') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-900/30">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

    <!-- Details Modal -->
    <div x-data="{ open: @entangle('showDetailModal') }" @keydown.escape.window="open = false" x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div x-show="open" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="open = false"></div>
        <div x-show="open" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4" class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200/50 dark:border-slate-800/60 w-full max-w-2xl max-h-[90vh] overflow-y-auto p-6 md:p-8" @click.outside="open = false">
            @if($selectedLog)
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-700 to-indigo-600 dark:from-blue-400 dark:to-indigo-300 tracking-tight font-heading">
                    {{ __('audit.details_title') }}
                </h3>
                <button type="button" @click="open = false" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4 p-4 bg-slate-50/50 dark:bg-slate-800/30 rounded-2xl border border-slate-100 dark:border-slate-800/60">
                    <div>
                        <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ __('audit.user') }}</span>
                        <p class="font-bold text-slate-700 dark:text-slate-200 mt-1">{{ $selectedLog->user?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ __('audit.action') }}</span>
                        <p class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border
                                {{ $selectedLog->action === 'created' ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border-emerald-100/30' : '' }}
                                {{ $selectedLog->action === 'deleted' ? 'bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-500 border-rose-100/30' : '' }}
                                {{ $selectedLog->action === 'updated' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 border-blue-100/30' : '' }}
                                {{ !in_array($selectedLog->action, ['created', 'deleted', 'updated']) ? 'bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-400 border-violet-100/30' : '' }}
                            ">{{ __('audit.' . $selectedLog->action) }}</span>
                        </p>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ __('audit.entity') }}</span>
                        <p class="font-bold text-slate-700 dark:text-slate-200 mt-1">{{ $selectedLog->entity_type }} #{{ $selectedLog->entity_id }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ __('audit.date') }}</span>
                        <p class="font-bold text-slate-700 dark:text-slate-200 mt-1">{{ $selectedLog->created_at->format('Y-m-d H:i:s') }}</p>
                    </div>
                </div>

                @php
                    function auditFieldLabel(string $key): string
                    {
                        $label = __("fields.{$key}");
                        return str_starts_with($label, 'fields.') ? $key : $label;
                    }

                    function auditFieldValue(string $key, mixed $value): string
                    {
                        if ($value === null || $value === '') return '-';
                        if (is_bool($value) || $value === '0' || $value === '1') {
                            return $value && $value !== '0' ? __('common.yes', ['default' => 'Yes']) : __('common.no', ['default' => 'No']);
                        }
                        if ($key === 'category_key' || $key === 'category_id') {
                            $cat = \App\Domains\Expenses\Models\ExpenseCategory::where('key', $value)->first();
                            if ($cat) return $cat->translated_name;
                        }
                        if (in_array($key, ['payment_method', 'payment_type']) && $value) {
                            $pm = __("payment_methods.{$value}");
                            return str_starts_with($pm, 'payment_methods.') ? $value : $pm;
                        }
                        if (in_array($key, ['is_active', 'status']) && in_array($value, ['active', 'inactive', 0, 1, '0', '1'])) {
                            return match ($value) {
                                'active', 1, '1' => __('employees.active', ['default' => 'Active']),
                                'inactive', 0, '0' => __('employees.inactive', ['default' => 'Inactive']),
                                default => $value,
                            };
                        }
                        if (in_array($key, ['date', 'date_start', 'date_end', 'hired_at'])) {
                            try { return \Carbon\Carbon::parse($value)->format('Y-m-d'); } catch (\Exception) {}
                        }
                        return is_array($value) ? json_encode($value) : (string) $value;
                    }
                @endphp

                @if($selectedLog->old_values)
                <div>
                    <h4 class="text-sm font-bold text-rose-500 dark:text-rose-400 mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        {{ __('audit.old_values') }}
                    </h4>
                    <div class="bg-rose-50/50 dark:bg-rose-900/10 border border-rose-100/50 dark:border-rose-800/30 rounded-2xl p-4 overflow-x-auto">
                        <table class="w-full text-xs">
                            <tbody>
                                @foreach((array)$selectedLog->old_values as $key => $value)
                                <tr class="border-b border-rose-100/30 dark:border-rose-800/20 last:border-0">
                                    <td class="py-2 pe-4 font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">{{ auditFieldLabel($key) }}</td>
                                    <td class="py-2 font-mono text-rose-700 dark:text-rose-300 break-all">{{ auditFieldValue($key, $value) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                @if($selectedLog->new_values)
                <div>
                    <h4 class="text-sm font-bold text-emerald-500 dark:text-emerald-400 mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ __('audit.new_values') }}
                    </h4>
                    <div class="bg-emerald-50/50 dark:bg-emerald-900/10 border border-emerald-100/50 dark:border-emerald-800/30 rounded-2xl p-4 overflow-x-auto">
                        <table class="w-full text-xs">
                            <tbody>
                                @foreach((array)$selectedLog->new_values as $key => $value)
                                <tr class="border-b border-emerald-100/30 dark:border-emerald-800/20 last:border-0">
                                    <td class="py-2 pe-4 font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">{{ auditFieldLabel($key) }}</td>
                                    <td class="py-2 font-mono text-emerald-700 dark:text-emerald-300 break-all">{{ auditFieldValue($key, $value) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                @if(!$selectedLog->old_values && !$selectedLog->new_values)
                <div class="text-center py-8">
                    <p class="text-slate-400 dark:text-slate-500 font-medium">{{ __('audit.no_details') }}</p>
                </div>
                @endif
            </div>

            <div class="mt-6 flex justify-end">
                <button type="button" @click="open = false" class="px-6 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors border border-slate-200 dark:border-slate-700 cursor-pointer">
                    {{ __('common.close') }}
                </button>
            </div>
            @endif
        </div>
    </div>
</div>
