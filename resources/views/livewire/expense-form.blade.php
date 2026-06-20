<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-700 to-indigo-600 dark:from-blue-400 dark:to-indigo-300 tracking-tight font-heading">
                {{ $expenseId ? __('expenses.edit') : __('expenses.add') }}
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1.5 font-medium">{{ __('expenses.form_desc') }}</p>
        </div>
        <a href="{{ route('expenses.index') }}" class="inline-flex items-center justify-center bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 px-6 py-2.5 rounded-xl font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-300 shadow-sm">
            {{ __('common.back') }}
        </a>
    </div>

    <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.2)] border border-white/60 dark:border-slate-700/60 p-6 md:p-10 relative z-10 max-w-4xl">
        <form wire:submit.prevent="save" method="POST" class="space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Date -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('expenses.date') }} <span class="text-red-500 dark:text-red-400">*</span></label>
                    <input type="date" wire:model="date" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-300 shadow-inner dark:shadow-none dark:[color-scheme:dark]">
                    @error('date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <!-- Amount -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('expenses.amount') }} <span class="text-red-500 dark:text-red-400">*</span></label>
                    <div class="relative">
                        <input type="text" inputmode="decimal" wire:model="amount" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-300 shadow-inner dark:shadow-none" dir="ltr">
                        <span class="absolute top-1/2 -translate-y-1/2 {{ session('locale', 'ar') === 'ar' ? 'left-4' : 'right-4' }} text-slate-400 dark:text-slate-500 font-bold text-sm">{{ getCurrency() }}</span>
                    </div>
                    @error('amount') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('expenses.category') }} <span class="text-red-500 dark:text-red-400">*</span></label>
                    <select wire:model.live="category_id" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-300 shadow-inner dark:shadow-none">
                        <option value="">{{ __('common.select') }}</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->translated_name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Sub-category (optional) -->
                <div wire:key="sub-category-field" @class(['block' => $showSubCategoryField, 'hidden' => !$showSubCategoryField])>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('expenses.sub_category') }} <span class="text-slate-400 text-xs font-normal">({{ __('common.optional') }})</span></label>
                    <select wire:model.live="sub_category_id" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-300 shadow-inner dark:shadow-none">
                        <option value="">{{ __('common.select') }}</option>
                        @foreach($subCategories as $sub)
                            <option value="{{ $sub['id'] }}">{{ $sub['name_' . app()->getLocale()] ?: $sub['name_fr'] ?: $sub['name_ar'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('expenses.payment_method') }} <span class="text-red-500 dark:text-red-400">*</span></label>
                    <select wire:model="payment_method" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-300 shadow-inner dark:shadow-none">
                        @foreach($paymentMethods as $pm)
                            <option value="{{ $pm['value'] }}">{{ $pm['label'] }}</option>
                        @endforeach
                    </select>
                    @error('payment_method') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Employee (only for salaries) -->
                <div wire:key="employee-field" @class(['block' => $showEmployeeField, 'hidden' => !$showEmployeeField])>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('expenses.employee') }} <span class="text-red-500 dark:text-red-400">*</span></label>
                    <select wire:model.live="employee_id" @if($showEmployeeField) required @endif class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-300 shadow-inner dark:shadow-none">
                        <option value="">{{ __('common.select') }}</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                    @error('employee_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    @if($showEmployeeAdvanceInfo)
                        <div class="mt-2 flex items-center gap-2 p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200/50 dark:border-amber-800/30">
                            <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-sm font-semibold text-amber-700 dark:text-amber-300">
                                {{ __('expenses.employee_advances', ['amount' => formatMoney((float)$employeeAdvanceInfo) . ' ' . getCurrency()]) }}
                            </span>
                        </div>
                    @elseif($employee_id && $showEmployeeField)
                        <div class="mt-2 flex items-center gap-2 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200/50 dark:border-emerald-800/30">
                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">
                                {{ __('expenses.employee_no_advances') }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('expenses.description') }} <span class="text-red-500 dark:text-red-400">*</span></label>
                <textarea wire:model="description" rows="2" placeholder="{{ __('expenses.desc_placeholder') }}" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-300 shadow-inner dark:shadow-none dark:placeholder-slate-500"></textarea>
                @error('description') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Receipt & Notes -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Notes -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('expenses.notes') }}</label>
                    <textarea wire:model="notes" rows="4" placeholder="{{ __('expenses.notes_placeholder') }}" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-300 shadow-inner dark:shadow-none dark:placeholder-slate-500"></textarea>
                </div>

                <!-- Receipt Upload -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('expenses.receipt') }}</label>
                    
                    <div class="relative w-full h-full min-h-[120px] group">
                        <input type="file" wire:model="receipt" accept="image/*,application/pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                        <div class="absolute inset-0 border-2 border-dashed border-blue-200 dark:border-blue-900/50 bg-blue-50/30 dark:bg-blue-900/10 rounded-xl flex flex-col items-center justify-center p-4 transition-colors group-hover:bg-blue-50/60 dark:group-hover:bg-blue-900/20 group-hover:border-blue-300 dark:group-hover:border-blue-800/50">
                            @if($receipt)
                                <svg class="w-8 h-8 text-blue-500 dark:text-blue-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="text-blue-600 dark:text-blue-400 font-semibold text-sm truncate w-full text-center">{{ $receipt->getClientOriginalName() }}</span>
                                <button type="button" wire:click="removeReceipt" class="mt-2 text-xs text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 relative z-30 font-bold bg-white dark:bg-slate-800 px-2 py-1 rounded shadow-sm">{{ __('expenses.remove_receipt') }}</button>
                            @else
                                <svg class="w-8 h-8 text-slate-400 dark:text-slate-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                <span class="text-slate-500 dark:text-slate-400 font-medium text-sm">{{ __('expenses.drag_drop_receipt') }}</span>
                                <span class="text-slate-400 dark:text-slate-500 text-xs mt-1">{{ __('expenses.receipt_help') }}</span>
                            @endif
                        </div>
                    </div>
                    @error('receipt') <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span> @enderror

                    @if($existingReceipt)
                        <div class="mt-4 flex items-center justify-between bg-slate-50 dark:bg-slate-900/50 p-3 rounded-lg border border-slate-100 dark:border-slate-800">
                            <div class="flex items-center gap-3">
                                <div class="bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 p-2 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ __('expenses.current_receipt') }}</p>
                                    <a href="{{ asset('storage/' . $existingReceipt) }}" target="_blank" class="text-xs text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1 mt-0.5">
                                        {{ __('expenses.view_file') }}
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-8 border-t border-slate-100 dark:border-slate-700/50 flex flex-col-reverse md:flex-row justify-end gap-3 md:gap-4 mt-8">
                <a href="{{ route('expenses.index') }}" class="w-full md:w-auto px-8 py-3 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors text-center border border-slate-200 dark:border-slate-600 shadow-sm">
                    {{ __('common.cancel') }}
                </a>
                <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center bg-gradient-to-r from-blue-600 to-blue-700 text-white px-10 py-3 rounded-xl font-bold shadow-lg shadow-blue-500/20 dark:shadow-blue-900/20 hover:shadow-blue-500/40 dark:hover:shadow-blue-900/40 hover:-translate-y-0.5 transition-all duration-300">
                    <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="save">{{ $expenseId ? __('common.update') : __('common.save') }}</span>
                    <span wire:loading wire:target="save">{{ __('common.loading') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

