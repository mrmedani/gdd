<div class="max-w-5xl mx-auto space-y-8 animate-fade-in">
    <!-- Header Section -->
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-700 to-indigo-600 dark:from-blue-400 dark:to-indigo-300 tracking-tight font-heading">
                {{ __('profile.title', ['default' => 'Mon Profil']) }}
            </h1>
            <p class="text-slate-500 dark:text-slate-500 text-sm mt-1.5 font-medium">{{ __('profile.subtitle', ['default' => 'Gerez vos informations personnelles et votre securite']) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 relative z-10">
        
        <!-- Left Column: Personal Info & Photo -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 p-6 sm:p-8 relative overflow-hidden group hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50/20 dark:bg-blue-500/5 rounded-full blur-3xl -mr-16 -mt-16 z-0 pointer-events-none"></div>
                <h2 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2 mb-6 relative z-10">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ __('profile.personal_info', ['default' => 'Informations Personnelles']) }}
                </h2>

                <form wire:submit="updateProfile" class="space-y-6 relative z-10">
                    <!-- Photo Upload Area -->
                    <div class="flex flex-col sm:flex-row items-center gap-6 p-5 bg-slate-50/50 dark:bg-slate-950/40 rounded-2xl border border-slate-200/30 dark:border-slate-800/60">
                        <div class="relative shrink-0 group">
                            @if ($photo)
                                @php $previewable = in_array($photo->getClientOriginalExtension(), ['jpg','jpeg','png','gif','webp','bmp','svg']); @endphp
                                @if ($previewable)
                                    <img src="{{ $photo->temporaryUrl() }}" class="w-24 h-24 rounded-full object-cover border-4 border-white dark:border-slate-800 shadow-lg">
                                @else
                                    <div class="w-24 h-24 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold text-3xl border-4 border-white dark:border-slate-800 shadow-lg">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                            @elseif (auth()->user()->photo)
                                <img src="{{ asset('storage/' . auth()->user()->photo) }}" class="w-24 h-24 rounded-full object-cover border-4 border-white dark:border-slate-800 shadow-lg">
                            @else
                                <div class="w-24 h-24 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold text-3xl border-4 border-white dark:border-slate-800 shadow-lg">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            @endif
                            
                            <!-- Loading indicator for photo upload -->
                            <div wire:loading wire:target="photo" class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center backdrop-blur-sm z-10">
                                <svg class="animate-spin h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </div>
                        </div>

                        <div class="flex-1 text-center sm:text-start space-y-3">
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold">{{ __('profile.photo_help', ['max' => $maxPhotoSize >= 1024 ? (int)($maxPhotoSize / 1024) . 'MB' : $maxPhotoSize . 'KB', 'default' => 'Formats acceptes : JPG, PNG, GIF. Taille maximale : :max.']) }}</p>
                            
                            <div class="flex flex-wrap justify-center sm:justify-start gap-3">
                                <label class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    {{ __('profile.upload_photo', ['default' => 'Changer de photo']) }}
                                    <input type="file" wire:model="photo" accept="image/*" class="hidden">
                                </label>
                                
                                @if(auth()->user()->photo)
                                    <button type="button" wire:click="removePhoto" wire:confirm="{{ __('profile.confirm_remove_photo', ['default' => 'Voulez-vous vraiment supprimer votre photo ?']) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-400 text-xs font-bold rounded-xl hover:bg-rose-100 dark:hover:bg-rose-950/45 transition-colors cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        {{ __('profile.remove_photo', ['default' => 'Supprimer']) }}
                                    </button>
                                @endif
                            </div>
                            @error('photo') <span class="text-xs text-rose-500 font-bold block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Name & Email -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1 uppercase tracking-wider">{{ __('profile.name', ['default' => 'Nom complet']) }}</label>
                            <input type="text" wire:model="name" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-white font-medium" required>
                            @error('name') <span class="text-xs text-rose-500 font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1 uppercase tracking-wider">{{ __('profile.email', ['default' => 'Adresse Email']) }}</label>
                            <input type="email" wire:model="email" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-white font-medium" required dir="ltr">
                            @error('email') <span class="text-xs text-rose-500 font-bold block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-blue-500/20 hover:shadow-blue-500/40 hover:-translate-y-0.5 transition-all duration-305 cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            {{ __('profile.save_changes', ['default' => 'Enregistrer les modifications']) }}
                            
                            <svg wire:loading wire:target="updateProfile" class="animate-spin -ms-1 me-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column: Notifications & Security -->
        <div class="space-y-8">
            <!-- WhatsApp Notifications -->
            <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 p-6 sm:p-8 relative overflow-hidden group hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
                <div class="absolute top-0 right-0 w-32 h-32 bg-green-50/20 dark:bg-green-500/5 rounded-full blur-3xl -mr-16 -mt-16 z-0 pointer-events-none"></div>
                <h2 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2 mb-6 relative z-10">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    {{ __('profile.whatsapp_title', ['default' => 'Notifications WhatsApp']) }}
                </h2>

                <div class="space-y-5 relative z-10">
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1 uppercase tracking-wider">{{ __('profile.whatsapp_phone', ['default' => 'Numéro WhatsApp']) }}</label>
                        <input type="text" wire:model.blur="whatsapp_phone" wire:change="updateWhatsAppPhone" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-green-500/20 focus:border-green-500 outline-none transition-all text-slate-700 dark:text-white font-medium" dir="ltr" placeholder="+212XXXXXXXXX">
                        @error('whatsapp_phone') <span class="text-xs text-rose-500 font-bold block">{{ $message }}</span> @enderror
                        <p class="text-xs text-slate-400 mt-1">{{ __('profile.whatsapp_phone_help', ['default' => 'Numéro avec indicatif (ex: +2126XXXXXXXX)']) }}</p>
                    </div>

                    <button type="button" wire:click="toggleWhatsApp" wire:loading.attr="disabled" class="relative flex items-start gap-3 p-4 bg-slate-50/50 dark:bg-slate-950/40 rounded-2xl border border-slate-200/30 dark:border-slate-800/60 cursor-pointer w-full text-start">
                        <div class="w-10 h-6 rounded-full transition-colors relative shrink-0 mt-0.5 after:content-[''] after:absolute after:top-0.5 after:start-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all {{ $notifyWhatsapp ? 'bg-green-500 after:translate-x-4' : 'bg-slate-300 dark:bg-slate-700 after:translate-x-0' }}"></div>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ __('profile.whatsapp_enable', ['default' => 'Recevoir les notifications WhatsApp']) }}</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ __('profile.whatsapp_enable_help', ['default' => 'Recevez les alertes de dépenses, rappels de salaires et rapports directement sur WhatsApp']) }}</span>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Security -->
            <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 p-6 sm:p-8 relative overflow-hidden group hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300 h-full">
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50/20 dark:bg-indigo-500/5 rounded-full blur-3xl -mr-16 -mt-16 z-0 pointer-events-none"></div>
                <h2 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2 mb-6 relative z-10">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    {{ __('profile.security', ['default' => 'Securite']) }}
                </h2>

                <form wire:submit="updatePassword" class="space-y-5 relative z-10">
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1 uppercase tracking-wider">{{ __('profile.current_password', ['default' => 'Mot de passe actuel']) }}</label>
                        <input type="password" wire:model="current_password" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-white font-medium" required dir="ltr">
                        @error('current_password') <span class="text-xs text-rose-500 font-bold block">{{ $message }}</span> @enderror
                    </div>

                    <div class="w-full h-px bg-slate-200/50 dark:bg-slate-800/60 my-4"></div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1 uppercase tracking-wider">{{ __('profile.new_password', ['default' => 'Nouveau mot de passe']) }}</label>
                        <input type="password" wire:model="new_password" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-white font-medium" required dir="ltr">
                        @error('new_password') <span class="text-xs text-rose-500 font-bold block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1 uppercase tracking-wider">{{ __('profile.confirm_password', ['default' => 'Confirmer le mot de passe']) }}</label>
                        <input type="password" wire:model="new_password_confirmation" class="w-full px-4 py-3 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-white font-medium" required dir="ltr">
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-slate-800 dark:bg-slate-700 text-white font-bold rounded-xl hover:bg-slate-900 dark:hover:bg-slate-600 transition-colors cursor-pointer border border-transparent dark:border-slate-700/50 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                            {{ __('profile.update_password', ['default' => 'Modifier le mot de passe']) }}
                            
                            <svg wire:loading wire:target="updatePassword" class="animate-spin -ms-1 me-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

