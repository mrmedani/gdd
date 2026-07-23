<!DOCTYPE html>
<html dir="{{ session('locale', 'ar') === 'ar' ? 'rtl' : 'ltr' }}" lang="{{ session('locale', 'ar') }}" x-data="{ dark: localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches) || (!('theme' in localStorage) && !window.matchMedia('(prefers-color-scheme: dark)').matches && '{{ \App\Domains\Settings\Models\Setting::get('default_theme', 'system') }}' === 'dark'), sidebar: false }" x-init="$watch('dark', val => localStorage.theme = val ? 'dark' : 'light')" :class="{ 'dark': dark }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) document.documentElement.classList.add('dark');
        
        // Sync meta theme-color with manual dark mode toggle
        function updateThemeColor() {
            const isDark = document.documentElement.classList.contains('dark');
            const themeColorMeta = document.querySelector('meta[name="theme-color"]:not([media])') || document.createElement('meta');
            themeColorMeta.name = "theme-color";
            
            // Get colors from meta tags if they exist, fallback to injected colors
            const lightColor = '{{ $pwaThemeColor ?? "#2563eb" }}';
            const darkColor = '{{ $pwaThemeColorDark ?? "#0f172a" }}';
            
            themeColorMeta.content = isDark ? darkColor : lightColor;
            
            // Remove media-based theme-colors once manual toggle is used
            document.querySelectorAll('meta[name="theme-color"][media]').forEach(el => el.remove());
            document.head.appendChild(themeColorMeta);
        }
        
        // Watch for manual toggle
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'class') {
                    updateThemeColor();
                }
            });
        });
        document.addEventListener('DOMContentLoaded', () => {
            observer.observe(document.documentElement, { attributes: true });
        });
    </script>
    <title>@yield('title', config('app.name'))</title>
    @php $appFavicon = \App\Domains\Settings\Models\Setting::get('app_favicon'); @endphp
    @if($appFavicon)
        <link rel="icon" href="{{ asset('storage/' . $appFavicon) }}" type="image/png">
    @endif
    @php
        $pwaThemeColor = \App\Domains\Settings\Models\Setting::get('pwa_theme_color', '#2563eb');
        $pwaThemeColorDark = \App\Domains\Settings\Models\Setting::get('pwa_theme_color_dark', '#0f172a');
        $pwaShortName = \App\Domains\Settings\Models\Setting::get('pwa_short_name', 'Chronorex');
        $pwaCustomIcon = \App\Domains\Settings\Models\Setting::get('pwa_icon', null);
        $pwaIconUrl = $pwaCustomIcon ? asset('storage/' . $pwaCustomIcon) : '/icons/icon-192x192.png';
    @endphp
    <script>
        // Store PHP values in JS variables for the script at the top
        window.pwaThemeColor = '{{ $pwaThemeColor }}';
        window.pwaThemeColorDark = '{{ $pwaThemeColorDark }}';
    </script>
    <link rel="manifest" href="{{ route('manifest.json') }}">
    <meta name="theme-color" content="{{ $pwaThemeColor }}" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="{{ $pwaThemeColorDark }}" media="(prefers-color-scheme: dark)">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ $pwaShortName }}">
    <link rel="apple-touch-icon" href="{{ $pwaIconUrl }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulseSubtle {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.85; transform: scale(0.98); }
        }

        .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
        .animate-slide-up { animation: slideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

        /* Glassmorphism utility */
        .glass-card {
            background-color: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        .dark .glass-card {
            background-color: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        
        /* Modern Scrollbars */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8;
        }

        /* Dark mode scrollbars */
        .dark ::-webkit-scrollbar-thumb {
            background-color: #334155;
        }
        .dark ::-webkit-scrollbar-thumb:hover {
            background-color: #475569;
        }

        /* Firefox Support */
        * {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }
        .dark * {
            scrollbar-color: #334155 transparent;
        }
    </style>
    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100 transition-colors duration-300 relative selection:bg-blue-500 selection:text-white">
    <!-- Ambient 2026 Background Orbs -->
    <div class="fixed top-0 start-1/4 w-[500px] h-[500px] bg-blue-500/10 dark:bg-blue-600/10 rounded-full blur-[120px] pointer-events-none z-0"></div>
    <div class="fixed bottom-0 end-1/4 w-[450px] h-[450px] bg-indigo-500/10 dark:bg-purple-600/10 rounded-full blur-[100px] pointer-events-none z-0"></div>

    <div x-data="{ sidebar: window.innerWidth >= 1024 }" class="flex h-screen overflow-hidden relative z-10">
        @auth
            <!-- Sidebar -->
            <aside class="fixed inset-y-0 start-0 z-50 w-72 shrink-0 bg-white dark:bg-slate-900 border-e border-slate-200 dark:border-slate-800 transform transition-all duration-300 ease-in-out lg:relative lg:!translate-x-0 flex flex-col shadow-sm lg:shadow-none overflow-hidden"
                   :class="sidebar ? 'translate-x-0' : 'ltr:-translate-x-full rtl:translate-x-full'">
                
                <div class="flex items-center justify-between h-20 px-6 pt-[env(safe-area-inset-top)] bg-blue-700 border-b border-blue-800/10 dark:border-slate-700/20">
                    @php $appLogo = \App\Domains\Settings\Models\Setting::get('app_logo'); @endphp
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        @if($appLogo)
                            <img src="{{ asset('storage/' . $appLogo) }}" alt="{{ config('app.name') }}" loading="lazy" class="h-10 max-w-[160px] object-contain drop-shadow-md dark:brightness-0 dark:invert">
                        @else
                            <span class="text-2xl font-black text-white tracking-wider font-heading drop-shadow-md">{{ config('app.name', 'Chronorex') }}</span>
                        @endif
                    </a>
                    <div class="flex items-center gap-1">
                        <button @click="sidebar = false" class="lg:hidden text-white/80 hover:text-white p-1.5 rounded-lg hover:bg-white/10 transition-colors focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto px-4 pt-6 pb-[env(safe-area-inset-bottom)] space-y-8">
                    <!-- Main Menu -->
                    <div>
                        <p class="px-4 text-xs font-semibold text-slate-400 dark:text-slate-500 mb-3">{{ __('nav.main_menu') }}</p>
                        <ul class="space-y-1">
                            @if(auth()->user()->hasPermission('dashboard'))
                            <li>
                                <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 rounded-xl transition-colors duration-200 group {{ request()->routeIs('dashboard') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white' }}">
                                    <div class="{{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-slate-200 dark:group-hover:bg-slate-700' }} p-2.5 rounded-xl transition-colors me-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                    </div>
                                    <span class="text-sm">{{ __('nav.dashboard') }}</span>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->hasPermission('expenses'))
                            <li>
                                <a href="{{ route('expenses.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-colors duration-200 group {{ request()->routeIs('expenses.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white' }}">
                                    <div class="{{ request()->routeIs('expenses.*') ? 'bg-blue-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-slate-200 dark:group-hover:bg-slate-700' }} p-2.5 rounded-xl transition-colors me-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <span class="text-sm">{{ __('nav.expenses') }}</span>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->hasPermission('treasury'))
                            <li>
                                <a href="{{ route('treasury.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-colors duration-200 group {{ request()->routeIs('treasury.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white' }}">
                                    <div class="{{ request()->routeIs('treasury.*') ? 'bg-blue-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-slate-200 dark:group-hover:bg-slate-700' }} p-2 rounded-lg transition-colors me-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    </div>
                                    <span class="text-sm">{{ __('nav.treasury') }}</span>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->hasPermission('employees'))
                            <li>
                                <a href="{{ route('employees.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-colors duration-200 group {{ request()->routeIs('employees.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white' }}">
                                    <div class="{{ request()->routeIs('employees.*') ? 'bg-blue-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-slate-200 dark:group-hover:bg-slate-700' }} p-2 rounded-lg transition-colors me-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </div>
                                    <span class="text-sm">{{ __('nav.employees') ?? 'Employees' }}</span>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->hasPermission('reports'))
                            <li>
                                <a href="{{ route('reports.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-colors duration-200 group {{ request()->routeIs('reports.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white' }}">
                                    <div class="{{ request()->routeIs('reports.*') ? 'bg-blue-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-slate-200 dark:group-hover:bg-slate-700' }} p-2 rounded-lg transition-colors me-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <span class="text-sm">{{ __('nav.reports') }}</span>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->hasPermission('statistics'))
                            <li>
                                <a href="{{ route('statistics.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-colors duration-200 group {{ request()->routeIs('statistics.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white' }}">
                                    <div class="{{ request()->routeIs('statistics.*') ? 'bg-blue-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-slate-200 dark:group-hover:bg-slate-700' }} p-2 rounded-lg transition-colors me-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <span class="text-sm">{{ __('nav.statistics') }}</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>

                    @php $hasAdminAccess = auth()->user()->hasPermission('settings') || auth()->user()->hasPermission('categories') || auth()->user()->hasPermission('users') || auth()->user()->hasPermission('roles') || auth()->user()->hasPermission('audit-logs') || auth()->user()->hasPermission('email-templates'); @endphp
                    @if($hasAdminAccess)
                    <!-- Administration -->
                    <div>
                        <p class="px-4 text-xs font-semibold text-slate-400 dark:text-slate-500 mb-3">{{ __('nav.administration') }}</p>
                        <ul class="space-y-1">
                            @if(auth()->user()->hasPermission('settings'))
                            <li>
                                <a href="{{ route('settings.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-colors duration-200 group {{ request()->routeIs('settings.index') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white' }}">
                                    <div class="{{ request()->routeIs('settings.index') ? 'bg-blue-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-slate-200 dark:group-hover:bg-slate-700' }} p-2 rounded-lg transition-colors me-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <span class="text-sm">{{ __('nav.settings') }}</span>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->hasPermission('categories'))
                            <li>
                                <a href="{{ route('settings.categories') }}" class="flex items-center px-4 py-3 rounded-xl transition-colors duration-200 group {{ request()->routeIs('settings.categories') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white' }}">
                                    <div class="{{ request()->routeIs('settings.categories') ? 'bg-blue-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-slate-200 dark:group-hover:bg-slate-700' }} p-2 rounded-lg transition-colors me-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    </div>
                                    <span class="text-sm">{{ __('settings.categories') ?? 'تصنيفات المصاريف' }}</span>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->hasPermission('roles'))
                            <li>
                                <a href="{{ route('settings.roles') }}" class="flex items-center px-4 py-3 rounded-xl transition-colors duration-200 group {{ request()->routeIs('settings.roles') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white' }}">
                                    <div class="{{ request()->routeIs('settings.roles') ? 'bg-blue-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-slate-200 dark:group-hover:bg-slate-700' }} p-2 rounded-lg transition-colors me-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    </div>
                                    <span class="text-sm">{{ __('settings.roles') }}</span>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->hasPermission('users'))
                            <li>
                                <a href="{{ route('settings.users') }}" class="flex items-center px-4 py-3 rounded-xl transition-colors duration-200 group {{ request()->routeIs('settings.users') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white' }}">
                                    <div class="{{ request()->routeIs('settings.users') ? 'bg-blue-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-slate-200 dark:group-hover:bg-slate-700' }} p-2 rounded-lg transition-colors me-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    </div>
                                    <span class="text-sm">{{ __('settings.manage_users') }}</span>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->hasPermission('audit-logs'))
                            <li>
                                <a href="{{ route('settings.audit-logs') }}" class="flex items-center px-4 py-3 rounded-xl transition-colors duration-200 group {{ request()->routeIs('settings.audit-logs') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white' }}">
                                    <div class="{{ request()->routeIs('settings.audit-logs') ? 'bg-blue-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-slate-200 dark:group-hover:bg-slate-700' }} p-2 rounded-lg transition-colors me-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                    </div>
                                    <span class="text-sm">{{ __('settings.audit_logs') }}</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                    @endif
                </div>

                <!-- Dark Mode Toggle -->
                <div class="p-4 border-t border-slate-200 dark:border-slate-700/40">
                    <button @click="dark = !dark" class="w-full flex items-center justify-center px-4 py-3 bg-slate-50 text-slate-600 hover:bg-slate-100 dark:bg-slate-700/40 dark:text-slate-300 dark:hover:bg-slate-700 rounded-xl transition-all shadow-inner border border-slate-200/50 dark:border-slate-700/50 font-semibold text-sm">
                        <template x-if="!dark">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 me-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                                Mode sombre
                            </div>
                        </template>
                        <template x-if="dark">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 me-2 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                Mode clair
                            </div>
                        </template>
                    </button>
                </div>
            </aside>

            <!-- Main Content Wrapper -->
            <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 dark:bg-slate-950 transition-colors duration-300">
                <!-- Top Navbar -->
                <nav class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200/60 dark:border-slate-800/60 shadow-[0_2px_15px_rgb(0,0,0,0.015)] sticky top-0 z-20 transition-colors duration-300">
                    <div class="px-6 h-20 pt-[env(safe-area-inset-top)] flex items-center justify-between">
                        <div class="flex items-center">
                            <button @click="sidebar = true" class="lg:hidden p-2.5 -ms-2 text-slate-500 dark:text-slate-400 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            </button>
                        </div>
                        
                        <div class="flex items-center gap-4">
                            <!-- Theme Toggle 2026 -->
                            <button @click="dark = !dark" class="relative p-2.5 text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 bg-slate-100 dark:bg-slate-800/80 rounded-2xl transition-all duration-300 hover:scale-105 active:scale-95 border border-slate-200/50 dark:border-slate-700/50 cursor-pointer" title="Changer le thème">
                                <template x-if="!dark">
                                    <svg class="w-5 h-5 transform transition-transform duration-500 rotate-0 hover:rotate-90 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                </template>
                                <template x-if="dark">
                                    <svg class="w-5 h-5 transform transition-transform duration-500 -rotate-12 hover:rotate-0 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                                </template>
                            </button>

                            <!-- Language Switcher -->
                            <div class="flex bg-slate-100/80 dark:bg-slate-800/80 backdrop-blur-md rounded-2xl p-1 shadow-inner border border-slate-200/60 dark:border-slate-700/50">
                                <form method="POST" action="{{ route('locale.switch', 'ar') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs font-bold px-3 py-1.5 rounded-xl transition-all duration-300 {{ session('locale', 'ar') === 'ar' ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400 shadow-sm border border-slate-200/50 dark:border-slate-600' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200' }}">AR</button>
                                </form>
                                <form method="POST" action="{{ route('locale.switch', 'fr') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs font-bold px-3 py-1.5 rounded-xl transition-all duration-300 {{ session('locale') === 'fr' ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400 shadow-sm border border-slate-200/50 dark:border-slate-600' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200' }}">FR</button>
                                </form>
                                <form method="POST" action="{{ route('locale.switch', 'en') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs font-bold px-3 py-1.5 rounded-xl transition-all duration-300 {{ session('locale') === 'en' ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400 shadow-sm border border-slate-200/50 dark:border-slate-600' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200' }}">EN</button>
                                </form>
                            </div>

                            <div class="w-px h-8 bg-slate-200/80 dark:bg-slate-800 mx-1"></div>

                            <!-- User Dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center gap-3 p-1.5 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors border border-transparent hover:border-slate-200 dark:hover:border-slate-700">
                                    @if(auth()->user()->photo)
                                        <img src="{{ asset('storage/' . auth()->user()->photo) }}" alt="{{ auth()->user()->name }}" loading="lazy" class="w-10 h-10 rounded-full object-cover border border-slate-200 dark:border-slate-700 shadow-sm">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold text-sm shadow-inner border border-white/20 dark:border-slate-700">
                                            {{ substr(auth()->user()->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div class="hidden md:block text-start me-1">
                                        <p class="text-sm font-bold text-slate-700 dark:text-slate-200 leading-tight">{{ auth()->user()->name }}</p>
                                        <p class="text-[11px] text-slate-400 dark:text-slate-400 mt-0.5">{{ auth()->user()->role->{'label_' . app()->getLocale()} ?? auth()->user()->role->name }}</p>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-400 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                
                                <div x-show="open" @click.outside="open = false" 
                                     x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute {{ session('locale', 'ar') === 'ar' ? 'left-0' : 'right-0' }} mt-3 w-56 bg-white dark:bg-slate-800 rounded-2xl shadow-premium-dark border border-slate-100 dark:border-slate-700 py-2 z-50">
                                    <div class="px-2">
                                        <a href="{{ route('profile') }}" class="w-full text-start px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 rounded-xl transition-colors flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            {{ __('nav.profile', ['default' => 'Mon Profil']) }}
                                        </a>
                                    </div>
                                    <form method="POST" action="{{ route('logout') }}" class="px-2 mt-1 border-t border-slate-100 dark:border-slate-700/60 pt-1">
                                        @csrf
                                        <button type="submit" class="w-full text-start px-4 py-2.5 text-sm font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-xl transition-colors flex items-center">
                                            <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                            {{ __('nav.logout') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>
 
                @if(session()->has('impersonator_id'))
                    <div class="bg-red-600 dark:bg-red-700 px-6 py-4 flex items-center justify-between text-white shadow-[0_4px_20px_rgba(220,38,38,0.5)] sticky top-0 z-30" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            <span class="text-sm font-bold">{{ __('settings.impersonating', ['name' => session('impersonator_name')]) }}</span>
                        </div>
                        <form method="POST" action="{{ route('leave-impersonation') }}">
                            @csrf
                            <button type="submit" class="text-xs font-bold px-4 py-2 bg-white/20 hover:bg-white/30 rounded-xl transition-colors cursor-pointer text-white">
                                {{ __('settings.leave_impersonation') }}
                            </button>
                        </form>
                    </div>
                @endif
                <main class="flex-1 overflow-y-auto p-4 md:p-8 lg:p-10 pb-[env(safe-area-inset-bottom)]">
                    <div class="max-w-7xl mx-auto">
                        {{ $slot ?? '' }}
                        @yield('content')
                    </div>
                </main>
            </div>         </div>

            <!-- Overlay for mobile sidebar -->
            <div x-show="sidebar" @click="sidebar = false" 
                 x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-20 lg:hidden" style="display: none;"></div>
        @else
            <main class="min-h-screen w-full flex items-center justify-center p-4">
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        @endauth
    </div>
    @livewireScripts
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('click', function (e) {
            let el = e.target.closest('[wire\\:confirm]');
            if (el && !el.hasAttribute('data-swal-confirmed')) {
                // Intercept the click before Livewire can trigger the native confirm
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();

                let message = el.getAttribute('wire:confirm');

                Swal.fire({
                    title: '{{ __("common.confirm") }}',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '{{ __("common.confirm") }}',
                    cancelButtonText: '{{ __("common.cancel") }}',
                    background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff',
                    color: document.documentElement.classList.contains('dark') ? '#f8fafc' : '#0f172a',
                    customClass: {
                        popup: 'rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-700',
                        confirmButton: 'rounded-xl px-6 py-2.5 font-bold',
                        cancelButton: 'rounded-xl px-6 py-2.5 font-bold'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        el.setAttribute('data-swal-confirmed', 'true');
                        
                        // Temporarily bypass the native window.confirm that Livewire uses
                        let originalConfirm = window.confirm;
                        window.confirm = function() { return true; };
                        
                        // Trigger the action
                        el.click();
                        
                        // Restore attributes and native confirm
                        window.confirm = originalConfirm;
                        el.removeAttribute('data-swal-confirmed');
                    }
                });
            }
        }, true); // true = Capture phase (crucial to run before Livewire)
    </script>
    <script>
        // Service Worker registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js');
            });
        }

        // PWA Install Prompt
        (function() {
            let deferredPrompt = null;
            const DISMISSED_KEY = 'pwa_install_dismissed';

            // Already installed (standalone mode) → do nothing
            if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) {
                return;
            }

            // Dismissed previously → don't bother again
            if (localStorage.getItem(DISMISSED_KEY) || localStorage.getItem('pwa_installed')) {
                return;
            }

            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;

                setTimeout(() => {
                    if (!deferredPrompt) return;
                    const isDark = document.documentElement.classList.contains('dark');
                    Swal.fire({
                        title: '📲 ' + '{{ __("pwa.install_title", ["default" => "Installer l\'application"]) }}',
                        html: '{{ __("pwa.install_desc", ["default" => "Ajoutez Chronorex à votre écran d\'accueil pour un accès rapide, même hors-ligne."]) }}',
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#2563eb',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: '{{ __("pwa.install_btn", ["default" => "Installer"]) }}',
                        cancelButtonText: '{{ __("pwa.not_now", ["default" => "Plus tard"]) }}',
                        showDenyButton: true,
                        denyButtonText: '{{ __("pwa.never", ["default" => "Ne plus demander"]) }}',
                        denyButtonColor: '#e2e8f0',
                        background: isDark ? '#1e293b' : '#ffffff',
                        color: isDark ? '#f8fafc' : '#0f172a',
                        customClass: {
                            popup: 'rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-700',
                            confirmButton: 'rounded-xl px-6 py-2.5 font-bold',
                            cancelButton: 'rounded-xl px-6 py-2.5 font-bold text-slate-500',
                            denyButton: 'rounded-xl px-6 py-2.5 font-bold text-xs bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            deferredPrompt.prompt();
                            deferredPrompt.userChoice.then((choice) => {
                                if (choice.outcome === 'accepted') {
                                    localStorage.setItem('pwa_installed', '1');
                                }
                                deferredPrompt = null;
                            });
                        } else if (result.isDenied) {
                            localStorage.setItem(DISMISSED_KEY, '1');
                            deferredPrompt = null;
                        } else {
                            deferredPrompt = null;
                        }
                    });
                }, 3000);
            });
        })();
    </script>

    {{-- Login Popup --}}
    @if(session('login_popup') && \App\Domains\Settings\Models\Setting::get('login_popup_enabled', false))
    @php $popupContent = \App\Domains\Settings\Models\Setting::get('login_popup_content', ''); @endphp
    @if($popupContent)
    <div x-data="{ show: true }" x-show="show" x-cloak class="fixed inset-0 z-[200] flex items-center justify-center p-4">
        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="show = false"></div>
        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95" class="relative bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl rounded-3xl shadow-2xl w-full max-w-lg border border-slate-200/50 dark:border-slate-800/60 overflow-hidden p-6 max-h-[80vh] overflow-y-auto">
            <button @click="show = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <div class="prose dark:prose-invert max-w-none">{!! nl2br(e($popupContent)) !!}</div>
        </div>
    </div>
    @endif
    @endif
</body>
</html>

