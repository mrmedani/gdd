<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto space-y-6">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Messages WhatsApp</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Personnalisez le contenu des notifications WhatsApp.</p>
            </div>
            <a href="{{ route('settings.index') }}" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 transition-all text-sm">
                &larr; Retour
            </a>
        </div>

        @if(session()->has('saved'))
            <div class="bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 px-5 py-3 rounded-2xl text-sm font-bold">
                Message enregistré avec succès.
            </div>
        @endif

        @foreach($templates as $index => $template)
            @php $type = $template['type']; @endphp
            <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 p-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50/20 dark:bg-emerald-500/5 rounded-full blur-3xl -mr-16 -mt-16 z-0 pointer-events-none"></div>

                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-lg font-bold text-slate-800 dark:text-white">{{ $template['label_fr'] }}</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $template['label_ar'] }}</p>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Activé</span>
                            <input type="checkbox" wire:model.live="templates.{{ $index }}.enabled" class="sr-only peer">
                            <span class="relative w-10 h-5 bg-slate-200 dark:bg-slate-700 rounded-full transition-colors peer-checked:bg-emerald-600 after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:w-4 after:h-4 after:bg-white after:rounded-full after:transition-all peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full"></span>
                        </label>
                    </div>

                    <!-- Variables -->
                    <div class="mb-4 p-3 bg-slate-50/50 dark:bg-slate-950/40 rounded-xl border border-slate-200/40 dark:border-slate-800/60">
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Variables disponibles :</span>
                        <div class="flex flex-wrap gap-1.5 mt-2">
                            @foreach($template['variables'] ?? [] as $var)
                                <code class="px-2 py-0.5 bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 text-xs font-mono rounded-md border border-slate-200 dark:border-slate-700">{{ '{' . $var . '}' }}</code>
                            @endforeach
                        </div>
                    </div>

                    <!-- FR -->
                    <div class="mb-3">
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Français</label>
                        <textarea wire:model="templates.{{ $index }}.message_fr" rows="4" class="w-full px-4 py-3 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all text-slate-700 dark:text-slate-200 shadow-sm font-mono text-xs leading-relaxed"></textarea>
                    </div>

                    <!-- AR -->
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">العربية</label>
                        <textarea wire:model="templates.{{ $index }}.message_ar" rows="4" dir="rtl" class="w-full px-4 py-3 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all text-slate-700 dark:text-slate-200 shadow-sm font-mono text-xs leading-relaxed"></textarea>
                    </div>

                    <button wire:click="save('{{ $type }}')" wire:loading.class="opacity-50" class="px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-bold rounded-xl hover:from-emerald-600 hover:to-emerald-700 hover:shadow-emerald-500/30 transition-all shadow-md shadow-emerald-500/20 cursor-pointer text-sm">
                        {{ __('common.save') }}
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>
