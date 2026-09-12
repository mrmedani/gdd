<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                {{ __('alerts.title') }}
            </h1>
            <p class="text-slate-500 dark:text-slate-500 text-sm mt-1.5 font-medium">{{ __('alerts.subtitle') }}</p>
        </div>
        @if(!$showForm)
            <button wire:click="create" class="inline-flex items-center justify-center bg-blue-600 text-white px-6 py-2.5 rounded-xl font-bold shadow-sm hover:bg-blue-700 transition-all duration-200 cursor-pointer">
                <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('alerts.add') }}
            </button>
        @else
            <button wire:click="resetForm" class="inline-flex items-center justify-center bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 px-6 py-2.5 rounded-xl font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-300 shadow-sm cursor-pointer">
                {{ __('common.back') }}
            </button>
        @endif
    </div>

    @if($showForm)
        <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-2xl shadow-sm border border-slate-200/50 dark:border-slate-800/60 p-6 md:p-10 mb-8">
            <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-6 border-b border-slate-100 dark:border-slate-800 pb-4">
                {{ $commitmentId ? __('alerts.edit') : __('alerts.add') }}
            </h2>

            <form wire:submit="save" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Label -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('alerts.label') }} <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="label" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-300" placeholder="{{ __('alerts.label_placeholder') }}">
                        @error('label') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Day -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('alerts.day') }} <span class="text-red-500">*</span></label>
                        <select wire:model="day" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-300">
                            @for($d = 1; $d <= 31; $d++)
                                <option value="{{ $d }}">{{ $d }}</option>
                            @endfor
                        </select>
                        @error('day') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Amount -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('alerts.amount') }}</label>
                        <div class="relative">
                            <input type="text" inputmode="decimal" wire:model="amount" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-300" dir="ltr">
                            <span class="absolute top-1/2 -translate-y-1/2 {{ session('locale', 'ar') === 'ar' ? 'left-4' : 'right-4' }} text-slate-400 dark:text-slate-500 font-semibold text-sm">{{ getCurrency() }}</span>
                        </div>
                        @error('amount') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Lead days -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('alerts.lead_days') }} <span class="text-red-500">*</span></label>
                        <select wire:model="lead_days" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-300">
                            @for($d = 0; $d <= 30; $d++)
                                <option value="{{ $d }}">{{ $d }} {{ __('alerts.days') }}</option>
                            @endfor
                        </select>
                        @error('lead_days') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('alerts.notes') }}</label>
                    <textarea wire:model="notes" rows="2" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-300"></textarea>
                </div>

                <!-- Active -->
                <div>
                    <button type="button" wire:click="$toggle('is_active')" class="flex items-center gap-2.5 cursor-pointer">
                        <span class="block w-10 h-6 rounded-full transition-colors {{ $is_active ? 'bg-blue-500' : 'bg-slate-300 dark:bg-slate-700' }}"></span>
                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ __('alerts.active') }}</span>
                    </button>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="resetForm" class="px-6 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors border border-slate-200/50 dark:border-slate-700/50 shadow-sm cursor-pointer">
                        {{ __('common.cancel') }}
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-sm transition-colors cursor-pointer">
                        {{ $commitmentId ? __('common.update') : __('common.save') }}
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
                        <th class="py-4 px-6 text-start">{{ __('alerts.label') }}</th>
                        <th class="py-4 px-6 text-start">{{ __('alerts.day') }}</th>
                        <th class="py-4 px-6 text-start">{{ __('alerts.next_due') }}</th>
                        <th class="py-4 px-6 text-end">{{ __('alerts.amount') }}</th>
                        <th class="py-4 px-6 text-center">{{ __('alerts.status') }}</th>
                        <th class="py-4 px-6 text-end">{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($commitments as $c)
                        @php
                            $daysLeft = $c->daysUntilDue();
                            $dueSoon = $c->is_active && $c->isDueSoon();
                        @endphp
                        <tr class="hover:bg-blue-50/20 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="py-4 px-6 font-semibold text-slate-800 dark:text-slate-200">{{ $c->label }}</td>
                            <td class="py-4 px-6 text-slate-600 dark:text-slate-400">{{ __('alerts.each_month_day', ['day' => $c->day]) }}</td>
                            <td class="py-4 px-6 whitespace-nowrap">
                                <span class="font-medium text-slate-700 dark:text-slate-300">{{ $c->nextDueDate()->format('d/m/Y') }}</span>
                                @if($dueSoon)
                                    <span class="ms-2 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $daysLeft === 0 ? 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300' }}">
                                        {{ $daysLeft === 0 ? __('alerts.today') : ($daysLeft === 1 ? __('alerts.tomorrow') : 'J-' . $daysLeft) }}
                                    </span>
                                @else
                                    <span class="ms-2 text-xs text-slate-400 dark:text-slate-500">J-{{ $daysLeft }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 font-bold text-slate-700 dark:text-slate-300 whitespace-nowrap text-end"><span dir="ltr">{{ $c->amount !== null ? number_format($c->amount, 2, ',', ' ') . ' ' . getCurrency() : '-' }}</span></td>
                            <td class="py-4 px-6 text-center">
                                <button wire:click="toggleActive({{ $c->id }})" class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold cursor-pointer {{ $c->is_active ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400' }}">
                                    {{ $c->is_active ? __('alerts.on') : __('alerts.off') }}
                                </button>
                            </td>
                            <td class="py-4 px-6 text-end whitespace-nowrap">
                                <button wire:click="edit({{ $c->id }})" class="text-blue-600 dark:text-blue-400 font-semibold hover:underline cursor-pointer">{{ __('common.edit') }}</button>
                                <button wire:click="delete({{ $c->id }})" wire:confirm="{{ __('alerts.confirm_delete') }}" class="text-rose-500 dark:text-rose-400 font-semibold hover:underline ms-3 cursor-pointer">{{ __('common.delete') }}</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400 dark:text-slate-500">{{ __('alerts.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">
            {{ $commitments->links() }}
        </div>
    </div>
</div>
