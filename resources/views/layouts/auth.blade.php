<!DOCTYPE html>
<html dir="{{ session('locale', 'ar') === 'ar' ? 'rtl' : 'ltr' }}" lang="{{ session('locale', 'ar') }}" x-data="{ dark: localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches) || (!('theme' in localStorage) && !window.matchMedia('(prefers-color-scheme: dark)').matches && '{{ \App\Domains\Settings\Models\Setting::get('default_theme', 'system') }}' === 'dark') }" x-init="$watch('dark', val => localStorage.theme = val ? 'dark' : 'light')" :class="{ 'dark': dark }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) document.documentElement.classList.add('dark')</script>
    <title>@yield('title', config('app.name'))</title>
    @php $appFavicon = \App\Domains\Settings\Models\Setting::get('app_favicon'); @endphp
    @if($appFavicon)
        <link rel="icon" href="{{ asset('storage/' . $appFavicon) }}" type="image/png">
    @endif
    <link rel="manifest" href="{{ route('manifest.json') }}">
    <meta name="theme-color" content="#2563eb">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
        .animate-slide-up { animation: slideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    </style>
    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 transition-colors duration-300 min-h-screen flex items-center justify-center p-4">
    {{ $slot ?? '' }}
    @yield('content')
    @livewireScripts
    @stack('scripts')
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js');
            });
        }
    </script>
</body>
</html>
