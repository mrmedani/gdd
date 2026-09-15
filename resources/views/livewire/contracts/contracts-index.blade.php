<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                {{ __('contracts.title') }}
            </h1>
            <p class="text-slate-500 dark:text-slate-500 text-sm mt-1.5 font-medium">{{ __('contracts.subtitle') }}</p>
        </div>
        @if(!$showForm)
            <button wire:click="create" class="inline-flex items-center justify-center bg-blue-600 text-white px-6 py-2.5 rounded-xl font-bold shadow-sm hover:bg-blue-700 transition-all duration-200 cursor-pointer">
                <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('contracts.add') }}
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
                {{ $contractId ? __('contracts.edit') : __('contracts.add') }}
            </h2>

            <form wire:submit="save" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Titre -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('contracts.title_label') }} <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="title" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-300" placeholder="{{ __('contracts.title_placeholder') }}">
                        @error('title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Contrepartie -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('contracts.party') }}</label>
                        <input type="text" wire:model="party" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-300" placeholder="{{ __('contracts.party_placeholder') }}">
                        @error('party') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Date début -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('contracts.start_date') }} <span class="text-red-500">*</span></label>
                        <input type="date" wire:model="start_date" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-300">
                        @error('start_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Date fin -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('contracts.end_date') }} <span class="text-red-500">*</span></label>
                        <input type="date" wire:model="end_date" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-300">
                        @error('end_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-2">{{ __('contracts.end_date_hint') }}</p>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('alerts.notes') }}</label>
                    <textarea wire:model="notes" rows="2" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-300"></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="resetForm" class="px-6 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors border border-slate-200/50 dark:border-slate-700/50 shadow-sm cursor-pointer">
                        {{ __('common.cancel') }}
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-sm transition-colors cursor-pointer">
                        {{ $contractId ? __('common.update') : __('common.save') }}
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
                        <th class="py-4 px-6 text-start">{{ __('contracts.title_label') }}</th>
                        <th class="py-4 px-6 text-start">{{ __('contracts.party') }}</th>
                        <th class="py-4 px-6 text-start">{{ __('contracts.start_date') }}</th>
                        <th class="py-4 px-6 text-start">{{ __('contracts.end_date') }}</th>
                        <th class="py-4 px-6 text-center">{{ __('alerts.status') }}</th>
                        <th class="py-4 px-6 text-end">{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($contracts as $c)
                        @php
                            $daysLeft = $c->daysUntilExpiry();
                            $status = $c->status();
                        @endphp
                        <tr class="hover:bg-blue-50/20 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="py-4 px-6 font-semibold text-slate-800 dark:text-slate-200">{{ $c->title }}</td>
                            <td class="py-4 px-6 text-slate-600 dark:text-slate-400">{{ $c->party ?? '—' }}</td>
                            <td class="py-4 px-6 whitespace-nowrap text-slate-600 dark:text-slate-400">{{ $c->start_date->format('d/m/Y') }}</td>
                            <td class="py-4 px-6 whitespace-nowrap">
                                <span class="font-medium text-slate-700 dark:text-slate-300">{{ $c->end_date->format('d/m/Y') }}</span>
                                @if($status === 'expiring')
                                    <span class="ms-2 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $daysLeft === 0 ? 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300' }}">
                                        {{ $daysLeft === 0 ? __('alerts.today') : ($daysLeft === 1 ? __('alerts.tomorrow') : 'J-' . $daysLeft) }}
                                    </span>
                                @elseif($status === 'expired')
                                    <span class="ms-2 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-200 dark:bg-slate-800 text-slate-500 dark:text-slate-400">
                                        {{ __('contracts.expired') }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold {{ $status === 'active' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' : ($status === 'expiring' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400') }}">
                                    {{ __('contracts.status_' . $status) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-end whitespace-nowrap">
                                <button wire:click="edit({{ $c->id }})" class="text-blue-600 dark:text-blue-400 font-semibold hover:underline cursor-pointer">{{ __('common.edit') }}</button>
                                <button wire:click="delete({{ $c->id }})" wire:confirm="{{ __('contracts.confirm_delete') }}" class="text-rose-500 dark:text-rose-400 font-semibold hover:underline ms-3 cursor-pointer">{{ __('common.delete') }}</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400 dark:text-slate-500">{{ __('contracts.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">
            {{ $contracts->links() }}
        </div>
    </div>
</div>
