<div class="max-w-5xl mx-auto animate-fade-in">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-700 to-indigo-600 dark:from-blue-400 dark:to-indigo-300 tracking-tight font-heading">
                {{ $employeeId ? __('employees.edit') : __('employees.add') }}
            </h1>
            <p class="text-slate-500 dark:text-slate-500 text-sm mt-1.5 font-medium">{{ __('employees.title_desc') }}</p>
        </div>
        <a href="{{ route('employees.index') }}" class="inline-flex items-center justify-center bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 px-6 py-2.5 rounded-xl font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-300 shadow-sm">
            {{ __('common.back') }}
        </a>
    </div>

    @if($employeeId)
        <div class="mb-6 flex space-x-2 rtl:space-x-reverse border-b border-slate-200/60 dark:border-slate-700/60 pb-1 overflow-x-auto">
            <button wire:click="$set('activeTab', 'details')" class="px-5 py-3 font-bold text-sm transition-all rounded-t-xl {{ $activeTab === 'details' ? 'bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl text-blue-600 dark:text-blue-400 border-t border-x border-slate-200/60 dark:border-slate-800 shadow-[0_-4px_10px_rgb(0,0,0,0.02)] relative top-[1px]' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-50/50 dark:hover:bg-slate-800/50' }}">
                {{ __('employees.tab_details') }}
            </button>
            <button wire:click="$set('activeTab', 'advances')" class="px-5 py-3 font-bold text-sm transition-all rounded-t-xl {{ $activeTab === 'advances' ? 'bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl text-amber-600 dark:text-amber-400 border-t border-x border-slate-200/60 dark:border-slate-800 shadow-[0_-4px_10px_rgb(0,0,0,0.02)] relative top-[1px]' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-50/50 dark:hover:bg-slate-800/50' }}">
                {{ __('employees.tab_advances') }}
            </button>
            <button wire:click="$set('activeTab', 'payments')" class="px-5 py-3 font-bold text-sm transition-all rounded-t-xl {{ $activeTab === 'payments' ? 'bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl text-emerald-600 dark:text-emerald-400 border-t border-x border-slate-200/60 dark:border-slate-800 shadow-[0_-4px_10px_rgb(0,0,0,0.02)] relative top-[1px]' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-50/50 dark:hover:bg-slate-800/50' }}">
                {{ __('employees.tab_payments') }}
            </button>
        </div>
    @endif

    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 overflow-hidden relative z-10">
        @if($activeTab === 'details')
            <form wire:submit.prevent="save" class="p-6 md:p-10 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('employees.name') }} <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="name" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-white shadow-inner dark:shadow-none font-medium">
                        @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('employees.role_title') }}</label>
                        <input type="text" wire:model="role_title" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-white shadow-inner dark:shadow-none font-medium">
                        @error('role_title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('employees.email') }}</label>
                        <input type="email" wire:model="email" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-white shadow-inner dark:shadow-none font-medium" dir="ltr">
                        @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('employees.phone') }}</label>
                        <input type="text" wire:model="phone" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-white shadow-inner dark:shadow-none font-medium" dir="ltr">
                        @error('phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('employees.base_salary') }} <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" inputmode="decimal" step="0.01" wire:model="base_salary" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-white shadow-inner dark:shadow-none font-medium" dir="ltr">
                            <span class="absolute top-1/2 -translate-y-1/2 {{ session('locale', 'ar') === 'ar' ? 'left-4' : 'right-4' }} text-slate-400 dark:text-slate-500 font-bold text-sm">{{ getCurrency() }}</span>
                        </div>
                        @error('base_salary') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('employees.hired_at') }}</label>
                        <input type="date" wire:model="hired_at" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-white shadow-inner dark:shadow-none font-medium dark:[color-scheme:dark]">
                        @error('hired_at') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('employees.status') }} <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select wire:model="status" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-white appearance-none shadow-inner dark:shadow-none font-semibold">
                                <option value="active">{{ __('employees.active') }}</option>
                                <option value="inactive">{{ __('employees.inactive') }}</option>
                            </select>
                            <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 absolute {{ session('locale', 'ar') === 'ar' ? 'left-4' : 'right-4' }} top-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                        @error('status') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="pt-8 border-t border-slate-100 dark:border-slate-800 flex flex-col-reverse md:flex-row justify-end gap-3 md:gap-4 mt-8">
                    <a href="{{ route('employees.index') }}" class="w-full md:w-auto px-8 py-3 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors text-center border border-slate-200 dark:border-slate-700 shadow-sm">
                        {{ __('common.cancel') }}
                    </a>
                    <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center bg-gradient-to-r from-blue-600 to-blue-700 text-white px-10 py-3 rounded-xl font-bold shadow-lg shadow-blue-500/20 dark:shadow-blue-900/20 hover:shadow-blue-500/40 dark:hover:shadow-blue-900/40 hover:-translate-y-0.5 transition-all duration-300">
                        {{ $employeeId ? __('common.update') : __('common.save') }}
                    </button>
                </div>
            </form>
        
        @elseif($activeTab === 'advances' && $employeeRecord)
            <div class="p-6 md:p-10">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-1 bg-gradient-to-br from-amber-50/50 to-orange-50/50 dark:from-slate-800/40 dark:to-slate-800/30 p-6 rounded-2xl border border-amber-200/30 dark:border-slate-700/50 shadow-inner h-fit">
                        <h3 class="font-extrabold text-amber-600 dark:text-amber-400 mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ __('employees.add_advance') }}
                        </h3>
                        <form wire:submit.prevent="createAdvance" class="space-y-5">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('employees.amount') }}</label>
                                <input type="number" inputmode="decimal" step="0.01" wire:model="advanceAmount" class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all shadow-sm dark:text-white font-medium">
                                @error('advanceAmount') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('employees.date') }}</label>
                                <input type="date" wire:model="advanceDate" class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all shadow-sm dark:text-white font-medium dark:[color-scheme:dark]">
                                @error('advanceDate') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('employees.notes') }}</label>
                                <textarea wire:model="advanceNotes" class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all shadow-sm dark:text-white font-medium" rows="3"></textarea>
                            </div>
                            <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-bold rounded-xl hover:from-amber-600 hover:to-orange-600 shadow-md shadow-amber-500/20 transition-all hover:-translate-y-0.5">
                                {{ __('common.save') }}
                            </button>
                        </form>
                    </div>
                    
                    <div class="lg:col-span-2">
                        <h3 class="font-extrabold text-slate-800 dark:text-white mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ __('employees.advances_history') }}
                        </h3>
                        <div class="overflow-x-auto rounded-2xl border border-slate-200/50 dark:border-slate-800/60 shadow-sm">
                            <table class="min-w-full text-sm text-start {{ session('locale', 'ar') === 'ar' ? 'rtl:text-right' : 'ltr:text-left' }}">
                                <thead class="bg-slate-50/50 dark:bg-slate-950/30 text-slate-500 dark:text-slate-500 font-bold text-xs uppercase tracking-wider border-b border-slate-100 dark:border-slate-800/60">
                                    <tr>
                                        <th class="py-4 px-5">{{ __('employees.date') }}</th>
                                        <th class="py-4 px-5">{{ __('employees.amount') }}</th>
                                        <th class="py-4 px-5">{{ __('employees.status') }}</th>
                                        <th class="py-4 px-5 text-center">{{ __('common.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50 bg-transparent">
                                    @forelse($employeeRecord->advances()->latest()->get() as $adv)
                                        <tr class="hover:bg-slate-50/20 dark:hover:bg-slate-800/30 transition-colors">
                                            <td class="py-4 px-5 font-semibold text-slate-600 dark:text-slate-400 whitespace-nowrap">{{ $adv->date->format('Y-m-d') }}</td>
                                            <td class="py-4 px-5 font-bold text-orange-600 dark:text-orange-400 whitespace-nowrap" dir="ltr">{{ formatMoney($adv->amount) }} {{ getCurrency() }}</td>
                                            <td class="py-4 px-5 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $adv->status === 'deducted' ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-100/30 dark:border-emerald-800/30' : 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border border-amber-100/30 dark:border-amber-800/30' }}">
                                                    {{ $adv->status === 'deducted' ? __('employees.adv_status_deducted') : __('employees.adv_status_pending') }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-5 text-center">
                                                <button wire:click="deleteAdvance({{ $adv->id }})" wire:confirm="{{ __('common.confirm_delete') }}" class="text-slate-400 dark:text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 min-w-[44px] min-h-[44px] p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer inline-flex items-center justify-center" title="{{ __('common.delete') }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="py-12 text-center text-slate-400 dark:text-slate-500 font-medium">{{ __('employees.no_data') }}</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        @elseif($activeTab === 'payments' && $employeeRecord)
            <div class="p-6 md:p-10">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-1 bg-gradient-to-br from-emerald-50/50 to-teal-50/50 dark:from-slate-800/40 dark:to-slate-800/30 p-6 rounded-2xl border border-emerald-200/30 dark:border-slate-700/50 shadow-inner h-fit">
                        <h3 class="font-extrabold text-emerald-600 dark:text-emerald-400 mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            {{ __('employees.log_payment') }}
                        </h3>
                        
                        <div class="mb-6 p-4 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-2xl border border-emerald-100/30 dark:border-slate-700/30 shadow-sm text-sm space-y-3">
                            <div class="flex justify-between items-center text-slate-600 dark:text-slate-300">
                                <span class="font-semibold text-xs uppercase tracking-wider text-slate-500">{{ __('employees.base_salary_label') }}</span> 
                                <span class="font-bold text-slate-800 dark:text-white" dir="ltr">{{ formatMoney($employeeRecord->base_salary) }} {{ getCurrency() }}</span>
                            </div>
                            <div class="flex justify-between items-center text-amber-600 dark:text-amber-400 border-t border-slate-100 dark:border-slate-800/50 pt-2">
                                <span class="font-semibold text-xs uppercase tracking-wider text-amber-600/70">{{ __('employees.pending_advances_label') }}</span> 
                                <span class="font-bold" dir="ltr">{{ formatMoney($employeeRecord->active_advances_total) }} {{ getCurrency() }}</span>
                            </div>
                        </div>

                        <form wire:submit.prevent="createPayment" class="space-y-5">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('reports.month') }}</label>
                                    <input type="number" min="1" max="12" wire:model="paymentMonth" class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all shadow-sm text-center dark:text-white font-bold">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('reports.year') }}</label>
                                    <input type="number" wire:model="paymentYear" class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all shadow-sm text-center dark:text-white font-bold">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('employees.deduct_advance') }}</label>
                                <input type="number" inputmode="decimal" step="0.01" wire:model.live="paymentDeduction" class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all shadow-sm text-rose-500 dark:text-rose-400 font-bold" dir="ltr">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('expenses.payment_method') }}</label>
                                <div class="relative">
                                    <select wire:model="paymentMethod" class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all shadow-sm dark:text-white appearance-none font-semibold">
                                        <option value="cash">{{ __('payment_methods.cash') }}</option>
                                        <option value="bank_transfer">{{ __('payment_methods.bank_transfer') }}</option>
                                        <option value="check">{{ __('payment_methods.check') }}</option>
                                    </select>
                                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 absolute {{ session('locale', 'ar') === 'ar' ? 'left-4' : 'right-4' }} top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('employees.reference') }}</label>
                                <input type="text" wire:model="paymentNotes" class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all shadow-sm dark:text-white font-medium">
                            </div>
                            
                            <div class="pt-4 border-t border-emerald-100/50 dark:border-emerald-800/30 mt-4">
                                <div class="flex justify-between items-center bg-white dark:bg-slate-950 p-3.5 rounded-xl border border-emerald-100 dark:border-slate-800 shadow-inner">
                                    <span class="font-bold text-slate-700 dark:text-slate-300 text-xs uppercase tracking-wider">{{ __('employees.net') }}</span>
                                    <span class="text-emerald-600 dark:text-emerald-400 font-extrabold text-lg" dir="ltr">{{ formatMoney($employeeRecord->base_salary - (float)($paymentDeduction ?: 0)) }} {{ getCurrency() }}</span>
                                </div>
                            </div>
                            
                            <button type="submit" class="w-full mt-4 px-4 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold rounded-xl hover:from-emerald-600 hover:to-teal-600 shadow-md shadow-emerald-500/20 transition-all hover:-translate-y-0.5">
                                {{ __('common.save') }}
                            </button>
                        </form>
                    </div>
                    
                    <div class="lg:col-span-2">
                        <h3 class="font-extrabold text-slate-800 dark:text-white mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            {{ __('employees.payments_history') }}
                        </h3>
                        <div class="overflow-x-auto rounded-2xl border border-slate-200/50 dark:border-slate-800/60 shadow-sm">
                            <table class="min-w-full text-sm text-start {{ session('locale', 'ar') === 'ar' ? 'rtl:text-right' : 'ltr:text-left' }}">
                                <thead class="bg-slate-50/50 dark:bg-slate-950/30 text-slate-500 dark:text-slate-500 font-bold text-xs uppercase tracking-wider border-b border-slate-100 dark:border-slate-800/60">
                                    <tr>
                                        <th class="py-4 px-5">{{ __('reports.month') }}</th>
                                        <th class="py-4 px-5">{{ __('employees.base') }}</th>
                                        <th class="py-4 px-5 text-rose-500 dark:text-rose-400">{{ __('employees.deduction') }}</th>
                                        <th class="py-4 px-5 text-emerald-600 dark:text-emerald-400">{{ __('employees.net') }}</th>
                                        <th class="py-4 px-5">{{ __('employees.date') }}</th>
                                        <th class="py-4 px-5 text-center">{{ __('common.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50 bg-transparent">
                                    @forelse($employeeRecord->payments()->latest('paid_at')->get() as $pay)
                                        <tr class="hover:bg-slate-50/20 dark:hover:bg-slate-800/30 transition-colors">
                                            <td class="py-4 px-5 font-bold text-slate-700 dark:text-slate-200 whitespace-nowrap" dir="ltr">{{ str_pad($pay->month, 2, '0', STR_PAD_LEFT) }}/{{ $pay->year }}</td>
                                            <td class="py-4 px-5 text-slate-500 dark:text-slate-400 whitespace-nowrap" dir="ltr">{{ formatMoney($pay->base_amount) }}</td>
                                            <td class="py-4 px-5 font-bold text-rose-500 dark:text-rose-500 whitespace-nowrap" dir="ltr">-{{ formatMoney($pay->advances_deducted) }}</td>
                                            <td class="py-4 px-5 font-extrabold text-emerald-600 dark:text-emerald-400 whitespace-nowrap" dir="ltr">{{ formatMoney($pay->net_amount) }}</td>
                                            <td class="py-4 px-5 font-semibold text-slate-600 dark:text-slate-400 whitespace-nowrap">{{ $pay->paid_at->format('Y-m-d') }}</td>
                                            <td class="py-4 px-5 text-center whitespace-nowrap">
                                                <button wire:click="deletePayment({{ $pay->id }})" wire:confirm="{{ __('common.confirm_delete') }}" class="text-slate-400 dark:text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 min-w-[44px] min-h-[44px] p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer inline-flex items-center justify-center" title="{{ __('common.delete') }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="py-12 text-center text-slate-400 dark:text-slate-500 font-medium">{{ __('employees.no_data') }}</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

