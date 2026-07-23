<div class="max-w-6xl mx-auto space-y-8 animate-fade-in">
    <div class="mb-4">
        <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-700 to-indigo-600 dark:from-blue-400 dark:to-indigo-300 tracking-tight font-heading">
            {{ __('nav.reports') }}
        </h1>
        <p class="text-slate-500 dark:text-slate-500 text-sm mt-1.5 font-medium">{{ __('reports.index_desc') }}</p>
    </div>

    <!-- Monthly Report -->
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 p-8 relative group hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50/20 dark:bg-blue-500/5 rounded-full blur-3xl -mr-16 -mt-16 z-0 pointer-events-none"></div>

        <div class="flex items-center gap-3 mb-6 relative z-10">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-2xl flex items-center justify-center border border-blue-200/20 dark:border-blue-800 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <h2 class="text-lg font-bold text-slate-800 dark:text-white">{{ __('reports.monthly_report') }}</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 relative z-10">
            <div class="lg:col-span-2">
                <form action="{{ route('reports.monthly.pdf') }}" method="POST" target="_blank">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('reports.period') }}</label>
                            <div class="relative">
                                <select wire:model.live="selectedPeriod"
                                        class="w-full pl-10 pr-10 py-2.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none text-slate-700 dark:text-slate-300 appearance-none shadow-inner font-semibold">
                                    @foreach($periods as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <svg class="w-4 h-4 text-slate-400 absolute left-4 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <svg class="w-4 h-4 text-slate-400 absolute right-4 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                            <p class="text-xs text-slate-400 dark:text-slate-500 font-medium mt-2">
                                {{ __('reports.period_from') }} <strong>{{ $previewPeriodStart }}</strong> {{ __('reports.period_to') }} <strong>{{ $previewPeriodEnd }}</strong>
                            </p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('expenses.category') }}</label>
                            <select wire:model.live="filterCategory" name="category_id" class="w-full px-4 py-2.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none text-slate-700 dark:text-slate-300 appearance-none shadow-inner font-semibold">
                                <option value="">{{ __('common.all') }}</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->translated_name }}</option>
                                    @foreach($cat->children as $child)
                                        <option value="{{ $child->id }}">&nbsp;&nbsp;└ {{ $child->translated_name }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('employees.employee') }}</label>
                            <select wire:model.live="filterEmployee" name="employee_id" class="w-full px-4 py-2.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none text-slate-700 dark:text-slate-300 appearance-none shadow-inner font-semibold">
                                <option value="">{{ __('common.all') }}</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('expenses.payment_method') }}</label>
                            <select wire:model.live="filterPaymentMethod" name="payment_method" class="w-full px-4 py-2.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none text-slate-700 dark:text-slate-300 appearance-none shadow-inner font-semibold">
                                <option value="">{{ __('common.all') }}</option>
                                @foreach($paymentMethods as $pm)
                                    <option value="{{ $pm->value }}">{{ __("payment_methods.{$pm->value}") }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 bg-gradient-to-r from-rose-500 to-rose-600 text-white px-4 py-3 rounded-xl font-bold shadow-lg shadow-rose-500/20 hover:shadow-rose-500/40 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            {{ __('reports.download_pdf') }}
                        </button>
                        <button type="submit" formaction="{{ route('reports.monthly.excel') }}" class="flex-1 inline-flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white px-4 py-3 rounded-xl font-bold shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            {{ __('reports.download_excel') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Preview -->
            <div class="bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200/50 dark:border-slate-800/60 rounded-2xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ __('reports.preview') }}</h3>
                    <span class="text-xs text-slate-400 dark:text-slate-500 font-semibold">{{ $previewPeriodLabel }}</span>
                </div>

                @if($previewCount > 0 || $previewGains > 0)
                    <div class="space-y-3">
                        <div class="flex items-center justify-between py-2 border-b border-slate-200/50 dark:border-slate-800/50">
                            <span class="text-sm text-slate-500 dark:text-slate-400 font-medium">{{ __('reports.total_gains') }}</span>
                            <span class="text-lg font-black text-emerald-600 dark:text-emerald-400">{{ formatMoney($previewGains) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-slate-500 dark:text-slate-400 font-medium">{{ __('reports.total_expenses') }}</span>
                            <span class="text-lg font-black text-rose-600 dark:text-rose-400">{{ formatMoney($previewTotal) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-t border-slate-200/50 dark:border-slate-800/50">
                            <span class="text-sm text-slate-500 dark:text-slate-400 font-medium">{{ __('reports.balance') }}</span>
                            <span class="text-lg font-black {{ $previewBalance >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ formatMoney($previewBalance) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-slate-500 dark:text-slate-400 font-medium">{{ __('reports.expense_count') }}</span>
                            <span class="text-lg font-black text-slate-900 dark:text-white">{{ $previewCount }}</span>
                        </div>

                        @if(count($previewByCategory) > 0)
                            <div class="pt-2 border-t border-slate-200/50 dark:border-slate-800/50">
                                <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">{{ __('dashboard.by_category') }}</p>
                                <div class="space-y-1.5">
                                    @foreach(array_slice($previewByCategory, 0, 4) as $cat)
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-slate-600 dark:text-slate-400 truncate max-w-[140px] font-medium">{{ $cat['name'] }}</span>
                                            <div class="flex items-center gap-2">
                                                <span class="text-slate-400 dark:text-slate-500 font-semibold">{{ $cat['percentage'] }}%</span>
                                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ formatMoney($cat['total']) }}</span>
                                            </div>
                                        </div>
                                        <div class="w-full h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                            <div class="h-full bg-blue-500 rounded-full" style="width: {{ $cat['percentage'] }}%"></div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-8">
                        <svg class="w-10 h-10 text-slate-300 dark:text-slate-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        <p class="text-xs text-slate-400 dark:text-slate-500 font-medium">{{ __('reports.no_data') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Annual Report -->
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 p-8 relative group hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
        <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50/20 dark:bg-indigo-500/5 rounded-full blur-3xl -mr-16 -mt-16 z-0 pointer-events-none"></div>

        <div class="flex items-center gap-3 mb-6 relative z-10">
            <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-2xl flex items-center justify-center border border-indigo-200/20 dark:border-indigo-800 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            </div>
            <h2 class="text-lg font-bold text-slate-800 dark:text-white">{{ __('reports.annual_report') }}</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 relative z-10">
            <div class="lg:col-span-2">
                <form action="{{ route('reports.annual.pdf') }}" method="POST" target="_blank">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('reports.year') }}</label>
                            <div class="relative">
                                <select name="year" wire:model.live="annualYear" class="w-full px-4 py-2.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none text-slate-700 dark:text-slate-300 appearance-none shadow-inner font-semibold">
                                    @foreach($years as $y)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endforeach
                                </select>
                                <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 absolute {{ session('locale', 'ar') === 'ar' ? 'left-4' : 'right-4' }} top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('expenses.category') }}</label>
                            <select name="category_id" class="w-full px-4 py-2.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none text-slate-700 dark:text-slate-300 appearance-none shadow-inner font-semibold">
                                <option value="">{{ __('common.all') }}</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->translated_name }}</option>
                                    @foreach($cat->children as $child)
                                        <option value="{{ $child->id }}">&nbsp;&nbsp;└ {{ $child->translated_name }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 bg-gradient-to-r from-rose-500 to-rose-600 text-white px-4 py-3 rounded-xl font-bold shadow-lg shadow-rose-500/20 hover:shadow-rose-500/40 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            {{ __('reports.download_pdf') }}
                        </button>
                        <button type="submit" formaction="{{ route('reports.annual.excel') }}" class="flex-1 inline-flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white px-4 py-3 rounded-xl font-bold shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            {{ __('reports.download_excel') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
