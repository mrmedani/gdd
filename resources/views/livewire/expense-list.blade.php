<div class="space-y-6 animate-fade-in">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
        <div>
            <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-700 to-indigo-600 dark:from-blue-400 dark:to-indigo-300 tracking-tight font-heading">{{ __('nav.expenses') }}</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1.5 font-medium">{{ __('expenses.list_desc') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="exportCsv" wire:loading.attr="disabled" class="inline-flex items-center justify-center bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/30 px-4 py-2.5 rounded-xl font-bold hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-all duration-200 shadow-sm cursor-pointer text-sm">
                <svg wire:loading wire:target="exportCsv" class="w-4 h-4 me-2 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <svg wire:loading.remove wire:target="exportCsv" class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                CSV
            </button>
            <button wire:click="$set('showImportModal', true)" class="inline-flex items-center justify-center bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400 border border-indigo-200/50 dark:border-indigo-800/30 px-4 py-2.5 rounded-xl font-bold hover:bg-indigo-100 dark:hover:bg-indigo-900/40 transition-all duration-200 shadow-sm cursor-pointer text-sm">
                <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
                {{ __('expenses.import_csv') }}
            </button>
            <a href="{{ route('expenses.create') }}" class="inline-flex items-center justify-center bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-500/20 hover:shadow-blue-500/40 hover:-translate-y-0.5 transition-all duration-200">
                <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('expenses.add') }}
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 p-6 relative overflow-hidden group hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50/20 dark:bg-blue-500/5 rounded-full blur-3xl -mr-16 -mt-16 z-0 pointer-events-none"></div>
        <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-5">
            <div>
                <label class="block text-xs font-bold text-slate-400 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('expenses.date_from') }}</label>
                <input type="date" wire:model="searchDateFrom" wire:change="$refresh" class="w-full px-4 py-2.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none text-slate-700 dark:text-slate-300 dark:[color-scheme:dark] shadow-inner dark:shadow-none font-medium">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-400 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('expenses.date_to') }}</label>
                <input type="date" wire:model="searchDateTo" wire:change="$refresh" class="w-full px-4 py-2.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none text-slate-700 dark:text-slate-300 dark:[color-scheme:dark] shadow-inner dark:shadow-none font-medium">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-400 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('expenses.category') }}</label>
                <div class="relative">
                    <select wire:model="searchCategory" wire:change="$refresh" class="w-full px-4 py-2.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none text-slate-700 dark:text-slate-300 appearance-none shadow-inner dark:shadow-none font-semibold">
                        <option value="">{{ __('common.all') }}</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->translated_name }}</option>
                        @endforeach
                    </select>
                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 absolute {{ session('locale', 'ar') === 'ar' ? 'left-4' : 'right-4' }} top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-400 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('expenses.min_amount') }}</label>
                <input type="number" inputmode="decimal" step="0.01" wire:model="searchAmountMin" wire:change="$refresh" class="w-full px-4 py-2.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none text-slate-700 dark:text-slate-300 dark:placeholder-slate-600 shadow-inner dark:shadow-none font-semibold" placeholder="0.00">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-400 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('expenses.max_amount') }}</label>
                <input type="number" inputmode="decimal" step="0.01" wire:model="searchAmountMax" wire:change="$refresh" class="w-full px-4 py-2.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none text-slate-700 dark:text-slate-300 dark:placeholder-slate-600 shadow-inner dark:shadow-none font-semibold" placeholder="10000.00">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-400 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('expenses.payment_method') }}</label>
                <div class="relative">
                    <select wire:model="searchPaymentMethod" wire:change="$refresh" class="w-full px-4 py-2.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none text-slate-700 dark:text-slate-300 appearance-none shadow-inner dark:shadow-none font-semibold">
                        <option value="">{{ __('common.all') }}</option>
                        @foreach($paymentMethods as $pm)
                            <option value="{{ $pm['value'] }}">{{ $pm['label'] }}</option>
                        @endforeach
                    </select>
                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 absolute {{ session('locale', 'ar') === 'ar' ? 'left-4' : 'right-4' }} top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div wire:loading.delay.long wire:target="searchDateFrom,searchDateTo,searchCategory,searchAmountMin,searchAmountMax,searchPaymentMethod" class="mb-6 flex justify-center">
        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/80 dark:bg-slate-900/80 backdrop-blur border border-blue-100 dark:border-blue-900/40 text-blue-600 dark:text-blue-400 shadow-premium text-xs font-semibold">
            <svg class="w-4.5 h-4.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            {{ __('common.loading') }}...
        </span>
    </div>

    <!-- Data Table -->
    <div wire:key="expense-table" class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 overflow-hidden relative z-10 hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
        @if(count($selectedExpenses) > 0)
            <div class="flex items-center gap-3 px-6 py-3 bg-blue-50 dark:bg-blue-900/20 border-b border-blue-100 dark:border-blue-800/30">
                <span class="text-sm font-bold text-blue-700 dark:text-blue-400">{{ count($selectedExpenses) }} sélectionnée(s)</span>
                        <button type="button" wire:click="deleteSelected" wire:confirm="Supprimer ces {{ count($selectedExpenses) }} dépenses ?" class="px-4 py-1.5 bg-red-600 text-white text-xs font-bold rounded-lg hover:bg-red-700 transition-colors cursor-pointer">
                    Supprimer la sélection
                </button>
                <button wire:click="$set('selectedExpenses', [])" class="px-4 py-1.5 bg-slate-200 dark:bg-slate-700 text-xs font-bold rounded-lg hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors cursor-pointer">
                    Annuler
                </button>
            </div>
        @endif
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-start {{ session('locale', 'ar') === 'ar' ? 'rtl:text-right' : 'ltr:text-left' }}">
                <thead class="bg-slate-50/50 dark:bg-slate-950/30 text-slate-400 dark:text-slate-500 font-bold text-xs uppercase tracking-wider border-b border-slate-100 dark:border-slate-800/60">
                    <tr>
                        <th class="py-4 px-4 w-12">
                            <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 rounded text-blue-600 border-slate-300 dark:border-slate-700 dark:bg-slate-950 focus:ring-blue-500/20 cursor-pointer">
                        </th>
                        <th class="py-4 px-6">{{ __('expenses.date') }}</th>
                        <th class="py-4 px-6">{{ __('expenses.description') }}</th>
                        <th class="py-4 px-6">{{ __('expenses.category') }}</th>
                        <th class="py-4 px-6">{{ __('expenses.amount') }}</th>
                        <th class="py-4 px-6">{{ __('expenses.payment_method') }}</th>
                        <th class="py-4 px-6">{{ __('expenses.employee') }}</th>
                        <th class="py-4 px-6">{{ __('expenses.receipt') }}</th>
                        <th class="py-4 px-6 text-center">{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50 bg-transparent">
                    @forelse($expenses as $expense)
                        <tr class="hover:bg-blue-50/20 dark:hover:bg-slate-800/30 transition-colors group">
                            <td class="py-4 px-4">
                                <input type="checkbox" wire:model.live="selectedExpenses" value="{{ $expense->id }}" class="w-4 h-4 rounded text-blue-600 border-slate-300 dark:border-slate-700 dark:bg-slate-950 focus:ring-blue-500/20 cursor-pointer">
                            </td>
                            <td class="py-4 px-6 font-semibold text-slate-600 dark:text-slate-400 whitespace-nowrap">{{ $expense->date->format('Y-m-d') }}</td>
                            <td class="py-4 px-6 text-slate-700 dark:text-slate-300 max-w-xs truncate font-medium" title="{{ $expense->description }}">{{ $expense->description }}</td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-900/25 text-blue-700 dark:text-blue-400 border border-blue-100/30 dark:border-blue-800/30">
                                    @if($expense->category)
                                        {{ $expense->category->translated_name }}
                                    @else
                                        {{ __("categories.{$expense->category_key}") }}
                                    @endif
                                </span>
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap">
                                <span class="font-bold text-slate-900 dark:text-white">{{ formatMoney($expense->amount) }}</span>
                                <span class="text-xs text-slate-400 dark:text-slate-500 ms-1 font-semibold">{{ getCurrency() }}</span>
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap">
                                <span class="inline-flex items-center text-slate-600 dark:text-slate-400 gap-1.5 font-medium">
                                    @if($expense->payment_method === 'cash')
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    @else
                                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                    @endif
                                    {{ __("payment_methods.{$expense->payment_method}") }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-slate-600 dark:text-slate-500 whitespace-nowrap">
                                @if($expense->employee)
                                    <span class="inline-flex items-center gap-1.5 font-medium">
                                        <svg class="w-4.5 h-4.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        {{ $expense->employee->name }}
                                    </span>
                                @else
                                    <span class="text-slate-300 dark:text-slate-700">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap">
                                @if($expense->receipt_path)
                                    <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank" class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-xs font-bold bg-blue-50 dark:bg-blue-900/20 px-3 py-1.5 rounded-xl border border-blue-100/30 dark:border-blue-800/30 transition-all duration-200">
                                        <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                        {{ __('expenses.receipt_attached') }}
                                    </a>
                                @else
                                    <span class="text-slate-300 dark:text-slate-700">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center gap-3 md:opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    @can('update', $expense)
                                        <a href="{{ route('expenses.edit', $expense->id) }}" class="text-slate-400 dark:text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors tooltip-trigger min-w-[44px] min-h-[44px] p-1 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg inline-flex items-center justify-center" title="{{ __('common.edit') }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                    @endcan
                                    @can('delete', $expense)
                                        <button type="button" wire:click="delete({{ $expense->id }})" wire:confirm="{{ __('expenses.confirm_delete') }}" class="text-slate-400 dark:text-slate-500 hover:text-red-600 dark:hover:text-red-400 transition-colors tooltip-trigger min-w-[44px] min-h-[44px] p-1 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg cursor-pointer inline-flex items-center justify-center" title="{{ __('common.delete') }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 bg-slate-50 dark:bg-slate-900/50 rounded-full flex items-center justify-center mb-4 border border-slate-200/50 dark:border-slate-800/60">
                                        <svg class="w-10 h-10 text-slate-300 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                    </div>
                                    <p class="text-slate-500 dark:text-slate-400 font-bold text-lg">{{ __('expenses.no_expenses') }}</p>
                                    <p class="text-slate-400 dark:text-slate-500 text-sm mt-1.5 font-semibold">{{ __('expenses.no_expenses_desc') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-900/30">
            <div wire:loading.delay wire:target="gotoPage,previousPage,nextPage" class="text-center text-slate-400 dark:text-slate-500 py-2 text-sm font-semibold">
                {{ __('common.loading') }}...
            </div>
            <div wire:loading.remove wire:target="gotoPage,previousPage,nextPage">
                {{ $expenses->links() }}
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    @if($showImportModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" wire:click.self="$set('showImportModal', false)">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200/50 dark:border-slate-800/60 w-full max-w-lg p-6 animate-fade-in">
            <div class="flex items-start justify-between mb-5">
                <div>
                    <h3 class="text-xl font-extrabold text-slate-800 dark:text-white font-heading">{{ __('expenses.import_csv') }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1.5 font-medium">{{ __('expenses.import_csv_desc') }}</p>
                </div>
                <button wire:click="$set('showImportModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors p-1 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            @if($showImportPreview)
                {{-- Preview --}}
                <div class="mb-4 max-h-48 overflow-y-auto">
                    <table class="min-w-full text-xs">
                        <thead><tr class="text-slate-400 font-bold uppercase tracking-wider"><th class="py-2 px-3 text-start">Date</th><th class="py-2 px-3 text-start">Description</th><th class="py-2 px-3 text-start">Catégorie</th><th class="py-2 px-3 text-end">Montant</th></tr></thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($importPreviewRows as $row)
                            <tr class="text-slate-600 dark:text-slate-400"><td class="py-2 px-3">{{ $row['date'] }}</td><td class="py-2 px-3 font-medium">{{ $row['description'] }}</td><td class="py-2 px-3">{{ $row['category'] }}</td><td class="py-2 px-3 text-end font-bold">{{ number_format($row['amount'], 2) }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="flex items-center gap-3 justify-end">
                    <button type="button" wire:click="cancelImport" class="px-5 py-2.5 text-sm font-bold text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors cursor-pointer">{{ __('common.cancel') }}</button>
                    <button type="button" wire:click="confirmImport" wire:loading.attr="disabled" class="px-6 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40 transition-all cursor-pointer">
                        <span wire:loading.remove wire:target="confirmImport">Confirmer l'import ({{ count($importPreviewRows) }}+ lignes)</span>
                        <span wire:loading wire:target="confirmImport">{{ __('common.loading') }}...</span>
                    </button>
                </div>
            @else
                <form wire:submit="previewCsv">
                    <div class="mb-5">
                        <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 mb-2 uppercase tracking-wider">{{ __('expenses.csv_file') }}</label>
                        <div class="border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl p-6 text-center hover:border-blue-300 dark:hover:border-blue-700 transition-colors">
                            <div wire:loading.remove wire:target="importFile">
                                <svg class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                <p class="text-sm text-slate-400 dark:text-slate-500 font-medium">{{ __('expenses.drop_file') }}</p>
                                <p class="text-xs text-slate-300 dark:text-slate-600 mt-1">{{ __('expenses.csv_columns_hint') }}</p>
                            </div>
                            <div wire:loading wire:target="importFile">
                                <div class="flex items-center justify-center gap-3">
                                    <svg class="w-6 h-6 text-blue-500 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span class="text-sm text-blue-500 font-semibold">{{ __('common.loading') }}...</span>
                                </div>
                            </div>
                            <input type="file" wire:model="importFile" accept=".csv,.txt" class="mt-4 block w-full text-sm text-slate-500 dark:text-slate-400 file:me-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-blue-50 dark:file:bg-blue-900/30 file:text-blue-700 dark:file:text-blue-400 hover:file:bg-blue-100 dark:hover:file:bg-blue-900/50 cursor-pointer">
                        </div>
                        @error('importFile') <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center gap-3 justify-end">
                        <button type="button" wire:click="$set('showImportModal', false)" class="px-5 py-2.5 text-sm font-bold text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors cursor-pointer">
                            {{ __('common.cancel') }}
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl font-bold text-sm shadow-lg shadow-blue-500/20 hover:shadow-blue-500/40 hover:-translate-y-0.5 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="previewCsv">{{ __('expenses.preview') }}</span>
                            <span wire:loading wire:target="previewCsv">{{ __('common.loading') }}...</span>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
    @endif
</div>

