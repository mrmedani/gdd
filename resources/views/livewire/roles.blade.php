<div class="animate-fade-in">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-700 to-pink-600 dark:from-purple-400 dark:to-pink-400 tracking-tight font-heading">
                {{ __('settings.roles') }}
            </h1>
            <p class="text-slate-500 dark:text-slate-500 text-sm mt-1.5 font-medium">{{ __('settings.roles_desc') }}</p>
        </div>
        @if(!$showForm)
            <button wire:click="create" class="inline-flex items-center justify-center bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-purple-500/20 hover:shadow-purple-500/40 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer">
                <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('settings.add_role') }}
            </button>
        @else
            <button wire:click="resetForm" class="inline-flex items-center justify-center bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 px-6 py-2.5 rounded-xl font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-300 shadow-sm cursor-pointer">
                {{ __('common.back') }}
            </button>
        @endif
    </div>

    @if($showForm)
        <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 p-6 md:p-10 relative overflow-hidden group hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
            <div class="absolute top-0 right-0 w-32 h-32 bg-purple-50/20 dark:bg-purple-500/5 rounded-full blur-3xl -mr-16 -mt-16 z-0 pointer-events-none"></div>
            <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-6 border-b border-slate-100 dark:border-slate-800 pb-4 relative z-10">
                {{ $roleId ? __('settings.edit_role') : __('settings.add_new_role') }}
            </h2>

            <form wire:submit="save" class="space-y-6 relative z-10">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('settings.name') }} <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="name" class="w-full px-4 py-2.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 outline-none transition-all text-slate-700 dark:text-slate-100 shadow-inner dark:shadow-none font-medium" dir="ltr">
                        @error('name') <span class="text-rose-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('settings.label_ar') }} <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="label_ar" class="w-full px-4 py-2.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 outline-none transition-all text-slate-700 dark:text-slate-100 shadow-inner dark:shadow-none font-medium">
                        @error('label_ar') <span class="text-rose-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('settings.label_fr') }}</label>
                        <input type="text" wire:model="label_fr" class="w-full px-4 py-2.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 outline-none transition-all text-slate-700 dark:text-slate-100 shadow-inner dark:shadow-none font-medium">
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 dark:border-slate-800/60">
                    <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-4">{{ __('settings.permissions') }}</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        @foreach([
                            'dashboard' => __('nav.dashboard'),
                            'expenses' => __('nav.expenses'),
                            'treasury' => __('nav.treasury'),
                            'employees' => __('nav.employees'),
                            'reports' => __('nav.reports'),
                            'statistics' => __('nav.statistics'),
                            'settings' => __('nav.settings'),
                            'categories' => __('settings.categories'),
                            'users' => __('settings.manage_users'),
                            'roles' => __('settings.roles'),
                            'audit-logs' => __('settings.audit_logs'),
                            'whatsapp-messages' => 'Messages WhatsApp',
                            'view-deficit' => __('settings.view_deficit'),
                            'delete-closure' => __('settings.delete_closure'),
                            'login-as' => __('settings.login_as'),
                        ] as $key => $label)
                            @php $checked = $permValues[$key] ?? false; @endphp
                            <button type="button" wire:click="togglePermission('{{ $key }}')"
                                    class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition-all duration-200 text-start w-full {{ $checked ? 'bg-purple-50 dark:bg-purple-900/20 border-purple-200 dark:border-purple-700/50 shadow-sm' : 'bg-slate-50/50 dark:bg-slate-950/30 border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50' }}">
                                <span class="relative shrink-0">
                                    <span class="block w-10 h-6 rounded-full transition-colors {{ $checked ? 'bg-purple-500 dark:bg-purple-600' : 'bg-slate-300 dark:bg-slate-700' }}"></span>
                                    <span class="dot absolute left-0.5 top-0.5 bg-white w-5 h-5 rounded-full transition-transform shadow-sm {{ $checked ? 'translate-x-4' : '' }}"></span>
                                </span>
                                <span class="text-xs font-semibold {{ $checked ? 'text-purple-700 dark:text-purple-300' : 'text-slate-700 dark:text-slate-300' }}">{{ $label }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="resetForm" class="px-6 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors border border-slate-200/50 dark:border-slate-700/50 shadow-sm cursor-pointer">
                        {{ __('common.cancel') }}
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-purple-600 text-white font-bold rounded-xl hover:bg-purple-700 hover:shadow-purple-500/35 transition-all shadow-md shadow-purple-500/20 cursor-pointer">
                        {{ __('common.save') }}
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 overflow-hidden relative z-10 hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-start {{ session('locale', 'ar') === 'ar' ? 'rtl:text-right' : 'ltr:text-left' }}">
                    <thead class="bg-slate-50/50 dark:bg-slate-950/30 text-slate-500 dark:text-slate-500 font-bold text-xs uppercase tracking-wider border-b border-slate-100 dark:border-slate-800/60">
                        <tr>
                            <th class="py-4 px-6">{{ __('settings.name') }}</th>
                            <th class="py-4 px-6">{{ __('settings.label_ar') }}</th>
                            <th class="py-4 px-6">{{ __('settings.label_fr') }}</th>
                            <th class="py-4 px-6 text-center">{{ __('settings.linked_users') }}</th>
                            <th class="py-4 px-6 text-center">{{ __('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50 bg-transparent">
                        @forelse($roles as $role)
                            <tr class="hover:bg-purple-50/20 dark:hover:bg-purple-900/10 transition-colors group">
                                <td class="py-4 px-6 font-bold text-slate-800 dark:text-slate-200">
                                    <span class="bg-slate-100 dark:bg-slate-800 border border-slate-200/40 dark:border-slate-700/50 px-2.5 py-1 rounded-xl text-xs font-bold text-slate-500 dark:text-slate-400">{{ $role->name }}</span>
                                </td>
                                <td class="py-4 px-6 font-semibold text-slate-800 dark:text-slate-200">{{ $role->label_ar }}</td>
                                <td class="py-4 px-6 text-slate-600 dark:text-slate-300 font-semibold">{{ $role->label_fr ?? '-' }}</td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 border border-blue-100/30 dark:border-blue-800/30">
                                        {{ $role->users_count }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-3 opacity-100 md:opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                        <button wire:click="edit({{ $role->id }})" class="text-slate-400 dark:text-slate-500 hover:text-purple-600 dark:hover:text-purple-400 min-w-[44px] min-h-[44px] p-1 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg cursor-pointer inline-flex items-center justify-center" title="{{ __('common.edit') }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        @if($role->users_count == 0)
                                            <button wire:click="delete({{ $role->id }})" wire:confirm="{{ __('settings.confirm_delete_role') }}" class="text-slate-400 dark:text-slate-500 hover:text-rose-600 dark:hover:text-rose-500 min-w-[44px] min-h-[44px] p-1 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg cursor-pointer inline-flex items-center justify-center" title="{{ __('common.delete') }}">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        @else
                                            <button disabled class="text-slate-200 dark:text-slate-800 cursor-not-allowed min-w-[44px] min-h-[44px] p-1 inline-flex items-center justify-center" title="{{ __('settings.cannot_delete_linked_role') }}">
                                                <svg class="w-5 h-5 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-20 h-20 bg-slate-50 dark:bg-slate-900/50 rounded-full flex items-center justify-center mb-4 border border-slate-200/50 dark:border-slate-800/60">
                                            <svg class="w-10 h-10 text-slate-300 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                        </div>
                                        <p class="text-slate-500 dark:text-slate-400 font-bold text-lg">{{ __('settings.no_roles') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($roles->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-800/50 bg-slate-50/30 dark:bg-slate-900/30">
                    {{ $roles->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
