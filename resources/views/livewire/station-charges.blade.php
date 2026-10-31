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







    <!-- Sélecteur de période : flèches + saut rapide (équipé d'un select natif) -->
    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 mb-6">
        <button wire:click="prevPeriod" wire:target="prevPeriod, nextPeriod" class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors cursor-pointer">
            <svg wire:loading.remove wire:target="prevPeriod, nextPeriod" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            <svg wire:loading wire:target="prevPeriod, nextPeriod" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
        </button>
        <select wire:model.live="period" title="{{ __('station_charges.jump', ['default' => 'Aller à la période']) }}"
            class="px-4 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 font-bold text-slate-800 dark:text-slate-100 text-center min-w-[300px] cursor-pointer">
            @foreach($availablePeriods as $ym => $label)
                <option value="{{ $ym }}" {{ $period === $ym ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button wire:click="nextPeriod" wire:target="prevPeriod, nextPeriod" class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors cursor-pointer">
            <svg wire:loading.remove wire:target="prevPeriod, nextPeriod" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <svg wire:loading wire:target="prevPeriod, nextPeriod" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
        </button>
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



                            <td class="py-4 px-6 text-end whitespace-nowrap">



                                <button wire:click="edit({{ $c->id }})" class="text-blue-600 dark:text-blue-400 font-semibold hover:underline cursor-pointer">{{ __('common.edit') }}</button>



                                <button wire:click="delete({{ $c->id }})" wire:confirm="{{ __('station_charges.confirm_delete', ['default' => 'Supprimer cette commission ?']) }}" class="text-rose-500 dark:text-rose-400 font-semibold hover:underline ms-3 cursor-pointer">{{ __('common.delete') }}</button>



                            </td>



                        </tr>



                    @empty



                        <tr>



                            <td colspan="4" class="py-10 text-center text-slate-400 dark:text-slate-500">



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







            <div>



                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('station_charges.period', ['default' => 'Période']) }} <span class="text-red-500">*</span></label>



                <select wire:model="period"
                    class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none text-slate-700 dark:text-slate-300 cursor-pointer">
                    @foreach($availablePeriods as $ym => $label)
                        <option value="{{ $ym }}">{{ $label }}</option>
                    @endforeach
                </select>



                @error('period') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror



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



