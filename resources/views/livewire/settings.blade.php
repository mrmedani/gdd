<div class="max-w-6xl mx-auto space-y-8 animate-fade-in">

    <div class="mb-4">
        <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-700 to-indigo-600 dark:from-blue-400 dark:to-indigo-300 tracking-tight font-heading">
            {{ __('nav.settings') }}
        </h1>
        <p class="text-slate-500 dark:text-slate-500 text-sm mt-1.5 font-medium">{{ __('settings.desc') }}</p>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 relative z-10">
        <a href="{{ route('settings.categories') }}" class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200/50 dark:border-slate-800/60 p-5 rounded-3xl shadow-premium dark:shadow-premium-dark flex items-center gap-4 hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover hover:bg-blue-50/20 dark:hover:bg-slate-800/25 hover:border-blue-200 dark:hover:border-slate-700 transition-all duration-300 group">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-2xl flex items-center justify-center border border-blue-200/20 dark:border-blue-800 shadow-sm group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
            </div>
            <div>
                <h3 class="font-bold text-sm text-slate-800 dark:text-slate-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ __('settings.categories') }}</h3>
                <p class="text-[11px] text-slate-400 dark:text-slate-400 mt-0.5 leading-tight">{{ __('settings.categories_link_desc') }}</p>
            </div>
        </a>
        <a href="{{ route('settings.users') }}" class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200/50 dark:border-slate-800/60 p-5 rounded-3xl shadow-premium dark:shadow-premium-dark flex items-center gap-4 hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover hover:bg-emerald-50/20 dark:hover:bg-slate-800/25 hover:border-emerald-200 dark:hover:border-slate-700 transition-all duration-300 group">
            <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center border border-emerald-200/20 dark:border-emerald-800 shadow-sm group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <div>
                <h3 class="font-bold text-sm text-slate-800 dark:text-slate-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ __('settings.manage_users') }}</h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 leading-tight">{{ __('settings.users_link_desc') }}</p>
            </div>
        </a>
        <a href="{{ route('settings.audit-logs') }}" class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200/50 dark:border-slate-800/60 p-5 rounded-3xl shadow-premium dark:shadow-premium-dark flex items-center gap-4 hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover hover:bg-purple-50/20 dark:hover:bg-slate-800/25 hover:border-purple-200 dark:hover:border-slate-700 transition-all duration-300 group">
            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-2xl flex items-center justify-center border border-purple-200/20 dark:border-purple-800 shadow-sm group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <div>
                <h3 class="font-bold text-sm text-slate-800 dark:text-slate-100 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">{{ __('settings.audit_logs_link') }}</h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 leading-tight">{{ __('settings.audit_link_desc') }}</p>
            </div>
        </a>

        <a href="{{ route('settings.backup') }}" class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200/50 dark:border-slate-800/60 p-5 rounded-3xl shadow-premium dark:shadow-premium-dark flex items-center gap-4 hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover hover:bg-orange-50/20 dark:hover:bg-slate-800/25 hover:border-orange-200 dark:hover:border-slate-700 transition-all duration-300 group">
            <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 rounded-2xl flex items-center justify-center border border-orange-200/20 dark:border-orange-800 shadow-sm group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
            </div>
            <div>
                <h3 class="font-bold text-sm text-slate-800 dark:text-slate-100 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">{{ __('settings.backup_title') }}</h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 leading-tight">{{ __('settings.backup_link_desc') }}</p>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 relative z-10">
        <!-- Application Settings -->
        <div class="space-y-8">
            <!-- Threshold & Currency -->
            <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 p-6 relative overflow-hidden group hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50/20 dark:bg-blue-500/5 rounded-full blur-3xl -mr-16 -mt-16 z-0 pointer-events-none"></div>
                <div class="flex items-center gap-3 mb-6 relative z-10">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center border border-blue-200/20 dark:border-blue-800 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white">{{ __('settings.general') }}</h2>
                </div>

                <div class="space-y-6 relative z-10">
                    <!-- Threshold -->
                    <form wire:submit="updateThreshold" class="bg-slate-50/50 dark:bg-slate-950/40 p-4 rounded-2xl border border-slate-200/40 dark:border-slate-800/60 flex flex-col sm:flex-row gap-4 items-end">
                        <div class="flex-1 w-full">
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('settings.threshold') }}</label>
                            <input type="number" wire:model="threshold" class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-200 shadow-sm font-medium" dir="ltr">
                            @error('threshold') <span class="text-rose-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 hover:shadow-blue-500/30 transition-all shadow-md shadow-blue-500/20 cursor-pointer">
                            {{ __('common.save') }}
                        </button>
                    </form>

                    <!-- Currency -->
                    <form wire:submit="updateCurrency" class="bg-slate-50/50 dark:bg-slate-950/40 p-4 rounded-2xl border border-slate-200/40 dark:border-slate-800/60 flex flex-col sm:flex-row gap-4 items-end">
                        <div class="flex-1 w-full">
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('settings.currency') }}</label>
                            <input type="text" wire:model="currency" placeholder="MAD" class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-200 shadow-sm font-bold" dir="ltr">
                            @error('currency') <span class="text-rose-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 hover:shadow-blue-500/30 transition-all shadow-md shadow-blue-500/20 cursor-pointer">
                            {{ __('common.save') }}
                        </button>
                    </form>
                    
                    <!-- Cash Deficit -->
                    <form wire:submit="updateCashDeficit" class="bg-red-50/40 dark:bg-red-950/20 p-4 rounded-2xl border border-red-200/40 dark:border-red-900/30 flex flex-col sm:flex-row gap-4 items-end">
                        <div class="flex-1 w-full">
                            <label class="block text-xs font-bold text-red-600 dark:text-red-400 mb-2 uppercase tracking-wider">{{ __('settings.cash_deficit') }}</label>
                            <div class="relative">
                                <input type="number" inputmode="decimal" wire:model="cashDeficit" min="0" step="0.01" class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-red-200 dark:border-red-900 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 outline-none transition-all text-slate-700 dark:text-slate-200 shadow-sm font-medium {{ session('locale', 'ar') === 'ar' ? 'pl-14' : 'pr-14' }}" dir="ltr">
                                <span class="absolute top-1/2 -translate-y-1/2 {{ session('locale', 'ar') === 'ar' ? 'left-4' : 'right-4' }} text-red-400 dark:text-red-500 font-bold text-xs uppercase bg-red-50 dark:bg-red-950/50 px-2 py-0.5 rounded border border-red-200/50 dark:border-red-900/50">{{ getCurrency() }}</span>
                            </div>
                            <p class="text-[11px] text-red-500/70 dark:text-red-400/60 mt-1 font-medium">{{ __('settings.cash_deficit_desc') }}</p>
                            @error('cashDeficit') <span class="text-rose-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 hover:shadow-red-500/30 transition-all shadow-md shadow-red-500/20 cursor-pointer">
                            {{ __('common.save') }}
                        </button>
                    </form>

                    <!-- Language (per user) -->
                    <form wire:submit="updateLocale" class="bg-slate-50/50 dark:bg-slate-950/40 p-4 rounded-2xl border border-slate-200/40 dark:border-slate-800/60 flex flex-col sm:flex-row gap-4 items-end">
                        <div class="flex-1 w-full">
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('settings.language') }}</label>
                            <div class="relative">
                                <select wire:model="locale" class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-200 appearance-none shadow-sm font-semibold">
                                    <option value="ar">{{ __('settings.lang_ar') }}</option>
                                    <option value="fr">{{ __('settings.lang_fr') }}</option>
                                    <option value="en">{{ __('settings.lang_en') }}</option>
                                </select>
                                <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 absolute {{ session('locale', 'ar') === 'ar' ? 'left-4' : 'right-4' }} top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1 font-medium">{{ __('settings.language_user_desc') }}</p>
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 hover:shadow-blue-500/30 transition-all shadow-md shadow-blue-500/20 cursor-pointer">
                            {{ __('common.save') }}
                        </button>
                    </form>

                    <!-- Default Platform Language -->
                    <form wire:submit="updateDefaultLocale" class="bg-indigo-50/40 dark:bg-indigo-950/20 p-4 rounded-2xl border border-indigo-200/40 dark:border-indigo-900/30 flex flex-col sm:flex-row gap-4 items-end">
                        <div class="flex-1 w-full">
                            <label class="block text-xs font-bold text-indigo-600 dark:text-indigo-400 mb-2 uppercase tracking-wider">{{ __('settings.default_language') }}</label>
                            <div class="relative">
                                <select wire:model="defaultLocale" class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-indigo-200 dark:border-indigo-900 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all text-slate-700 dark:text-slate-200 appearance-none shadow-sm font-semibold">
                                    <option value="ar">{{ __('settings.lang_ar') }}</option>
                                    <option value="fr">{{ __('settings.lang_fr') }}</option>
                                    <option value="en">{{ __('settings.lang_en') }}</option>
                                </select>
                                <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 absolute {{ session('locale', 'ar') === 'ar' ? 'left-4' : 'right-4' }} top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                            <p class="text-[11px] text-indigo-500/70 dark:text-indigo-400/60 mt-1 font-medium">{{ __('settings.default_language_desc') }}</p>
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 hover:shadow-indigo-500/30 transition-all shadow-md shadow-indigo-500/20 cursor-pointer">
                            {{ __('common.save') }}
                        </button>
                    </form>

                    <!-- Month Period -->
                    <form wire:submit="updateMonthPeriod" class="bg-slate-50/50 dark:bg-slate-950/40 p-4 rounded-2xl border border-slate-200/40 dark:border-slate-800/60 flex flex-col sm:flex-row gap-4 items-end">
                        <div class="flex-1 w-full">
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('settings.month_period') }}</label>
                            <div class="flex gap-3 items-center">
                                <input type="number" wire:model="monthPeriodStartDay" min="1" max="28" class="w-24 px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-200 shadow-sm font-bold text-center" dir="ltr">
                                <span class="text-sm text-slate-500 dark:text-slate-400 font-medium">{{ __('settings.month_period_desc') }}</span>
                            </div>
                            @error('monthPeriodStartDay') <span class="text-rose-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 hover:shadow-blue-500/30 transition-all shadow-md shadow-blue-500/20 cursor-pointer">
                            {{ __('common.save') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Appearance Settings -->
            <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 p-6 relative overflow-hidden group hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
                <div class="absolute top-0 right-0 w-32 h-32 bg-pink-50/20 dark:bg-pink-500/5 rounded-full blur-3xl -mr-16 -mt-16 z-0 pointer-events-none"></div>
                <div class="flex items-center gap-3 mb-6 relative z-10">
                    <div class="w-10 h-10 bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400 rounded-xl flex items-center justify-center border border-pink-200/20 dark:border-pink-800 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white">Apparence / المظهر</h2>
                </div>

                <div class="space-y-6 relative z-10">
                    <!-- Logo -->
                    <form wire:submit="updateLogo" class="bg-slate-50/50 dark:bg-slate-950/40 p-4 rounded-2xl border border-slate-200/40 dark:border-slate-800/60 flex flex-col gap-4">
                        <div class="flex flex-col sm:flex-row gap-4 items-center">
                            @if($currentLogo = \App\Domains\Settings\Models\Setting::get('app_logo'))
                                <div class="w-16 h-16 rounded-xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 shadow-inner flex items-center justify-center p-1 shrink-0">
                                    <img src="{{ asset('storage/' . $currentLogo) }}" class="max-w-full max-h-full object-contain drop-shadow-sm">
                                </div>
                            @endif
                            <div class="flex-1 w-full">
                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Logo de l'application / شعار التطبيق</label>
                                <input type="file" wire:model="logo" accept="image/*" class="w-full px-4 py-2 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs outline-none transition-all text-slate-700 dark:text-slate-200 shadow-sm file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 dark:file:bg-blue-900/30 file:text-blue-700 dark:file:text-blue-400 hover:file:bg-blue-100 dark:hover:file:bg-blue-900/50">
                                @error('logo') <span class="text-rose-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                            </div>
                            <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-pink-600 text-white font-bold rounded-xl hover:bg-pink-700 hover:shadow-pink-500/30 transition-all shadow-md shadow-pink-500/20 cursor-pointer self-end">
                                {{ __('common.save') }}
                            </button>
                        </div>
                    </form>

                    <!-- App Name -->
                    <form wire:submit="updateAppName" class="bg-slate-50/50 dark:bg-slate-950/40 p-4 rounded-2xl border border-slate-200/40 dark:border-slate-800/60 flex flex-col sm:flex-row gap-4 items-end">
                        <div class="flex-1 w-full">
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Nom de la plateforme / اسم المنصة</label>
                            <input type="text" wire:model="appName" class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 outline-none transition-all text-slate-700 dark:text-slate-200 shadow-sm font-semibold">
                            @error('appName') <span class="text-rose-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-pink-600 text-white font-bold rounded-xl hover:bg-pink-700 hover:shadow-pink-500/30 transition-all shadow-md shadow-pink-500/20 cursor-pointer">
                            {{ __('common.save') }}
                        </button>
                    </form>

                    <!-- Favicon -->
                    <form wire:submit="updateFavicon" class="bg-slate-50/50 dark:bg-slate-950/40 p-4 rounded-2xl border border-slate-200/40 dark:border-slate-800/60 flex flex-col gap-4">
                        <div class="flex flex-col sm:flex-row gap-4 items-center">
                            @if($currentFavicon = \App\Domains\Settings\Models\Setting::get('app_favicon'))
                                <div class="w-10 h-10 rounded-xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 shadow-inner flex items-center justify-center p-1 shrink-0">
                                    <img src="{{ asset('storage/' . $currentFavicon) }}" class="max-w-full max-h-full object-contain">
                                </div>
                            @endif
                            <div class="flex-1 w-full">
                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Favicon (Icone d'onglet) / ايقونة التبويب</label>
                                <input type="file" wire:model="favicon" accept=".ico,.png,.jpg,.jpeg" class="w-full px-4 py-2 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs outline-none transition-all text-slate-700 dark:text-slate-200 shadow-sm file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 dark:file:bg-blue-900/30 file:text-blue-700 dark:file:text-blue-400 hover:file:bg-blue-100 dark:hover:file:bg-blue-900/50">
                                @error('favicon') <span class="text-rose-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                            </div>
                            <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-pink-600 text-white font-bold rounded-xl hover:bg-pink-700 hover:shadow-pink-500/30 transition-all shadow-md shadow-pink-500/20 cursor-pointer self-end">
                                {{ __('common.save') }}
                            </button>
                        </div>
                    </form>

                    <!-- Default Theme -->
                    <form wire:submit="updateDefaultTheme" class="bg-amber-50/40 dark:bg-amber-950/20 p-4 rounded-2xl border border-amber-200/40 dark:border-amber-900/30 flex flex-col sm:flex-row gap-4 items-end">
                        <div class="flex-1 w-full">
                            <label class="block text-xs font-bold text-amber-600 dark:text-amber-400 mb-2 uppercase tracking-wider">{{ __('settings.default_theme') }}</label>
                            <div class="flex gap-3 flex-wrap">
                                <label class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-950 border rounded-xl text-sm font-semibold cursor-pointer transition-all has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50/50 dark:has-[:checked]:bg-amber-950/30 has-[:checked]:shadow-sm border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300">
                                    <input type="radio" wire:model="defaultTheme" value="light" class="sr-only">
                                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    <span>{{ __('settings.theme_light') }}</span>
                                </label>
                                <label class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-950 border rounded-xl text-sm font-semibold cursor-pointer transition-all has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50/50 dark:has-[:checked]:bg-amber-950/30 has-[:checked]:shadow-sm border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300">
                                    <input type="radio" wire:model="defaultTheme" value="dark" class="sr-only">
                                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                                    <span>{{ __('settings.theme_dark') }}</span>
                                </label>
                                <label class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-950 border rounded-xl text-sm font-semibold cursor-pointer transition-all has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50/50 dark:has-[:checked]:bg-amber-950/30 has-[:checked]:shadow-sm border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300">
                                    <input type="radio" wire:model="defaultTheme" value="system" class="sr-only">
                                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <span>{{ __('settings.theme_system') }}</span>
                                </label>
                            </div>
                            <p class="text-[11px] text-amber-500/70 dark:text-amber-400/60 mt-1.5 font-medium">{{ __('settings.default_theme_desc') }}</p>
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-amber-600 text-white font-bold rounded-xl hover:bg-amber-700 hover:shadow-amber-500/30 transition-all shadow-md shadow-amber-500/20 cursor-pointer">
                            {{ __('common.save') }}
                        </button>
                    </form>

                    <!-- PWA Settings -->
                    <div class="bg-slate-50/50 dark:bg-slate-950/40 p-5 rounded-2xl border border-slate-200/40 dark:border-slate-800/60 space-y-5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl flex items-center justify-center border border-indigo-200/20 dark:border-indigo-800 shadow-sm shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">PWA / تطبيق الجوال</h3>
                        </div>

                        <form wire:submit="updatePwaSettings" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Short Name / الاسم القصير</label>
                                    <input type="text" wire:model="pwaShortName" maxlength="30" class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all text-slate-700 dark:text-slate-200 shadow-sm font-semibold">
                                    @error('pwaShortName') <span class="text-rose-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Description</label>
                                    <input type="text" wire:model="pwaDescription" maxlength="300" class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all text-slate-700 dark:text-slate-200 shadow-sm font-semibold">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Theme Color (clair) / لون السمة (فاتح)</label>
                                    <div class="flex gap-2 items-center">
                                        <input type="color" wire:model="pwaThemeColor" class="w-10 h-10 p-0.5 rounded-lg border border-slate-200 dark:border-slate-800 cursor-pointer bg-transparent">
                                        <input type="text" wire:model="pwaThemeColor" maxlength="7" class="flex-1 px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all text-slate-700 dark:text-slate-200 shadow-sm font-mono font-semibold">
                                    </div>
                                    @error('pwaThemeColor') <span class="text-rose-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Theme Color (sombre) / لون السمة (داكن)</label>
                                    <div class="flex gap-2 items-center">
                                        <input type="color" wire:model="pwaThemeColorDark" class="w-10 h-10 p-0.5 rounded-lg border border-slate-200 dark:border-slate-800 cursor-pointer bg-transparent">
                                        <input type="text" wire:model="pwaThemeColorDark" maxlength="7" class="flex-1 px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all text-slate-700 dark:text-slate-200 shadow-sm font-mono font-semibold">
                                    </div>
                                    @error('pwaThemeColorDark') <span class="text-rose-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Background Color / لون الخلفية</label>
                                    <div class="flex gap-2 items-center">
                                        <input type="color" wire:model="pwaBgColor" class="w-10 h-10 p-0.5 rounded-lg border border-slate-200 dark:border-slate-800 cursor-pointer bg-transparent">
                                        <input type="text" wire:model="pwaBgColor" maxlength="7" class="flex-1 px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all text-slate-700 dark:text-slate-200 shadow-sm font-mono font-semibold">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Display / وضع العرض</label>
                                    <div class="relative">
                                        <select wire:model="pwaDisplay" class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all text-slate-700 dark:text-slate-200 appearance-none font-semibold">
                                            <option value="standalone">Standalone</option>
                                            <option value="fullscreen">Fullscreen</option>
                                            <option value="minimal-ui">Minimal UI</option>
                                            <option value="browser">Browser</option>
                                        </select>
                                        <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 absolute {{ session('locale', 'ar') === 'ar' ? 'left-4' : 'right-4' }} top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Orientation / الاتجاه</label>
                                    <div class="relative">
                                        <select wire:model="pwaOrientation" class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all text-slate-700 dark:text-slate-200 appearance-none font-semibold">
                                            <option value="portrait-primary">Portrait</option>
                                            <option value="landscape-primary">Landscape</option>
                                            <option value="any">Any</option>
                                        </select>
                                        <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 absolute {{ session('locale', 'ar') === 'ar' ? 'left-4' : 'right-4' }} top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Icon (optionnel / اختياري) <span class="text-xs font-normal text-slate-400 dark:text-slate-500">512×512 PNG</span></label>
                                    <input type="file" wire:model="pwaIcon" accept="image/png" class="w-full px-4 py-2 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs outline-none transition-all text-slate-700 dark:text-slate-200 shadow-sm file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 dark:file:bg-indigo-900/30 file:text-indigo-700 dark:file:text-indigo-400 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-900/50">
                                    @error('pwaIcon') <span class="text-rose-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 hover:shadow-indigo-500/30 transition-all shadow-md shadow-indigo-500/20 cursor-pointer">
                                    {{ __('common.save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Mail Configuration -->
            <!-- WhatsApp Configuration -->
            <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 p-6 relative overflow-hidden group hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300"
                 wire:poll.10s="pollWhatsAppStatus">
                <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50/20 dark:bg-emerald-500/5 rounded-full blur-3xl -mr-16 -mt-16 z-0 pointer-events-none"></div>
                <div class="flex items-center gap-3 mb-6 relative z-10">
                    <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center border border-emerald-200/20 dark:border-emerald-800 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-white">WhatsApp</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-tight">Configuration du worker WhatsApp</p>
                    </div>
                </div>

                <form wire:submit="updateWhatsAppConfig" class="space-y-4 bg-slate-50/50 dark:bg-slate-950/40 p-5 rounded-2xl border border-slate-200/40 dark:border-slate-800/60 relative z-10">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">ID du Chat</label>
                            <input type="text" wire:model="whatsappChatId" class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all text-slate-700 dark:text-slate-200 shadow-sm font-semibold" dir="ltr" placeholder="2126XXXXXXXXX" @disabled($waStatus === 'connected' && $waPhone)>
                            @error('whatsappChatId') <span class="text-rose-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">URL du Worker</label>
                            <input type="text" wire:model="whatsappWorkerUrl" class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all text-slate-700 dark:text-slate-200 shadow-sm font-semibold" dir="ltr" placeholder="http://127.0.0.1:9090">
                            @error('whatsappWorkerUrl') <span class="text-rose-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" wire:click="$toggle('whatsappEnabled')"
                            class="relative w-10 h-5 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500/30 cursor-pointer @if($whatsappEnabled) bg-emerald-600 @else bg-slate-200 dark:bg-slate-700 @endif">
                            <span class="absolute top-0.5 start-[2px] w-4 h-4 bg-white border border-slate-300 rounded-full transition-transform duration-200 shadow-sm @if($whatsappEnabled) translate-x-full rtl:-translate-x-full @else translate-x-0 @endif"></span>
                        </button>
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ __('common.enabled') }}</span>
                    </div>
                    <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-emerald-500 to-green-600 text-white font-bold rounded-xl hover:from-emerald-600 hover:to-green-700 hover:shadow-emerald-500/30 transition-all shadow-md shadow-emerald-500/20 cursor-pointer mt-2">
                        {{ __('common.save') }}
                    </button>
                </form>

                    <!-- Message Delay -->
                    <form wire:submit="updateWhatsAppMessageDelay" class="bg-slate-50/50 dark:bg-slate-950/40 p-4 rounded-2xl border border-slate-200/40 dark:border-slate-800/60 flex flex-col sm:flex-row gap-4 items-end">
                        <div class="flex-1 w-full">
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Délai entre messages (secondes)</label>
                            <input type="number" wire:model="whatsappMessageDelay" min="1" max="30" class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all text-slate-700 dark:text-slate-200 shadow-sm font-bold" dir="ltr">
                            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1 font-medium">Délai aléatoire entre 1 et cette valeur après chaque message (anti-spam WhatsApp).</p>
                            @error('whatsappMessageDelay') <span class="text-rose-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 hover:shadow-emerald-500/30 transition-all shadow-md shadow-emerald-500/20 cursor-pointer">
                            {{ __('common.save') }}
                        </button>
                    </form>

                <div class="mt-6 space-y-4 relative z-10">
                    @if($waStatus === 'qr_ready')
                    <div class="bg-slate-50/50 dark:bg-slate-950/40 p-5 rounded-2xl border border-emerald-200/40 dark:border-emerald-800/40">
                        <div class="flex flex-col sm:flex-row items-center gap-6">
                            <div class="flex-shrink-0">
                                <div class="w-56 h-56 rounded-xl border-2 border-emerald-200 dark:border-emerald-800 bg-white dark:bg-slate-950 p-2 shadow-sm flex items-center justify-center">
                                    @if($waQr)
                                    <img src="{{ $waQr }}" class="w-full h-full object-contain">
                                    @else
                                    <svg class="animate-spin w-8 h-8 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-1 text-center sm:text-left">
                                <h3 class="text-lg font-bold text-emerald-700 dark:text-emerald-400 mb-2">Scannez le QR Code</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                                    Ouvrez WhatsApp sur votre téléphone et scannez le code QR ci-contre pour connecter votre appareil.
                                </p>
                                <div class="mt-4 flex items-center gap-2 justify-center sm:justify-start">
                                    <span class="px-3 py-1 text-xs font-bold rounded-lg bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                        QR prêt
                                    </span>
                                    <svg class="animate-spin w-4 h-4 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="bg-slate-50/50 dark:bg-slate-950/40 p-4 rounded-2xl border border-slate-200/40 dark:border-slate-800/60">
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold text-slate-800 dark:text-slate-200 text-sm">Statut</h3>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 text-xs font-bold rounded-lg {{
                                    match($waStatus) {
                                        'connected' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                        'auth_failure', 'error' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400',
                                        'starting' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                        default => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400',
                                    }
                                }}">
                                    {{ match($waStatus) {
                                        'connected' => 'Connecté',
                                        'auth_failure' => "Échec d'auth",
                                        'starting' => 'Démarrage...',
                                        'disconnected' => 'Déconnecté',
                                        default => 'Inconnu',
                                    } }}
                                </span>
                                @if($waStatus === 'starting')
                                <svg class="animate-spin w-4 h-4 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                @endif
                            </div>
                        </div>
                        @if($waStatus === 'connected' && $waPhone)
                        <div class="mt-2 text-xs text-slate-500 dark:text-slate-400 font-medium">
                            Connecté en tant que <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $waPhone }}</span>
                        </div>
                        @endif
                        @if($waStatus === 'starting')
                        <div class="mt-2 text-xs text-slate-500 dark:text-slate-400 font-medium">
                            Initialisation du navigateur Chrome...
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Actions (wrapped in forms for Livewire submit, since wire:click needs Alpine) -->
                    <div class="flex flex-wrap gap-3">
                        <form wire:submit="startWhatsAppWorker" class="contents">
                            <button type="submit"
                                class="px-5 py-2.5 font-bold rounded-xl transition-all shadow-md cursor-pointer
                                {{ $waStarting ? 'bg-slate-300 text-slate-500 dark:bg-slate-700 dark:text-slate-400 shadow-none cursor-not-allowed' : 'bg-emerald-600 text-white hover:bg-emerald-700 hover:shadow-emerald-500/30 shadow-emerald-500/20' }}"
                                @disabled($waStarting)>
                                @if($waStarting)
                                <span class="flex items-center gap-2">
                                    <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Démarrage...
                                </span>
                                @else
                                <span>Start Worker</span>
                                @endif
                            </button>
                        </form>
                        <form wire:submit="refreshQr" class="contents">
                            <button type="submit"
                                class="px-5 py-2.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 font-bold rounded-xl hover:bg-emerald-200 dark:hover:bg-emerald-900/50 border border-emerald-200/30 dark:border-emerald-800/40 transition-all cursor-pointer">
                                Refresh QR
                            </button>
                        </form>
                        <form wire:submit="disconnectWhatsApp" class="contents" onsubmit="return confirm('Êtes-vous sûr de vouloir déconnecter WhatsApp ?')">
                            <button type="submit"
                                class="px-5 py-2.5 bg-rose-600 text-white font-bold rounded-xl hover:bg-rose-700 hover:shadow-rose-500/30 transition-all shadow-md shadow-rose-500/20 cursor-pointer">
                                Disconnect
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Login Popup -->
            <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 p-6 relative overflow-hidden group hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50/20 dark:bg-blue-500/5 rounded-full blur-3xl -mr-16 -mt-16 z-0 pointer-events-none"></div>
                <div class="flex items-center gap-3 mb-6 relative z-10">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center border border-blue-200/20 dark:border-blue-800 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-white">Popup de connexion</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-tight">Popup affiché après la connexion d'un utilisateur.</p>
                    </div>
                </div>

                <form wire:submit="updateLoginPopup" class="space-y-5 bg-slate-50/50 dark:bg-slate-950/40 p-5 rounded-2xl border border-slate-200/40 dark:border-slate-800/60 relative z-10">
                    <div class="flex items-center gap-3">
                        <button type="button" wire:click="$toggle('loginPopupEnabled')"
                            class="relative w-10 h-5 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500/30 cursor-pointer @if($loginPopupEnabled) bg-blue-600 @else bg-slate-200 dark:bg-slate-700 @endif">
                            <span class="absolute top-0.5 start-[2px] w-4 h-4 bg-white border border-slate-300 rounded-full transition-transform duration-200 shadow-sm @if($loginPopupEnabled) translate-x-full rtl:-translate-x-full @else translate-x-0 @endif"></span>
                        </button>
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Activé</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Contenu du popup</label>
                        <textarea wire:model="loginPopupContent" rows="6" class="w-full px-4 py-3 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-200 shadow-sm font-medium" placeholder="Écrivez votre message ici..."></textarea>
                        @error('loginPopupContent') <span class="text-rose-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold rounded-xl hover:from-blue-600 hover:to-indigo-700 hover:shadow-blue-500/30 transition-all shadow-md shadow-blue-500/20 cursor-pointer">
                        {{ __('common.save') }}
                    </button>
                </form>
            </div>

            <!-- Password Update -->
            <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 p-6 relative overflow-hidden group hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50/20 dark:bg-indigo-500/5 rounded-full blur-3xl -mr-16 -mt-16 z-0 pointer-events-none"></div>
                <div class="flex items-center gap-3 mb-6 relative z-10">
                    <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl flex items-center justify-center border border-indigo-200/20 dark:border-indigo-800 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white">{{ __('settings.password') }}</h2>
                </div>

                <form wire:submit="updatePassword" class="space-y-4 bg-slate-50/50 dark:bg-slate-950/40 p-5 rounded-2xl border border-slate-200/40 dark:border-slate-800/60 relative z-10">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('settings.current_password') }}</label>
                        <input type="password" wire:model="passwordCurrent" class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all text-slate-700 dark:text-slate-200 shadow-sm font-bold" dir="ltr">
                        @error('passwordCurrent') <span class="text-rose-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('settings.new_password') }}</label>
                        <input type="password" wire:model="passwordNew" class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all text-slate-700 dark:text-slate-200 shadow-sm font-bold" dir="ltr">
                        @error('passwordNew') <span class="text-rose-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('settings.confirm_password') }}</label>
                        <input type="password" wire:model="passwordConfirm" class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all text-slate-700 dark:text-slate-200 shadow-sm font-bold" dir="ltr">
                    </div>
                    <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-indigo-500 to-blue-600 text-white font-bold rounded-xl hover:from-indigo-600 hover:to-blue-700 hover:shadow-indigo-500/30 transition-all shadow-md shadow-indigo-500/20 cursor-pointer mt-2">
                        {{ __('settings.update_password') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Danger Zone & Alerts -->
        <div class="space-y-8">
            <div class="bg-rose-50/50 dark:bg-rose-950/15 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark border border-rose-200/50 dark:border-rose-900/40 p-6 relative overflow-hidden group hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
                <div class="absolute top-0 right-0 w-32 h-32 bg-rose-50/20 dark:bg-rose-500/5 rounded-full blur-3xl -mr-16 -mt-16 z-0 pointer-events-none"></div>
                <div class="flex items-center gap-3 mb-6 relative z-10">
                    <div class="w-10 h-10 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-500 rounded-xl flex items-center justify-center border border-rose-200/20 dark:border-rose-800 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-rose-700 dark:text-rose-400">{{ __('settings.danger_zone', ['default' => 'Zone Dangereuse']) }}</h2>
                        <p class="text-xs text-rose-600/80 dark:text-rose-400/80 mt-0.5 leading-tight">{{ __('settings.danger_zone_desc', ['default' => 'Attention, ces actions sont irreversibles.']) }}</p>
                    </div>
                </div>

                <div class="bg-white/50 dark:bg-slate-950/40 p-5 rounded-2xl border border-rose-100/30 dark:border-rose-900/20 relative z-10">
                    <h3 class="font-bold text-slate-800 dark:text-slate-200 text-sm mb-1">{{ __('settings.delete_all_data', ['default' => 'Supprimer toutes les donnees']) }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-500 mb-4 leading-relaxed">{{ __('settings.delete_all_data_desc', ['default' => 'Supprime l\'historique complet des charges, revenus et clotures.']) }}</p>
                    
                    <button wire:click="deleteAllData" 
                            wire:confirm="{{ __('settings.confirm_delete_all', ['default' => 'Etes-vous sur de vouloir supprimer absolument toutes les donnees de la caisse et des charges ? Cette action est irreversible !']) }}" 
                            class="w-full sm:w-auto px-6 py-3 bg-rose-600 text-white font-bold rounded-xl hover:bg-rose-700 hover:shadow-rose-600/30 transition-all shadow-md shadow-rose-500/20 flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        {{ __('settings.delete_all_btn', ['default' => 'Tout supprimer']) }}
                    </button>
                </div>
            </div>

            <!-- Alerts -->
            <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 p-6 relative overflow-hidden group hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300 flex flex-col h-fit">
                <div class="absolute top-0 right-0 w-32 h-32 bg-amber-50/20 dark:bg-amber-500/5 rounded-full blur-3xl -mr-16 -mt-16 z-0 pointer-events-none"></div>
                <div class="flex justify-between items-center mb-6 border-b border-slate-100 dark:border-slate-800/60 pb-4 relative z-10">
                    <div class="flex items-center gap-3">
                        <div class="relative w-10 h-10 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl flex items-center justify-center border border-amber-200/20 dark:border-amber-800 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            @if($unreadAlerts > 0)
                                <span class="absolute -top-1 -right-1 w-3 h-3 bg-rose-500 rounded-full border-2 border-white dark:border-slate-900"></span>
                            @endif
                        </div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-white">{{ __('settings.alerts') }}</h2>
                    </div>
                    @if($unreadAlerts > 0)
                        <button wire:click="markAlertsRead" class="text-xs font-bold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 px-3 py-1.5 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-900/40 border border-amber-200/20 dark:border-amber-800/30 transition-colors cursor-pointer">
                            {{ __('settings.mark_read') }}
                        </button>
                    @endif
                </div>

                {{-- Alert Preferences --}}
                @if(auth()->user()->isAdmin())
                <div class="mb-4 p-4 bg-blue-50/40 dark:bg-blue-950/20 rounded-2xl border border-blue-100/50 dark:border-blue-900/30 relative z-10">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-3">{{ __('settings.alert_preferences') }}</h3>
                    <div class="flex flex-wrap gap-2 mb-3">
                        @foreach($this->allAlertTypes as $type)
                            @php
                                $label = __("alerts.type.{$type}");
                                $isSelected = in_array($type, $alertPreferences);
                            @endphp
                            <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border cursor-pointer transition-colors
                                {{ $isSelected ? 'bg-blue-600 text-white border-blue-600' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                                <input type="checkbox" value="{{ $type }}" wire:model.live="alertPreferences" class="sr-only">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    <button wire:click="updateAlertPreferences" class="px-4 py-1.5 bg-blue-600 text-white text-xs font-bold rounded-lg hover:bg-blue-700 transition-colors cursor-pointer">
                        {{ __('settings.save_preferences') }}
                    </button>
                </div>
                @endif

                <div class="space-y-3 flex-1 overflow-y-auto pr-1 relative z-10 max-h-[350px]">
                    @forelse($alerts as $alert)
                        @php
                            $severityStyles = [
                                'info' => ['bg' => 'bg-sky-50/40 dark:bg-sky-950/20', 'border' => 'border-sky-200/40 dark:border-sky-800/40', 'label' => 'text-sky-700 dark:text-sky-300', 'badge' => 'bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400 border-sky-200/30 dark:border-sky-800/30'],
                                'warning' => ['bg' => 'bg-amber-50/40 dark:bg-amber-950/20', 'border' => 'border-amber-200/40 dark:border-amber-800/40', 'label' => 'text-amber-700 dark:text-amber-300', 'badge' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border-amber-200/30 dark:border-amber-800/30'],
                                'error' => ['bg' => 'bg-rose-50/40 dark:bg-rose-950/20', 'border' => 'border-rose-200/40 dark:border-rose-800/40', 'label' => 'text-rose-700 dark:text-rose-300', 'badge' => 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 border-rose-200/30 dark:border-rose-800/30'],
                                'success' => ['bg' => 'bg-emerald-50/40 dark:bg-emerald-950/20', 'border' => 'border-emerald-200/40 dark:border-emerald-800/40', 'label' => 'text-emerald-700 dark:text-emerald-300', 'badge' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border-emerald-200/30 dark:border-emerald-800/30'],
                            ];
                            $sev = $alert->severity ?? 'info';
                            $style = $severityStyles[$sev] ?? $severityStyles['info'];
                            $typeLabel = __("alerts.type.{$alert->type}");
                            $severityLabel = __("alerts.severity.{$sev}");
                        @endphp
                        <div class="p-4 rounded-xl border transition-all hover:shadow-sm {{ $style['bg'] }} {{ $style['border'] }} {{ $alert->is_read ? 'opacity-75' : '' }}">
                            <div class="flex justify-between items-start gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold border {{ $style['badge'] }}">{{ $typeLabel }}</span>
                                        <span class="text-[10px] font-bold {{ $style['label'] }}">{{ $severityLabel }}</span>
                                        @if(!$alert->is_read)
                                            <span class="px-2 py-0.5 bg-amber-100/60 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-[9px] font-bold rounded-full">{{ __('settings.new_alert') }}</span>
                                        @endif
                                    </div>
                                    <p class="text-xs leading-relaxed {{ $alert->is_read ? 'text-slate-600 dark:text-slate-400' : 'text-slate-800 dark:text-slate-200 font-bold' }}">
                                        {{ $alert->{'message_' . app()->getLocale()} ?? $alert->message_fr ?? $alert->message_ar }}
                                    </p>
                                </div>
                                <div class="flex flex-col items-end gap-2 shrink-0">
                                    <button wire:click="deleteAlert({{ $alert->id }})" wire:confirm="{{ __('settings.confirm_delete_alert') }}" class="text-slate-400 dark:text-slate-500 hover:text-rose-600 dark:hover:text-rose-500 bg-white dark:bg-slate-950 hover:bg-rose-50 dark:hover:bg-rose-900/20 min-w-[44px] min-h-[44px] p-1.5 rounded-xl border border-slate-200/30 dark:border-slate-800 shadow-sm cursor-pointer inline-flex items-center justify-center" title="{{ __('common.delete') }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-2 font-bold flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $alert->created_at->diffForHumans() }}
                            </p>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center h-full text-slate-500 dark:text-slate-500 py-12">
                            <svg class="w-12 h-12 text-slate-200 dark:text-slate-800 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            <p class="font-bold text-xs uppercase tracking-wider">{{ __('settings.no_alerts') }}</p>
                        </div>
                    @endforelse
                </div>
                @if($alerts->hasPages())
                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800/60">
                        {{ $alerts->links(data: ['pageName' => 'alerts_page']) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if(auth()->user()->isAdmin())
    <!-- Cron Jobs Section -->
    <div class="space-y-4 relative z-10">
        <div class="bg-indigo-50/30 dark:bg-indigo-950/10 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark border border-indigo-200/50 dark:border-indigo-900/40 p-6 relative overflow-hidden group hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50/20 dark:bg-indigo-500/5 rounded-full blur-3xl -mr-16 -mt-16 z-0 pointer-events-none"></div>
            <!-- Header -->
            <div class="flex items-center gap-4 mb-6 relative z-10">
                <div class="p-3 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-2xl border border-indigo-200/20 dark:border-indigo-800 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-indigo-800 dark:text-indigo-300">{{ __('settings.cron_jobs_title') }}</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-500 mt-0.5 font-medium">{{ __('settings.cron_jobs_desc') }}</p>
                </div>
            </div>

            <!-- Main Cron Command -->
            <div x-data="{ copied: false }" class="bg-blue-50/60 dark:bg-blue-950/20 border border-blue-200/30 dark:border-blue-900/30 rounded-2xl p-4 mb-6">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-blue-700 dark:text-blue-400">Pour l'automatisation complète, ajoutez cette ligne dans <strong>cPanel → Cron Jobs</strong> :</p>
                        <div class="flex items-center gap-2 mt-2">
                            <code id="cron-main" class="flex-1 text-[11px] bg-white dark:bg-slate-950 text-slate-600 dark:text-slate-400 px-3 py-2 rounded-lg font-mono border border-blue-200/30 dark:border-slate-800/30 break-all select-all">* * * * * cd /home/gestion/public_html && php artisan schedule:run >> /dev/null 2>&1</code>
                            <button @click="copyCommand('cron-main', $el); copied = true; setTimeout(() => copied = false, 2000)" class="shrink-0 p-2 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg hover:bg-blue-50 dark:hover:bg-slate-800 transition-colors cursor-pointer" title="Copier">
                                <svg x-show="!copied" class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                <svg x-show="copied" class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cron Job Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative z-10">

                <!-- High Expenses Check -->
                <div x-data="{ copied: false }" class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-2xl p-5 border border-slate-200/50 dark:border-slate-800/60 flex flex-col justify-between gap-4 hover:shadow-md transition-all">
                    <div class="flex items-start gap-3">
                        <div class="p-2.5 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl shrink-0 border border-amber-200/20 dark:border-amber-800 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-slate-800 dark:text-slate-100 text-sm leading-tight">{{ __('settings.cron_high_expenses') }}</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-500 mt-1 font-semibold leading-relaxed">{{ __('settings.cron_high_expenses_desc') }}</p>
                            <div class="flex items-center gap-1.5 mt-2">
                                <code id="cron-high-expenses" class="flex-1 text-xs bg-slate-100 dark:bg-slate-950 text-slate-600 dark:text-slate-400 px-2 py-1.5 rounded-lg font-mono border border-slate-200/30 dark:border-slate-800/30 break-all select-all">0 9 * * * cd /home/gestion/public_html && php artisan alerts:high-expenses</code>
                                <button @click="copyCommand('cron-high-expenses', $el); copied = true; setTimeout(() => copied = false, 2000)" class="shrink-0 p-1.5 bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors cursor-pointer" title="Copier">
                                    <svg x-show="!copied" class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    <svg x-show="copied" class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button
                        wire:click="runCronJob('alerts:high-expenses')"
                        wire:loading.attr="disabled"
                        wire:target="runCronJob('alerts:high-expenses')"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl transition-all shadow-sm shadow-amber-400/30 hover:shadow-amber-500/40 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">
                        <svg wire:loading.remove wire:target="runCronJob('alerts:high-expenses')" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <svg wire:loading wire:target="runCronJob('alerts:high-expenses')" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('settings.cron_run_btn') }}
                    </button>
                </div>

                <!-- Salary Reminders -->
                <div x-data="{ copied: false }" class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-2xl p-5 border border-slate-200/50 dark:border-slate-800/60 flex flex-col justify-between gap-4 hover:shadow-md transition-all">
                    <div class="flex items-start gap-3">
                        <div class="p-2.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl shrink-0 border border-emerald-200/20 dark:border-emerald-800 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-slate-800 dark:text-slate-100 text-sm leading-tight">{{ __('settings.cron_salary_reminders') }}</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-500 mt-1 font-semibold leading-relaxed">{{ __('settings.cron_salary_reminders_desc') }}</p>
                            <div class="flex items-center gap-1.5 mt-2">
                                <code id="cron-salary-reminders" class="flex-1 text-xs bg-slate-100 dark:bg-slate-950 text-slate-600 dark:text-slate-400 px-2 py-1.5 rounded-lg font-mono border border-slate-200/30 dark:border-slate-800/30 break-all select-all">0 8 1 * * cd /home/gestion/public_html && php artisan alerts:salary-reminders</code>
                                <button @click="copyCommand('cron-salary-reminders', $el); copied = true; setTimeout(() => copied = false, 2000)" class="shrink-0 p-1.5 bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors cursor-pointer" title="Copier">
                                    <svg x-show="!copied" class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    <svg x-show="copied" class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button
                        wire:click="runCronJob('alerts:salary-reminders')"
                        wire:loading.attr="disabled"
                        wire:target="runCronJob('alerts:salary-reminders')"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold rounded-xl transition-all shadow-sm shadow-emerald-400/30 hover:shadow-emerald-500/40 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">
                        <svg wire:loading.remove wire:target="runCronJob('alerts:salary-reminders')" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <svg wire:loading wire:target="runCronJob('alerts:salary-reminders')" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('settings.cron_run_btn') }}
                    </button>
                </div>

                <!-- Database Backup -->
                <div x-data="{ copied: false }" class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-2xl p-5 border border-slate-200/50 dark:border-slate-800/60 flex flex-col justify-between gap-4 hover:shadow-md transition-all">
                    <div class="flex items-start gap-3">
                        <div class="p-2.5 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl shrink-0 border border-blue-200/20 dark:border-blue-800 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-slate-800 dark:text-slate-100 text-sm leading-tight">{{ __('settings.cron_backup') }}</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-500 mt-1 font-semibold leading-relaxed">{{ __('settings.cron_backup_desc') }}</p>
                            <div class="flex items-center gap-1.5 mt-2">
                                <code id="cron-backup" class="flex-1 text-xs bg-slate-100 dark:bg-slate-950 text-slate-600 dark:text-slate-400 px-2 py-1.5 rounded-lg font-mono border border-slate-200/30 dark:border-slate-800/30 break-all select-all">0 2 * * 0 cd /home/gestion/public_html && php artisan backup:database</code>
                                <button @click="copyCommand('cron-backup', $el); copied = true; setTimeout(() => copied = false, 2000)" class="shrink-0 p-1.5 bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors cursor-pointer" title="Copier">
                                    <svg x-show="!copied" class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    <svg x-show="copied" class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button
                        wire:click="runCronJob('backup:database')"
                        wire:loading.attr="disabled"
                        wire:target="runCronJob('backup:database')"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-bold rounded-xl transition-all shadow-sm shadow-blue-400/30 hover:shadow-blue-500/40 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">
                        <svg wire:loading.remove wire:target="runCronJob('backup:database')" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <svg wire:loading wire:target="runCronJob('backup:database')" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('settings.cron_run_btn') }}
                    </button>
                </div>

            </div>

                <!-- Clear Cache -->
                <div x-data="{ copied: false }" class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-2xl p-5 border border-slate-200/50 dark:border-slate-800/60 flex flex-col justify-between gap-4 hover:shadow-md transition-all">
                    <div class="flex items-start gap-3">
                        <div class="p-2.5 bg-violet-100 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400 rounded-xl shrink-0 border border-violet-200/20 dark:border-violet-800 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-slate-800 dark:text-slate-100 text-sm leading-tight">{{ __('settings.clear_cache') }}</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-500 mt-1 font-semibold leading-relaxed">{{ __('settings.clear_cache_desc') }}</p>
                            <div class="flex items-center gap-1.5 mt-2">
                                <code id="cron-cache" class="flex-1 text-xs bg-slate-100 dark:bg-slate-950 text-slate-600 dark:text-slate-400 px-2 py-1.5 rounded-lg font-mono border border-slate-200/30 dark:border-slate-800/30 break-all select-all">0 * * * * cd /home/gestion/public_html && php artisan optimize:clear</code>
                                <button @click="copyCommand('cron-cache', $el); copied = true; setTimeout(() => copied = false, 2000)" class="shrink-0 p-1.5 bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors cursor-pointer" title="Copier">
                                    <svg x-show="!copied" class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    <svg x-show="copied" class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button
                        wire:click="clearCache"
                        wire:loading.attr="disabled"
                        wire:target="clearCache"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-violet-500 hover:bg-violet-600 text-white text-xs font-bold rounded-xl transition-all shadow-sm shadow-violet-400/30 hover:shadow-violet-500/40 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">
                        <svg wire:loading.remove wire:target="clearCache" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <svg wire:loading wire:target="clearCache" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('settings.clear_cache_btn') }}
                    </button>
                </div>

            </div>
        </div>
    </div>
@endif

</div>

@push('scripts')
<script>
function copyCommand(id, btn) {
    const text = document.getElementById(id).textContent.trim();
    navigator.clipboard.writeText(text).catch(function() {
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
    });
}

document.addEventListener('livewire:init', () => {
    Livewire.on('cache-cleared', () => {
        // Clear all browser caches (Service Worker)
        if ('caches' in window) {
            caches.keys().then(names => {
                names.forEach(name => caches.delete(name));
            });
        }
        // Unregister all Service Workers
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistrations().then(regs => {
                regs.forEach(reg => reg.unregister());
            });
        }
        // Reload after a short delay
        setTimeout(() => window.location.reload(), 500);
    });
});
</script>
@endpush

