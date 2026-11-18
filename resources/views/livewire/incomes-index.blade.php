<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                {{ __('incomes.title') }}
            </h1>
            <p class="text-slate-500 dark:text-slate-500 text-sm mt-1.5 font-medium">{{ __('incomes.subtitle') }}</p>
        </div>
        @if(!$showForm)
            <button wire:click="create" class="inline-flex items-center justify-center bg-blue-600 text-white px-6 py-2.5 rounded-xl font-bold shadow-sm hover:bg-blue-700 transition-all duration-200 cursor-pointer">
                <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('incomes.add') }}
            </button>
        @else
            <button wire:click="resetForm" class="inline-flex items-center justify-center bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 px-6 py-2.5 rounded-xl font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-300 shadow-sm cursor-pointer">
                {{ __('common.back') }}
            </button>
        @endif
    </div>

    <!-- Sélecteur de période : month-picker grid (identique à /station-charges et la clôture de caisse) -->
    <div class="flex justify-center mb-6">
        <div x-data="{ pickerOpen: false, pickerYear: {{ \Carbon\Carbon::createFromFormat('Y-m', $period)->year }} }" class="relative w-full max-w-md">
            <button type="button" @click="pickerOpen = !pickerOpen"
                    class="w-full flex items-center gap-3 px-4 py-3 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl font-semibold dark:text-white cursor-pointer text-left hover:bg-slate-100 dark:hover:bg-slate-900/50 transition-colors">
                <svg wire:loading wire:target="prevPeriod, nextPeriod, goToPeriod" class="w-5 h-5 animate-spin text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                <svg wire:loading.remove wire:target="prevPeriod, nextPeriod, goToPeriod" class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>{{ $periodShortLabel }}</span>
            </button>

            <div x-show="pickerOpen" @click.outside="pickerOpen = false" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                 x-cloak
                 class="absolute z-50 mt-2 w-full bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 p-4">
                <div class="flex items-center justify-between mb-3">
                    <button type="button" @click="pickerYear--" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <span class="font-bold text-slate-800 dark:text-white text-sm" x-text="pickerYear"></span>
                    <button type="button" @click="pickerYear++" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    @foreach ([1 => __('station_charges.month_jan'), 2 => __('station_charges.month_feb'), 3 => __('station_charges.month_mar'), 4 => __('station_charges.month_apr'), 5 => __('station_charges.month_may'), 6 => __('station_charges.month_jun'), 7 => __('station_charges.month_jul'), 8 => __('station_charges.month_aug'), 9 => __('station_charges.month_sep'), 10 => __('station_charges.month_oct'), 11 => __('station_charges.month_nov'), 12 => __('station_charges.month_dec')] as $num => $monthLabel)
                        <button type="button"
                                @click="const p = pickerYear + '-{{ str_pad($num, 2, '0', STR_PAD_LEFT) }}'; $wire.goToPeriod(p); pickerOpen = false;"
                                :class="{
                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 font-bold': $wire.period === (pickerYear + '-{{ str_pad($num, 2, '0', STR_PAD_LEFT) }}'),
                                    'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50': $wire.period !== (pickerYear + '-{{ str_pad($num, 2, '0', STR_PAD_LEFT) }}')
                                }"
                                class="px-2 py-2.5 rounded-lg text-sm font-medium transition-colors">
                            {{ $monthLabel }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes par type de source -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @foreach($sourceCards as $card)
            @php
                $colors = [
                    'investment' => ['from-emerald-500 to-teal-600', 'bg' => 'bg-emerald-50 dark:bg-emerald-500/10', 'text' => 'text-emerald-600 dark:text-emerald-400', 'ring' => 'bg-emerald-100 dark:bg-emerald-500/20'],
                    'franchise_fee' => ['from-blue-500 to-indigo-600', 'bg' => 'bg-blue-50 dark:bg-blue-500/10', 'text' => 'text-blue-600 dark:text-blue-400', 'ring' => 'bg-blue-100 dark:bg-blue-500/20'],
                    'other' => ['from-slate-500 to-slate-600', 'bg' => 'bg-slate-50 dark:bg-slate-500/10', 'text' => 'text-slate-600 dark:text-slate-400', 'ring' => 'bg-slate-100 dark:bg-slate-500/20'],
                ];
                $c = $colors[$card['key']] ?? $colors['other'];
                $active = $sourceFilter === $card['key'];
            @endphp
            <button type="button" wire:click="filterByType('{{ $card['key'] }}')"
                class="relative overflow-hidden text-start rounded-2xl p-5 border transition-all duration-200 cursor-pointer {{ $active ? 'bg-slate-900 dark:bg-white/5 border-slate-300 dark:border-white/20 shadow-md' : 'bg-white/80 dark:bg-slate-900/80 border-slate-200/50 dark:border-slate-800/60 hover:-translate-y-0.5 hover:shadow-sm' }}">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 rounded-xl {{ $c['ring'] }}">
                        <svg class="w-5 h-5 {{ $c['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19v3m0-18v3m0 6v3m-9 0h3m12 0h3M5.636 5.636l2.121 2.121m8.486 8.486l2.121 2.121M5.636 18.364l2.121-2.121m8.486-8.486l2.121-2.121"/></svg>
                    </div>
                    <span class="text-xs font-semibold {{ $active ? 'text-white dark:text-slate-200' : 'text-slate-400 dark:text-slate-500' }}">{{ $card['count'] }} {{ __('incomes.entries') }}</span>
                </div>
                <p class="text-xs font-semibold {{ $active ? 'text-white/80 dark:text-slate-300' : 'text-slate-500 dark:text-slate-400' }} mb-1">{{ $card['label'] }}</p>
                <h3 class="text-2xl font-black {{ $active ? 'text-white dark:text-white' : 'text-slate-900 dark:text-white' }} font-mono flex items-baseline gap-1">
                    <span dir="ltr">+ {{ number_format($card['total'], 2, ',', ' ') }}</span>
                    <span class="text-xs font-bold {{ $active ? 'text-white/70' : 'text-slate-400' }}">{{ getCurrency() }}</span>
                </h3>
            </button>
        @endforeach
    </div>

    @if($showForm)
        <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-2xl shadow-sm border border-slate-200/50 dark:border-slate-800/60 p-6 md:p-10 mb-8">
            <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-6 border-b border-slate-100 dark:border-slate-800 pb-4">
                {{ $incomeId ? __('incomes.edit') : __('incomes.add') }}
            </h2>

            <form wire:submit="save" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Date -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('incomes.date') }} <span class="text-red-500">*</span></label>
                        <input type="date" wire:model="date" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-300 dark:[color-scheme:dark]">
                        @error('date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Amount -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('incomes.amount') }} <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="text" inputmode="decimal" wire:model="amount" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-300" dir="ltr">
                            <span class="absolute top-1/2 -translate-y-1/2 {{ session('locale', 'ar') === 'ar' ? 'left-4' : 'right-4' }} text-slate-400 dark:text-slate-500 font-semibold text-sm">{{ getCurrency() }}</span>
                        </div>
                        @error('amount') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Source type -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('incomes.source_type') }} <span class="text-red-500">*</span></label>
                        <select wire:model.live="source_type" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-300">
                            @foreach(self::SOURCE_TYPES as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('source_type') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Source name -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('incomes.source_name') }}</label>
                        <input type="text" wire:model="source_name" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-300" placeholder="{{ __('incomes.source_name_placeholder') }}">
                    </div>

                    <!-- Sous-type (uniquement pour Investissement) -->
                    @if($source_type === 'investment')
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('incomes.sub_type') }}</label>
                        <select wire:model.live="sub_type" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-300">
                            <option value="">{{ __('common.select') }}</option>
                            @foreach(self::SUB_TYPES as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('sub_type') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    @endif
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('incomes.notes') }}</label>
                    <textarea wire:model="notes" rows="3" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-300"></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="resetForm" class="px-6 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors border border-slate-200/50 dark:border-slate-700/50 shadow-sm cursor-pointer">
                        {{ __('common.cancel') }}
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-sm transition-colors cursor-pointer">
                        {{ $incomeId ? __('common.update') : __('common.save') }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- List -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/50 dark:border-slate-800/60 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50/50 dark:bg-slate-950/30 text-slate-500 dark:text-slate-500 font-bold text-xs uppercase tracking-wider border-b border-slate-100 dark:border-slate-800/60">
                    <tr>
                        <th class="py-4 px-6 text-start">{{ __('incomes.date') }}</th>
                        <th class="py-4 px-6 text-start">{{ __('incomes.source_type') }}</th>
                        <th class="py-4 px-6 text-start">{{ __('incomes.source_name') }}</th>
                        <th class="py-4 px-6 text-start">{{ __('incomes.sub_type') }}</th>
                        <th class="py-4 px-6 text-end">{{ __('incomes.amount') }}</th>
                        <th class="py-4 px-6 text-end">{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($incomes as $income)
                        <tr class="hover:bg-blue-50/20 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="py-4 px-6 font-medium text-slate-700 dark:text-slate-300 whitespace-nowrap">{{ $income->date->format('d/m/Y') }}</td>
                            <td class="py-4 px-3 md:px-6">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300">
                                    {{ \App\Domains\Treasury\Models\Income::sourceTypeLabel($income->source_type) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-slate-600 dark:text-slate-400">{{ $income->source_name ?? '-' }}</td>
                            <td class="py-4 px-6 text-slate-600 dark:text-slate-400">{{ $income->sub_type ? \App\Domains\Treasury\Models\Income::subTypeLabel($income->sub_type) : '-' }}</td>
                            <td class="py-4 px-6 font-bold text-emerald-600 dark:text-emerald-400 whitespace-nowrap text-end"><span dir="ltr">+ {{ number_format($income->amount, 2, ',', ' ') }} {{ getCurrency() }}</span></td>
                            <td class="py-4 px-4 md:px-6">
                                <div class="flex items-end justify-end gap-2">
                                <button wire:click="edit({{ $income->id }})" class="min-h-[44px] min-w-[44px] inline-flex items-center justify-center bg-slate-100 dark:bg-slate-800 text-blue-600 dark:text-blue-400 hover:bg-blue-500 hover:text-white rounded-xl transition-colors cursor-pointer" title="{{ __('common.edit') }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button wire:click="delete({{ $income->id }})" wire:confirm="{{ __('incomes.confirm_delete') }}" class="min-h-[44px] min-w-[44px] inline-flex items-center justify-center bg-slate-100 dark:bg-slate-800 text-rose-500 hover:bg-rose-500 hover:text-white rounded-xl transition-colors cursor-pointer" title="{{ __('common.delete') }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400 dark:text-slate-500">{{ __('incomes.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">
            {{ $incomes->links() }}
        </div>
    </div>
</div>
