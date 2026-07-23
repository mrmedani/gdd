<div class="space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800 dark:text-white tracking-tight font-heading">{{ __('nav.expenses') }}</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1.5 font-medium">{{ __('expenses.list_desc') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="exportCsv" wire:loading.attr="disabled" class="inline-flex items-center justify-center bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 px-4 py-2.5 rounded-xl font-semibold hover:bg-emerald-50 dark:hover:bg-emerald-900/30 cursor-pointer text-sm transition-colors">
                <svg wire:loading wire:target="exportCsv" class="w-4 h-4 me-2 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <svg wire:loading.remove wire:target="exportCsv" class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                CSV
            </button>
            <button wire:click="$set('showImportModal', true)" class="inline-flex items-center justify-center bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 px-4 py-2.5 rounded-xl font-semibold hover:bg-indigo-50 dark:hover:bg-indigo-900/30 cursor-pointer text-sm transition-colors">
                <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
                {{ __('expenses.import_csv') }}
            </button>
            <a href="{{ route('expenses.create') }}" class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-semibold shadow-sm transition-colors">
                <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('expenses.add') }}
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Filtres & Recherche
            </h2>
            @if($searchDateFrom || $searchDateTo || $searchCategory || $searchAmountMin || $searchAmountMax || $searchKeyword)
                <button wire:click="$set('searchDateFrom', ''); $set('searchDateTo', ''); $set('searchCategory', ''); $set('searchAmountMin', ''); $set('searchAmountMax', ''); $set('searchKeyword', '');" class="text-xs font-bold text-rose-500 hover:text-rose-600 dark:hover:text-rose-400 transition-colors flex items-center gap-1.5 cursor-pointer bg-rose-50 dark:bg-rose-950/40 px-3 py-1 rounded-full border border-rose-200/50 dark:border-rose-900/30">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Réinitialiser les filtres
                </button>
            @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 sm:gap-5">
            <div>
                <label class="block text-xs font-bold text-slate-400 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('expenses.date_from') }}</label>
                <div class="relative">
                    <input type="date" wire:model="searchDateFrom" wire:change="$refresh" class="w-full ps-10 pe-4 py-2.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none text-slate-700 dark:text-slate-300 [color-scheme:light] dark:[color-scheme:dark] shadow-inner dark:shadow-none font-medium cursor-pointer [&::-webkit-calendar-picker-indicator]:bg-transparent [&::-webkit-calendar-picker-indicator]:opacity-0 [&::-webkit-calendar-picker-indicator]:absolute [&::-webkit-calendar-picker-indicator]:inset-0 [&::-webkit-calendar-picker-indicator]:w-full [&::-webkit-calendar-picker-indicator]:h-full [&::-webkit-calendar-picker-indicator]:cursor-pointer">
                    <svg class="w-4 h-4 text-slate-400 absolute start-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-400 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('expenses.date_to') }}</label>
                <div class="relative">
                    <input type="date" wire:model="searchDateTo" wire:change="$refresh" class="w-full ps-10 pe-4 py-2.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none text-slate-700 dark:text-slate-300 [color-scheme:light] dark:[color-scheme:dark] shadow-inner dark:shadow-none font-medium cursor-pointer [&::-webkit-calendar-picker-indicator]:bg-transparent [&::-webkit-calendar-picker-indicator]:opacity-0 [&::-webkit-calendar-picker-indicator]:absolute [&::-webkit-calendar-picker-indicator]:inset-0 [&::-webkit-calendar-picker-indicator]:w-full [&::-webkit-calendar-picker-indicator]:h-full [&::-webkit-calendar-picker-indicator]:cursor-pointer">
                    <svg class="w-4 h-4 text-slate-400 absolute start-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-400 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('expenses.category') }}</label>
                <div class="relative">
                    <select wire:model="searchCategory" wire:change="$refresh" class="w-full ps-10 pe-10 py-2.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none text-slate-700 dark:text-slate-300 appearance-none shadow-inner dark:shadow-none font-semibold cursor-pointer">
                        <option value="" class="bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 font-semibold">{{ __('common.all') }}</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" class="bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 font-semibold">{{ $cat->translated_name }}</option>
                        @endforeach
                    </select>
                    <svg class="w-4 h-4 text-slate-400 absolute start-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    <svg class="w-4 h-4 text-slate-400 absolute end-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-400 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('expenses.min_amount') }}</label>
                <div class="relative">
                    <input type="number" inputmode="decimal" step="0.01" wire:model="searchAmountMin" wire:change="$refresh" class="w-full ps-10 pe-4 py-2.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none text-slate-700 dark:text-slate-300 dark:placeholder-slate-600 shadow-inner dark:shadow-none font-semibold [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none [-moz-appearance:textfield]" placeholder="0.00">
                    <svg class="w-4 h-4 text-slate-400 absolute start-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-400 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('expenses.max_amount') }}</label>
                <div class="relative">
                    <input type="number" inputmode="decimal" step="0.01" wire:model="searchAmountMax" wire:change="$refresh" class="w-full ps-10 pe-4 py-2.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none text-slate-700 dark:text-slate-300 dark:placeholder-slate-600 shadow-inner dark:shadow-none font-semibold [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none [-moz-appearance:textfield]" placeholder="10000.00">
                    <svg class="w-4 h-4 text-slate-400 absolute start-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-400 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('common.search') }}</label>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="searchKeyword" class="w-full ps-10 pe-4 py-2.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none text-slate-700 dark:text-slate-300 dark:placeholder-slate-600 shadow-inner dark:shadow-none font-medium" placeholder="{{ __('expenses.search_placeholder') }}">
                    <svg class="w-4 h-4 text-slate-400 absolute start-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div wire:loading.delay.long wire:target="searchDateFrom,searchDateTo,searchCategory,searchAmountMin,searchAmountMax,searchKeyword" class="mb-6 flex justify-center">
        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white dark:bg-slate-900 border border-blue-100 dark:border-blue-900/40 text-blue-600 dark:text-blue-400 shadow-sm text-xs font-semibold">
            <svg class="w-4.5 h-4.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            {{ __('common.loading') }}...
        </span>
    </div>

    <!-- Data Table -->
    <div wire:key="expense-table" class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <!-- Old selection banner removed for new Floating Action Bar -->
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
                        <tr class="hover:bg-blue-50/40 dark:hover:bg-slate-800/40 transition-all duration-300 group hover:shadow-sm">
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
                                <span class="font-bold text-blue-600 dark:text-blue-400 font-mono tracking-tight text-base">{{ formatMoney($expense->amount) }}</span>
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
                                    <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank" class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-xs font-semibold bg-blue-50 dark:bg-blue-900/20 px-3 py-1.5 rounded-lg transition-colors">
                                        <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                        {{ __('expenses.receipt_attached') }}
                                    </a>
                                @else
                                    <span class="text-slate-300 dark:text-slate-700">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center gap-2 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-all duration-300 translate-y-0 md:translate-y-2 md:group-hover:translate-y-0">
                                    @can('update', $expense)
                                        <a href="{{ route('expenses.edit', $expense->id) }}" class="text-indigo-500 hover:text-white transition-colors w-9 h-9 hover:bg-indigo-500 rounded-full inline-flex items-center justify-center" title="{{ __('common.edit') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                    @endcan
                                    @can('delete', $expense)
                                        <button type="button" wire:click="delete({{ $expense->id }})" wire:confirm="{{ __('expenses.confirm_delete') }}" class="text-red-500 hover:text-white transition-colors w-9 h-9 hover:bg-red-500 rounded-full cursor-pointer inline-flex items-center justify-center" title="{{ __('common.delete') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-24 text-center">
                                <div class="flex flex-col items-center justify-center max-w-md mx-auto">
                                    <div class="w-16 h-16 mb-4 flex items-center justify-center bg-blue-50 dark:bg-blue-900/20 rounded-full">
                                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <h3 class="text-xl font-extrabold text-slate-800 dark:text-white mb-2">{{ __('expenses.no_expenses') }}</h3>
                                    <p class="text-slate-500 dark:text-slate-400 font-medium text-sm leading-relaxed">{{ __('expenses.no_expenses_desc') }}</p>
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

    <!-- Floating Action Bar (Dynamic Island style) -->
    @if(count($selectedExpenses) > 0)
        <div class="fixed bottom-8 left-1/2 -translate-x-1/2 z-50 animate-slide-up-fade">
            <div class="bg-slate-900 dark:bg-slate-950 border border-slate-700 dark:border-slate-800 rounded-2xl shadow-lg p-2 flex items-center gap-4">
                <div class="px-4 py-2 bg-blue-500/20 dark:bg-blue-500/10 rounded-full flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    <span class="text-sm font-semibold text-blue-400">{{ count($selectedExpenses) }} sélection(s)</span>
                </div>
                <div class="flex items-center gap-2 pr-2">
                    <button type="button" wire:click="deleteSelected" wire:confirm="Supprimer définitivement ces {{ count($selectedExpenses) }} dépenses ?" class="px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition-colors cursor-pointer flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Supprimer
                    </button>
                    <button wire:click="$set('selectedExpenses', [])" class="p-2.5 text-slate-400 hover:text-white hover:bg-slate-800 rounded-xl transition-colors cursor-pointer" title="Annuler la sélection">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <style>
        .animate-slide-up-fade { animation: slideUpFade 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes slideUpFade { 0% { opacity: 0; transform: translate(-50%, 20px) scale(0.95); } 100% { opacity: 1; transform: translate(-50%, 0) scale(1); } }
    </style>

    <!-- Import Modal -->
    @if($showImportModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" wire:click.self="$set('showImportModal', false)">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-800 w-full max-w-lg p-6">
            <div class="flex items-start justify-between mb-5">
                <div>
                    <h3 class="text-xl font-extrabold text-slate-800 dark:text-white">{{ __('expenses.import_csv') }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1.5 font-medium">{{ __('expenses.import_csv_desc') }}</p>
                </div>
                <button wire:click="$set('showImportModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors p-1 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            @if($showImportPreview)
                {{-- Preview --}}
                <div class="mb-4 max-h-56 overflow-y-auto rounded-xl border border-slate-200/50 dark:border-slate-800/60 shadow-inner custom-scrollbar">
                    <table class="min-w-full text-xs text-left">
                        <thead class="sticky top-0 bg-slate-100/90 dark:bg-slate-800/90 backdrop-blur-md z-10"><tr class="text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider"><th class="py-3 px-4">Date</th><th class="py-3 px-4">Description</th><th class="py-3 px-4">Catégorie</th><th class="py-3 px-4 text-right">Montant</th></tr></thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-slate-900">
                            @foreach($importPreviewRows as $row)
                            <tr class="text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors"><td class="py-3 px-4 whitespace-nowrap">{{ $row['date'] }}</td><td class="py-3 px-4 font-medium">{{ $row['description'] }}</td><td class="py-3 px-4">
                                <span class="px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded text-[10px] font-bold">{{ $row['category'] }}</span>
                            </td><td class="py-3 px-4 text-right font-bold text-slate-800 dark:text-white">{{ number_format($row['amount'], 2) }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="flex items-center gap-3 justify-end mt-6">
                    <button type="button" wire:click="cancelImport" class="px-6 py-2.5 text-sm font-semibold text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors cursor-pointer">{{ __('common.cancel') }}</button>
                    <button type="button" wire:click="confirmImport" wire:loading.attr="disabled" class="px-8 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold text-sm transition-colors cursor-pointer flex items-center">
                        <span wire:loading.remove wire:target="confirmImport" class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Confirmer l'import ({{ count($importPreviewRows) }} lignes)
                        </span>
                        <span wire:loading wire:target="confirmImport" class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            {{ __('common.loading') }}...
                        </span>
                    </button>
                </div>
            @else
                <form wire:submit="previewCsv">
                    <div class="mb-5">
                        <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 mb-2 uppercase tracking-wider">{{ __('expenses.csv_file') }}</label>
                        <div class="relative border-2 border-dashed border-indigo-200 dark:border-indigo-800/60 rounded-xl p-8 text-center hover:border-indigo-400 dark:hover:border-indigo-500 hover:bg-indigo-50/50 dark:hover:bg-indigo-900/10 transition-colors cursor-pointer overflow-hidden">
                            <div wire:loading.remove wire:target="importFile" class="relative">
                                <div class="w-14 h-14 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-500 dark:text-indigo-400 rounded-xl flex items-center justify-center mx-auto mb-4 transition-colors">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                </div>
                                <p class="text-base text-slate-700 dark:text-slate-300 font-bold">{{ __('expenses.drop_file') }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-500 mt-2 max-w-xs mx-auto">{{ __('expenses.csv_columns_hint') }}</p>
                            </div>
                            <div wire:loading wire:target="importFile" class="py-4">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <svg class="w-8 h-8 text-indigo-500 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span class="text-sm text-indigo-500 font-bold">{{ __('common.loading') }}...</span>
                                </div>
                            </div>
                            <input type="file" wire:model="importFile" accept=".csv,.txt" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                        </div>
                        @error('importFile') <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center gap-3 justify-end">
                        <button type="button" wire:click="$set('showImportModal', false)" class="px-5 py-2.5 text-sm font-semibold text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors cursor-pointer">
                            {{ __('common.cancel') }}
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold text-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
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

