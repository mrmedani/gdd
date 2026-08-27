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
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300">
                                    {{ \App\Domains\Treasury\Models\Income::sourceTypeLabel($income->source_type) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-slate-600 dark:text-slate-400">{{ $income->source_name ?? '-' }}</td>
                            <td class="py-4 px-6 text-slate-600 dark:text-slate-400">{{ $income->sub_type ? \App\Domains\Treasury\Models\Income::subTypeLabel($income->sub_type) : '-' }}</td>
                            <td class="py-4 px-6 font-bold text-emerald-600 dark:text-emerald-400 whitespace-nowrap text-end"><span dir="ltr">+ {{ number_format($income->amount, 2, ',', ' ') }} {{ getCurrency() }}</span></td>
                            <td class="py-4 px-6 text-end whitespace-nowrap">
                                <button wire:click="edit({{ $income->id }})" class="text-blue-600 dark:text-blue-400 font-semibold hover:underline cursor-pointer">{{ __('common.edit') }}</button>
                                <button wire:click="delete({{ $income->id }})" wire:confirm="{{ __('incomes.confirm_delete') }}" class="text-rose-500 dark:text-rose-400 font-semibold hover:underline ms-3 cursor-pointer">{{ __('common.delete') }}</button>
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
