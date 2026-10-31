<div>
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">{{ __('station_charges.title', ['default' => 'Commissions stations']) }}</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ __('station_charges.subtitle', ['default' => 'Charges des agences (wilayas) : saisie manuelle chaque période, déduite automatiquement à la clôture de caisse.']) }}</p>
        </div>
        <button wire:click="create" class="inline-flex items-center px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-colors shadow-lg shadow-blue-600/20 cursor-pointer">
            <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            {{ __('station_charges.add', ['default' => 'Ajouter une commission']) }}
        </button>
    </div>
    <!-- Sélecteur de période : month-picker grid (identique à la clôture de caisse) -->
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
                 class="absolute z-50 mt-2 w-full bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 p-4" x-cloak>
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
                    @php $closedMonthsJson = json_encode($closedMonths); @endphp
                    @foreach ([1 => __('station_charges.month_jan'), 2 => __('station_charges.month_feb'), 3 => __('station_charges.month_mar'), 4 => __('station_charges.month_apr'), 5 => __('station_charges.month_may'), 6 => __('station_charges.month_jun'), 7 => __('station_charges.month_jul'), 8 => __('station_charges.month_aug'), 9 => __('station_charges.month_sep'), 10 => __('station_charges.month_oct'), 11 => __('station_charges.month_nov'), 12 => __('station_charges.month_dec')] as $num => $monthLabel)
                        <button type="button"
                                @click="const p = pickerYear + '-{{ str_pad($num, 2, '0', STR_PAD_LEFT) }}'; if (!{{ $closedMonthsJson }}.includes(p)) { $wire.goToPeriod(p); pickerOpen = false; }"
                                :class="{
                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 font-bold': $wire.period === (pickerYear + '-{{ str_pad($num, 2, '0', STR_PAD_LEFT) }}'),
                                    'text-slate-300 dark:text-slate-600 opacity-40 cursor-not-allowed line-through dark:opacity-40': {{ $closedMonthsJson }}.includes(pickerYear + '-{{ str_pad($num, 2, '0', STR_PAD_LEFT) }}'),
                                    'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50': $wire.period !== (pickerYear + '-{{ str_pad($num, 2, '0', STR_PAD_LEFT) }}') && !{{ $closedMonthsJson }}.includes(pickerYear + '-{{ str_pad($num, 2, '0', STR_PAD_LEFT) }}')
                                }"
                                class="px-2 py-2.5 rounded-lg text-sm font-medium transition-colors"
                                :disabled="{{ $closedMonthsJson }}.includes(pickerYear + '-{{ str_pad($num, 2, '0', STR_PAD_LEFT) }}')"
                                :title="{{ $closedMonthsJson }}.includes(pickerYear + '-{{ str_pad($num, 2, '0', STR_PAD_LEFT) }}') ? '{{ __('station_charges.month_closed', ['default' => 'Période déjà clôturée']) }}' : ''">
                            {{ $monthLabel }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <!-- Bandeau période : range + total -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-50/50 dark:bg-blue-950/20 border border-blue-100/50 dark:border-blue-900/30 p-4 rounded-xl">
            <p class="text-xs font-semibold text-blue-800 dark:text-blue-400 mb-1">{{ __('station_charges.period_range', ['default' => 'Période']) }}</p>
            <p class="text-sm font-bold text-slate-700 dark:text-slate-200" dir="ltr">{{ $clotureRange['start']->format('d/m/Y') }} → {{ $clotureRange['end']->format('d/m/Y') }}</p>
        </div>
        <div class="bg-slate-50/50 dark:bg-slate-950/30 border border-slate-200/50 dark:border-slate-800/50 p-4 rounded-xl">
            <p class="text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">{{ __('station_charges.nb_stations', ['default' => 'Stations']) }}</p>
            <p class="text-2xl font-black text-slate-800 dark:text-white leading-none">{{ count($charges) }}</p>
        </div>
        <div class="bg-amber-50/30 dark:bg-amber-950/20 border border-amber-100/50 dark:border-amber-900/30 p-4 rounded-xl">
            <p class="text-xs font-semibold text-amber-800 dark:text-amber-400 mb-1">{{ __('station_charges.total', ['default' => 'Total commissions']) }}</p>
            <p class="text-2xl font-black text-amber-600 dark:text-amber-500 leading-none"><span dir="ltr">{{ number_format($total, 2, ',', ' ') }}</span> <span class="text-sm font-bold">{{ getCurrency() }}</span></p>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                <span class="text-emerald-600 dark:text-emerald-400">{{ __('station_charges.paid_short', ['default' => 'Payé']) }}: <span dir="ltr">{{ number_format($paidTotal, 2, ',', ' ') }}</span></span>
                · <span class="text-rose-600 dark:text-rose-400">{{ __('station_charges.unpaid_short', ['default' => 'Reste']) }}: <span dir="ltr">{{ number_format($unpaidTotal, 2, ',', ' ') }}</span></span>
            </p>
        </div>
    </div>
    @if($isFuture)
    <div class="mb-6 p-4 rounded-xl bg-yellow-50 dark:bg-yellow-950/20 border border-yellow-200/50 dark:border-yellow-900/30 text-sm text-yellow-800 dark:text-yellow-300">
        {{ __('station_charges.future_warn', ['default' => 'Période future : la clôture se fera quand la période sera terminée. Tu peux pré-enregistrer les commissions dès maintenant.']) }}
    </div>
    @endif
    <!-- Tableau -->
    <div class="bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-950/40 text-start text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">
                        <th class="py-4 px-6 text-start">{{ __('station_charges.station', ['default' => 'Station']) }}</th>
                        <th class="py-4 px-6 text-start">{{ __('station_charges.amount', ['default' => 'Montant']) }}</th>
                        <th class="py-4 px-6 text-start hidden md:table-cell">{{ __('station_charges.notes', ['default' => 'Notes']) }}</th>
                        <th class="py-4 px-6 text-center">{{ __('station_charges.status', ['default' => 'Statut']) }}</th>
                        <th class="py-4 px-6 text-end">{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($charges as $c)
                        <tr class="hover:bg-blue-50/20 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="py-4 px-6">
                                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $c->station_name }}</span>
                                <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">{{ $c->created_by ? ($c->creator?->name ?? '') : '' }}</div>
                            </td>
                            <td class="py-4 px-6 font-bold text-slate-700 dark:text-slate-300 whitespace-nowrap"><span dir="ltr">{{ number_format($c->amount, 2, ',', ' ') }} {{ getCurrency() }}</span></td>
                            <td class="py-4 px-6 text-slate-500 dark:text-slate-400 text-sm max-w-xs truncate hidden md:table-cell">{{ $c->notes ?? '—' }}</td>
                            <td class="py-4 px-6 text-center whitespace-nowrap">
                                <button wire:click="togglePaid({{ $c->id }})" wire:loading.class="opacity-40" wire:target="togglePaid"
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold cursor-pointer transition-colors {{ $c->paid_at ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800' }}">
                                    {{ $c->paid_at ? __('station_charges.paid', ['default' => 'Payée']) : __('station_charges.unpaid', ['default' => 'Pas encore']) }}
                                </button>
                            </td>
                            <td class="py-4 px-6 text-end whitespace-nowrap">
                                <button wire:click="edit({{ $c->id }})" class="text-blue-600 dark:text-blue-400 font-semibold hover:underline cursor-pointer">{{ __('common.edit') }}</button>
                                <button wire:click="delete({{ $c->id }})" wire:confirm="{{ __('station_charges.confirm_delete', ['default' => 'Supprimer cette commission ?']) }}" class="text-rose-500 dark:text-rose-400 font-semibold hover:underline ms-3 cursor-pointer">{{ __('common.delete') }}</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400 dark:text-slate-500">
                                {{ __('station_charges.no_data', ['default' => 'Aucune commission enregistrée pour cette période.']) }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <!-- Formulaire (modal) -->
    @if($showForm)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data x-show="true" wire:key="modal">
        <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" wire:click="resetForm"></div>
        <form wire:submit="save" class="relative bg-white dark:bg-slate-900 w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 p-6 space-y-5">
            <h2 class="text-lg font-black text-slate-900 dark:text-white">
                {{ $chargeId ? __('station_charges.edit_title', ['default' => 'Modifier la commission']) : __('station_charges.add_title', ['default' => 'Nouvelle commission']) }}
            </h2>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('station_charges.station', ['default' => 'Station']) }} <span class="text-red-500">*</span></label>
                <input type="text" wire:model="station_name" list="stationsList"
                    class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none text-slate-700 dark:text-slate-300"
                    placeholder="{{ __('station_charges.station_placeholder', ['default' => 'STATION ORAN, STATION SETIF…']) }}">
                <datalist id="stationsList">
                    @foreach($knownStations as $s)
                        <option value="{{ $s }}"></option>
                    @endforeach
                </datalist>
                @error('station_name') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            {{-- FIX simplification : la période se choisit UNIQUEMENT sur la page
                 (sélecteur du haut). Dans le modal, c'est un simple TITRE affiché. --}}
            {{-- FIX UI : le badge période hérite du mode sombre de la platform
                 (dark:bg-blue-500/15 + texte et bordure adaptés) — avant il
                 restait un bloc clair « fond bleu pâle / texte illisible » --}}
            <div class="flex items-center justify-between gap-3 bg-blue-600/10 dark:bg-blue-500/15 border border-blue-200 dark:border-blue-500/30 rounded-xl px-4 py-3">
                <p class="text-xs font-semibold text-blue-700 dark:text-blue-400">{{ __('station_charges.period', ['default' => 'Période']) }}</p>
                <p class="font-bold text-slate-800 dark:text-white">{{ $periodTitle }}</p>
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400"><span dir="ltr">{{ $periodRange['start']->format('d/m') }} → {{ $periodRange['end']->format('d/m') }}</span></span>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('station_charges.amount', ['default' => 'Montant']) }} <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="text" inputmode="decimal" wire:model="amount" dir="ltr"
                        class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none font-semibold text-slate-700 dark:text-slate-300"
                        placeholder="0.00">
                    <span class="absolute top-1/2 -translate-y-1/2 {{ session('locale', 'ar') === 'ar' ? 'left-4' : 'right-4' }} text-slate-400 dark:text-slate-500 font-bold text-sm">{{ getCurrency() }}</span>
                </div>
                @error('amount') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('station_charges.notes', ['default' => 'Notes (optionnel)']) }}</label>
                <textarea wire:model="notes" rows="2" maxlength="2000"
                    class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none text-sm text-slate-700 dark:text-slate-300"
                    placeholder="{{ __('station_charges.notes_placeholder', ['default' => 'Détail de la période, ajustement…']) }}"></textarea>
                @error('notes') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" wire:click="resetForm" class="flex-1 px-4 py-3 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 rounded-xl font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">{{ __('common.cancel') }}</button>
                <button type="submit" class="flex-1 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-colors cursor-pointer">
                    <span wire:loading wire:target="save">{{ __('common.saving', ['default' => '…']) }}</span>
                    <span wire:loading.remove wire:target="save">{{ __('common.save') }}</span>
                </button>
            </div>
        </form>
    </div>
    @endif
</div>
