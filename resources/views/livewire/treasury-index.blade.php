<div class="max-w-6xl mx-auto space-y-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800 dark:text-white tracking-tight font-heading">
                {{ __('caisse.title') }}
            </h1>
            <p class="text-slate-500 dark:text-slate-500 text-sm mt-1.5 font-medium">{{ __('caisse.subtitle') }}</p>
        </div>
        @if($currentMonthClosed)
            <div class="inline-flex items-center justify-center bg-slate-200 dark:bg-slate-800 text-slate-400 dark:text-slate-500 px-6 py-2.5 rounded-xl font-semibold cursor-not-allowed select-none border border-slate-200 dark:border-slate-700" title="{{ __('caisse.already_closed', ['default' => 'Ce mois est déjà clôturé.']) }}">
                <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                {{ __('caisse.month_already_closed', ['default' => 'Mois clôturé']) }}
            </div>
        @else
            <button wire:click="$set('showCloseModal', true)" class="inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl font-semibold transition-colors cursor-pointer">
                <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                {{ __('caisse.close_month_btn') }}
            </button>
        @endif
    </div>

    <!-- Global Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Balance -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">{{ __('caisse.global_balance') }}</p>
                    <h3 class="text-2xl font-black text-slate-800 dark:text-white leading-none flex items-baseline gap-1 font-mono"><span dir="ltr">{{ number_format($globalBalance, 2, ',', ' ') }}</span> <span class="text-xs text-slate-400 font-semibold">{{ getCurrency() }}</span></h3>
                </div>
            </div>
        </div>

        <!-- Total Gains -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">{{ __('caisse.total_gains') }}</p>
                    <h3 class="text-2xl font-black text-blue-600 dark:text-blue-400 leading-none flex items-baseline gap-1 font-mono"><span dir="ltr">{{ number_format($totalGains, 2, ',', ' ') }}</span> <span class="text-xs text-slate-400 font-semibold">{{ getCurrency() }}</span></h3>
                </div>
            </div>
        </div>

        <!-- Total Expenses -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-rose-100 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">{{ __('caisse.total_expenses') }}</p>
                    <h3 class="text-2xl font-black text-slate-800 dark:text-white leading-none flex items-baseline gap-1"><span dir="ltr">{{ number_format($totalExpenses, 2, ',', ' ') }}</span> <span class="text-xs text-slate-400 font-semibold">{{ getCurrency() }}</span></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Closures History -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center gap-3 bg-slate-50/20 dark:bg-slate-950/20">
            <div class="w-10 h-10 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-lg flex items-center justify-center border border-slate-200/30 dark:border-slate-700/50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h2 class="text-lg font-bold text-slate-800 dark:text-white">{{ __('caisse.history_title') }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-start {{ session('locale', 'ar') === 'ar' ? 'rtl:text-right' : 'ltr:text-left' }}">
                <thead class="bg-slate-50/50 dark:bg-slate-950/30 text-slate-500 dark:text-slate-500 font-bold text-xs uppercase tracking-wider border-b border-slate-100 dark:border-slate-800/60">
                    <tr>
                        <th class="py-4 px-6">{{ __('caisse.month') }}</th>
                        <th class="py-4 px-6">{{ __('caisse.gains') }}</th>
                        <th class="py-4 px-6">{{ __('caisse.total_incomes_auto') }}</th>
                        <th class="py-4 px-6">{{ __('caisse.expenses') }}</th>
                        <th class="py-4 px-6">{{ __('caisse.investments') }}</th>
                        <th class="py-4 px-6">{{ __('caisse.balance') }}</th>
                        <th class="py-4 px-6 text-center">{{ __('caisse.growth') }}</th>
                        <th class="py-4 px-6">{{ __('caisse.close_date') }}</th>
                        <th class="py-4 px-6">{{ __('caisse.closed_by') }}</th>
                        <th class="py-4 px-6">{{ __('caisse.closure_note_th', ['default' => 'Note']) }}</th>
                        @can('manage-delete-closure')
                            <th class="py-4 px-6 text-center">{{ __('common.actions') }}</th>
                        @endcan
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50 bg-transparent">
                    @forelse($closures as $closure)
                        @php $pm = \Carbon\Carbon::createFromFormat('Y-m', $closure->month); @endphp
                        <tr class="hover:bg-blue-50/20 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="flex shrink-0 flex-col items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-800/40 text-blue-700 dark:text-blue-300" style="min-width:3rem;min-height:3rem">
                                        <span class="text-sm font-black leading-none">{{ $pm->translatedFormat('M') }}</span>
                                        <span class="text-[10px] font-bold leading-tight">{{ $pm->format('Y') }}</span>
                                    </div>
                                    <span class="font-bold text-slate-800 dark:text-slate-200 whitespace-nowrap">{{ formatPeriodLabelShort($closure->month) }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-100 dark:border-emerald-800/40"><span dir="ltr">+ {{ number_format($closure->gains, 2, ',', ' ') }} {{ getCurrency() }}</span></span></td>
                            <td class="py-4 px-6 whitespace-nowrap"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-sky-50 dark:bg-sky-500/10 text-sky-700 dark:text-sky-300 border border-sky-100 dark:border-sky-800/40"><span dir="ltr">+ {{ number_format($closure->incomes ?? 0, 2, ',', ' ') }} {{ getCurrency() }}</span></span></td>
                            <td class="py-4 px-6 whitespace-nowrap"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-100 dark:border-rose-800/40"><span dir="ltr">− {{ number_format($closure->expenses, 2, ',', ' ') }} {{ getCurrency() }}</span></span></td>
                            <td class="py-4 px-6 whitespace-nowrap"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-100 dark:border-amber-800/40"><span dir="ltr">− {{ number_format($closure->investments ?? 0, 2, ',', ' ') }} {{ getCurrency() }}</span></span></td>
                            <td class="py-4 px-6 whitespace-nowrap"><span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-black {{ $closure->balance >= 0 ? 'bg-emerald-600 text-white shadow-sm' : 'bg-rose-600 text-white shadow-sm' }}"><span dir="ltr">{{ $closure->balance > 0 ? '+' : '' }}{{ number_format($closure->balance, 2, ',', ' ') }} {{ getCurrency() }}</span></span></td>
                            <td class="py-4 px-6 text-center whitespace-nowrap">
                                @php $rate = $growthRates[$closure->month] ?? null; @endphp
                                @if($rate !== null)
                                    <span class="inline-flex items-center gap-1 text-sm font-extrabold {{ $rate >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-500' }}">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="{{ $rate >= 0 ? 'M10 3l7 9H3z' : 'M10 17l-7-9h14z' }}"/></svg>
                                        {{ number_format(abs($rate), 1) }}%
                                    </span>
                                @else
                                    <span class="text-slate-300 dark:text-slate-600">—</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 font-semibold text-slate-500 dark:text-slate-400 whitespace-nowrap">{{ $closure->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-4 px-6 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-black">{{ mb_substr($closure->closer->name ?? '?', 0, 1) }}</span>
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $closure->closer->name ?? '-' }}</span>
                                </div>
                            </td>
                            {{-- NOTE DE CLÔTURE — affichage + édition inline --}}
                            <td class="py-4 px-6 align-top">
                                @if($editingNoteId === $closure->id)
                                    <div class="min-w-[220px] max-w-md">
                                        <textarea wire:model="editingNote" rows="3" maxlength="2000" id="note-input-{{ $closure->id }}"
                                            class="w-full px-3 py-2 bg-white dark:bg-slate-950/60 border border-blue-300 dark:border-blue-700 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white outline-none transition-all text-xs"></textarea>
                                        @error('editingNote') <span class="text-red-500 text-[10px] block">{{ $message }}</span> @enderror
                                        <div class="flex gap-2 mt-2 justify-end">
                                            <button type="button" wire:click="cancelEditNote" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 cursor-pointer border border-slate-200/60 dark:border-slate-700/60">{{ __('common.cancel') }}</button>
                                            <button type="button" wire:click="saveNote"
                                                class="px-3 py-1.5 text-xs font-bold rounded-lg bg-blue-600 text-white hover:bg-blue-700 cursor-pointer">{{ __('common.save') }}</button>
                                        </div>
                                    </div>
                                @elseif($closure->closure_note)
                                    <div class="group flex items-start gap-2 min-w-[220px] max-w-md">
                                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed pe-0" dir="auto">{{ $closure->closure_note }}</p>
                                        <button type="button" wire:click="startEditNote({{ $closure->id }})"
                                                class="shrink-0 mt-0.5 p-1 rounded hover:bg-blue-50 dark:hover:bg-blue-900/20 text-blue-500 hover:text-blue-600 cursor-pointer transition-colors" title="Modifier la note">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-slate-300 dark:text-slate-600 text-xs">—</span>
                                @endif
                            </td>
                            @can('manage-delete-closure')
                                <td class="py-4 px-6 text-center">
                                    <button wire:click="confirmDelete({{ $closure->id }})"
                                            class="text-rose-400 hover:text-rose-600 dark:hover:text-rose-400 min-w-[44px] min-h-[44px] p-1 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg cursor-pointer inline-flex items-center justify-center transition-colors"
                                            title="{{ __('common.delete') }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            @endcan
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900/50 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-10 h-10 text-slate-300 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    </div>
                                    <p class="text-slate-500 dark:text-slate-400 font-bold text-lg">{{ __('caisse.no_closures') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($closures->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-900/30">
                {{ $closures->links() }}
            </div>
        @endif
    </div>

    <!-- Delete Closure Modal -->
    <div x-data="{ show: @entangle('showDeleteModal') }" 
         x-show="show" 
         style="display: none"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        
        <div x-show="show" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-slate-900/60 backdrop-blur-md"
             @click="show = false"></div>
        
        <div x-show="show" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-lg w-full max-w-md border border-slate-200 dark:border-slate-800 overflow-hidden">
            
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-950/40">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">{{ __('caisse.delete_modal_title') }}</h3>
                <button @click="show = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <form wire:submit="deleteClosure" class="p-6 space-y-5">
                <div class="flex items-center gap-3 text-rose-600 dark:text-rose-400 bg-rose-50/50 dark:bg-rose-950/20 p-4 rounded-xl border border-rose-100/30 dark:border-rose-900/30">
                    <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <p class="text-sm font-semibold">{{ __('caisse.delete_modal_warning') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('caisse.delete_confirm_password') }} <span class="text-red-500">*</span></label>
                    <input type="password" wire:model="deletePassword" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 dark:text-white outline-none transition-all font-semibold" placeholder="••••••••">
                    @error('deletePassword') <span class="text-rose-500 dark:text-rose-400 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-3 justify-end pt-2 border-t border-slate-100 dark:border-slate-800/60">
                    <button type="button" @click="show = false" class="px-6 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors cursor-pointer border border-slate-200/50 dark:border-slate-700/50">{{ __('caisse.cancel') }}</button>
                    <button type="submit" class="px-6 py-2.5 bg-rose-600 text-white font-semibold rounded-xl hover:bg-rose-700 transition-colors flex items-center cursor-pointer">
                        <svg wire:loading wire:target="deleteClosure" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="deleteClosure">{{ __('caisse.delete_submit') }}</span>
                        <span wire:loading wire:target="deleteClosure">{{ __('common.loading') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Close Month Modal -->
    <div x-data="{ show: @entangle('showCloseModal') }" 
         x-show="show" 
         style="display: none"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        
        <!-- Backdrop -->
        <div x-show="show" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-slate-900/60 backdrop-blur-md"
             @click="show = false"></div>
        
        <!-- Modal Content -->
        <div x-show="show" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-lg w-full max-w-lg border border-slate-200 dark:border-slate-800 overflow-hidden">
            
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-950/40">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">{{ __('caisse.modal_title') }}</h3>
                <button @click="show = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form wire:submit="closeMonthSubmit" class="p-6 space-y-6">
                <!-- Month Selection -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('caisse.month_to_close') }} <span class="text-red-500">*</span></label>
                    <div x-data="{ pickerOpen: false, pickerYear: {{ date('Y') }} }" class="relative">
                        <button type="button" @click="pickerOpen = !pickerOpen"
                                class="w-full flex items-center gap-3 px-4 py-3 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl font-semibold dark:text-white cursor-pointer text-left hover:bg-slate-100 dark:hover:bg-slate-900/50 transition-colors">
                            <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>{{ $closeMonth ? formatPeriodLabelShort($closeMonth) : 'Sélectionnez un mois' }}</span>
                        </button>

                        <div x-show="pickerOpen" @click.outside="pickerOpen = false"
                             class="absolute z-50 mt-2 w-full bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 p-4"
                             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                            <div class="flex items-center justify-between mb-3">
                                <button type="button" @click="pickerYear--"
                                        class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                </button>
                                <span class="font-bold text-slate-800 dark:text-white text-sm" x-text="pickerYear"></span>
                                <button type="button" @click="pickerYear++"
                                        class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach (['01' => 'Janvier', '02' => 'Février', '03' => 'Mars', '04' => 'Avril', '05' => 'Mai', '06' => 'Juin', '07' => 'Juillet', '08' => 'Août', '09' => 'Septembre', '10' => 'Octobre', '11' => 'Novembre', '12' => 'Décembre'] as $num => $label)
                                    <button type="button"
                                            @click="const p = pickerYear + '-' + '{{ $num }}'; if (!{{ json_encode($closedMonths) }}.includes(p)) { $wire.set('closeMonth', p); pickerOpen = false; }"
                                            :class="{
                                                'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400': $wire.closeMonth === (pickerYear + '-' + '{{ $num }}'),
                                                'text-slate-300 dark:text-slate-600 opacity-40 cursor-not-allowed': {{ json_encode($closedMonths) }}.includes(pickerYear + '-' + '{{ $num }}'),
                                                'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50': $wire.closeMonth !== (pickerYear + '-' + '{{ $num }}') && !{{ json_encode($closedMonths) }}.includes(pickerYear + '-' + '{{ $num }}')
                                            }"
                                            class="px-2 py-2.5 rounded-lg text-sm font-medium transition-colors">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @error('closeMonth') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Expenses Display (Readonly) -->
                <div class="bg-rose-50/30 dark:bg-rose-950/20 border border-rose-100/50 dark:border-rose-900/30 p-4 rounded-xl">
                    <label class="block text-xs font-semibold text-rose-800 dark:text-rose-400 mb-1.5">{{ __('caisse.total_expenses_auto') }}</label>
                    <div class="text-2xl font-black text-rose-600 dark:text-rose-500 leading-none flex items-baseline gap-1">
                        <span dir="ltr">{{ number_format((float)$calculatedExpenses, 2, ',', ' ') }}</span> <span class="text-sm font-bold">{{ getCurrency() }}</span>
                    </div>
                </div>

                <!-- Incomes Display (Readonly) -->
                <div class="bg-emerald-50/30 dark:bg-emerald-950/20 border border-emerald-100/50 dark:border-emerald-900/30 p-4 rounded-xl">
                    <label class="block text-xs font-semibold text-emerald-800 dark:text-emerald-400 mb-1.5">{{ __('caisse.total_incomes_auto') }}</label>
                    <div class="text-2xl font-black text-emerald-600 dark:text-emerald-500 leading-none flex items-baseline gap-1">
                        <span dir="ltr">+ {{ number_format((float)$calculatedIncomes, 2, ',', ' ') }}</span> <span class="text-sm font-bold">{{ getCurrency() }}</span>
                    </div>
                </div>

                <!-- Investments Display (Readonly) -->
                <div class="bg-amber-50/30 dark:bg-amber-950/20 border border-amber-100/50 dark:border-amber-900/30 p-4 rounded-xl">
                    <label class="block text-xs font-semibold text-amber-800 dark:text-amber-400 mb-1.5">{{ __('caisse.total_investments_auto') }}</label>
                    <div class="text-2xl font-black text-amber-600 dark:text-amber-500 leading-none flex items-baseline gap-1">
                        <span dir="ltr">− {{ number_format((float)$calculatedInvestments, 2, ',', ' ') }}</span> <span class="text-sm font-bold">{{ getCurrency() }}</span>
                    </div>
                </div>

                <!-- Gains Input -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('caisse.month_gains') }} <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="text" inputmode="decimal" wire:model.live="closeGains" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white outline-none transition-all font-semibold" dir="ltr" placeholder="0.00">
                        <span class="absolute top-1/2 -translate-y-1/2 {{ session('locale', 'ar') === 'ar' ? 'left-4' : 'right-4' }} text-slate-400 dark:text-slate-500 font-bold text-sm">{{ getCurrency() }}</span>
                    </div>
                    @error('closeGains') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Note de clôture (optionnel) -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('caisse.closure_note', ['default' => 'Note de clôture (optionnel)']) }}</label>
                    <textarea wire:model="closeNote" rows="2" maxlength="2000"
                        class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white outline-none transition-all text-sm"
                        placeholder="{{ __('caisse.closure_note_placeholder', ['default' => 'Remarques sur ce mois : événements particuliers, ajustements, contexte… (optionnel)']) }}"></textarea>
                    @error('closeNote') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Live Balance Preview -->
                @if($closeGains !== '')
                @php $previewBalance = (float)str_replace([' ', ','], ['', '.'], $closeGains) + (float)$calculatedIncomes - (float)$calculatedExpenses - (float)$calculatedInvestments; @endphp
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800/60">
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">{{ __('caisse.preview_balance') }}</p>
                    <div class="text-3xl font-black flex items-baseline gap-1 {{ $previewBalance >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-500' }}">
                        <span dir="ltr">{{ $previewBalance > 0 ? '+' : '' }}{{ number_format($previewBalance, 2, ',', ' ') }}</span> <span class="text-sm font-bold">{{ getCurrency() }}</span>
                    </div>
                </div>
                @endif

                <!-- Warning -->
                <div class="bg-amber-50/50 dark:bg-amber-950/20 border border-amber-100/30 dark:border-amber-900/30 p-4 rounded-xl flex gap-3 text-amber-800 dark:text-amber-300 text-sm font-medium">
                    <svg class="w-5 h-5 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <p>{{ __('caisse.warning_lock') }}</p>
                </div>

                <div class="flex gap-3 justify-end pt-2 border-t border-slate-100 dark:border-slate-800/60">
                    <button type="button" @click="show = false" class="px-6 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors cursor-pointer border border-slate-200/50 dark:border-slate-700/50">{{ __('caisse.cancel') }}</button>
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 text-white font-semibold rounded-xl hover:bg-emerald-700 transition-colors flex items-center cursor-pointer">
                        <svg wire:loading wire:target="closeMonthSubmit" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="closeMonthSubmit">{{ __('caisse.submit') }}</span>
                        <span wire:loading wire:target="closeMonthSubmit">{{ __('caisse.submitting') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

