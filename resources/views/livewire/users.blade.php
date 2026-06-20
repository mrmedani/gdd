<div class="max-w-5xl mx-auto space-y-8 animate-fade-in">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
        <div>
            <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-700 to-indigo-600 dark:from-blue-400 dark:to-indigo-300 tracking-tight font-heading">
                {{ __('settings.manage_users') }}
            </h1>
            <p class="text-slate-500 dark:text-slate-500 text-sm mt-1.5 font-medium">{{ __('settings.users_desc') }}</p>
        </div>
        <a href="{{ route('settings.index') }}" class="inline-flex items-center justify-center bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 px-6 py-2.5 rounded-xl font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-300 shadow-sm">
            {{ __('common.back') }}
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 relative z-10">
        <!-- Add/Edit User Form -->
        <div id="user-form" class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 p-6 md:p-8 relative overflow-hidden group hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300 h-fit">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50/20 dark:bg-blue-500/5 rounded-full blur-3xl -mr-16 -mt-16 z-0 pointer-events-none"></div>
            <div class="flex items-center gap-3 mb-6 relative z-10">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center border border-blue-200/20 dark:border-blue-800 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                </div>
                <h2 class="text-lg font-bold text-slate-800 dark:text-white">{{ $editingUserId ? __('settings.edit_user') : __('settings.add_user') }}</h2>
            </div>

            <form wire:submit="saveUser" class="space-y-6 relative z-10">
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('settings.name') }}</label>
                    <input type="text" wire:model="name" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-200 shadow-inner dark:shadow-none font-medium">
                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('settings.email') }}</label>
                    <input type="email" wire:model="email" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-200 shadow-inner dark:shadow-none font-medium" dir="ltr">
                    @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('settings.password') }} <span class="text-slate-400 dark:text-slate-500 text-xs font-normal">({{ __('common.optional') }})</span></label>
                    <input type="password" wire:model="password" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-200 shadow-inner dark:shadow-none font-medium" dir="ltr">
                    @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('settings.confirm_password') }}</label>
                    <input type="password" wire:model="passwordConfirmation" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-200 shadow-inner dark:shadow-none font-medium" dir="ltr">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('settings.role') }}</label>
                        <div class="relative">
                            <select wire:model="role" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-200 appearance-none shadow-inner dark:shadow-none font-semibold">
                                @foreach($roles as $r)
                                    <option value="{{ $r->name }}">{{ $r->label_ar ?? $r->name }}</option>
                                @endforeach
                            </select>
                            <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 absolute {{ session('locale', 'ar') === 'ar' ? 'left-4' : 'right-4' }} top-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('settings.locale') }}</label>
                        <div class="relative">
                            <select wire:model="locale" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-200 appearance-none shadow-inner dark:shadow-none font-semibold">
                                <option value="ar">العربية</option>
                                <option value="fr">Français</option>
                                <option value="en">English</option>
                            </select>
                            <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 absolute {{ session('locale', 'ar') === 'ar' ? 'left-4' : 'right-4' }} top-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800/60 flex flex-col-reverse sm:flex-row justify-end gap-3">
                    @if($editingUserId)
                        <button type="button" wire:click="cancelEdit" class="w-full sm:w-auto px-6 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors text-center border border-slate-200 dark:border-slate-700 shadow-sm cursor-pointer">
                            {{ __('common.cancel') }}
                        </button>
                    @endif
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-500/20 hover:shadow-blue-500/40 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer">
                        {{ $editingUserId ? __('common.update') : __('settings.add_user') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Users List -->
        <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 p-6 md:p-8 relative overflow-hidden group hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
            <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50/20 dark:bg-emerald-500/5 rounded-full blur-3xl -mr-16 -mt-16 z-0 pointer-events-none"></div>
            <div class="flex items-center gap-3 mb-6 relative z-10">
                <div class="w-10 h-10 bg-emerald-100/40 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center border border-emerald-200/20 dark:border-emerald-800 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <h2 class="text-lg font-bold text-slate-800 dark:text-white">{{ __('settings.existing_users') }}</h2>
            </div>

            <div class="mb-5 relative z-10">
                <div class="relative">
                    <svg class="absolute top-1/2 -translate-y-1/2 {{ session('locale', 'ar') === 'ar' ? 'right-4' : 'left-4' }} w-5 h-5 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('settings.search_users') }}" class="w-full px-4 py-3 {{ session('locale', 'ar') === 'ar' ? 'pr-12' : 'pl-12' }} bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-100 shadow-inner dark:shadow-none font-medium">
                </div>
            </div>

            <div class="space-y-3 relative z-10">
                @foreach($users as $user)
                    <div class="flex items-center justify-between p-4 rounded-2xl border border-slate-200/50 dark:border-slate-800/40 bg-slate-50/30 dark:bg-slate-950/30 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all group">
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center text-white font-extrabold text-sm shadow-md">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-bold text-slate-800 dark:text-slate-200 text-sm leading-tight">{{ $user->name }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs text-slate-500 dark:text-slate-500" dir="ltr">{{ $user->email }}</span>
                                    @php $roleName = $user->role?->name; @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $roleName === 'admin' ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400 border border-purple-100/30 dark:border-purple-800/30' : 'bg-sky-50 dark:bg-sky-900/20 text-sky-700 dark:text-sky-400 border border-sky-100/30 dark:border-sky-800/30' }}">
                                        {{ $user->role?->label_ar ?? $user->role?->name ?? __('settings.accountant') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-2 items-center opacity-100 md:opacity-0 group-hover:opacity-100 transition-opacity">
                            <button type="button" x-on:click="$wire.editUser({{ $user->id }})" class="text-slate-400 dark:text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-800 min-w-[44px] min-h-[44px] p-2 rounded-lg transition-colors cursor-pointer inline-flex items-center justify-center" title="{{ __('common.edit') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            @can('login-as')
                                @if($user->id !== auth()->id())
                                    <button wire:click="loginAsUser({{ $user->id }})" wire:confirm="{{ __('settings.confirm_login_as') }}" class="text-slate-400 dark:text-slate-500 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 min-w-[44px] min-h-[44px] p-2 rounded-lg transition-colors cursor-pointer inline-flex items-center justify-center" title="{{ __('settings.login_as') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                    </button>
                                @endif
                            @endcan
                            @if($user->id !== auth()->id())
                                <button wire:click="deleteUser({{ $user->id }})" wire:confirm="{{ __('settings.confirm_delete_user') }}" class="text-slate-400 dark:text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-slate-100 dark:hover:bg-slate-800 min-w-[44px] min-h-[44px] p-2 rounded-lg transition-colors cursor-pointer inline-flex items-center justify-center" title="{{ __('common.delete') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @if($users->hasPages())
                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800/60">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.addEventListener('scroll-to-form', () => {
        const el = document.getElementById('user-form');
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
</script>
@endpush

