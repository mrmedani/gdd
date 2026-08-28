<!DOCTYPE html>
<html dir="{{ session('locale', 'ar') === 'ar' ? 'rtl' : 'ltr' }}" lang="{{ session('locale', 'ar') }}" style="background:transparent;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('ai.title') }}</title>
    @vite('resources/css/app.css')
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) document.documentElement.classList.add('dark');
    </script>
    <style>html,body{background:transparent;margin:0;padding:0;overflow:hidden;}</style>
</head>
<body class="font-sans antialiased">
    @include('livewire.ai.chatbot', ['chatHistory' => $chatHistory ?? [], 'greeting' => $greeting ?? __('ai.greeting'), 'assistantName' => $assistantName ?? __('ai.title')])
</body>
</html>
