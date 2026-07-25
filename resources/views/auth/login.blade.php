@extends('layouts.auth')

@section('title', __('auth.login_title'))

@push('styles')
<style>
    .login-card { animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes fadeIn { from { opacity: 0; transform: scale(0.95) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear { display: none; }
</style>
@endpush

@section('content')
<div class="w-full max-w-md mx-auto login-card animate-slide-up">
    <!-- Gradient background blur decorative blobs -->
    <div class="absolute -top-10 -left-10 w-40 h-40 bg-blue-500/10 dark:bg-blue-500/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-purple-500/10 dark:bg-purple-500/5 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative bg-white/85 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark p-8 sm:p-10 border border-slate-200/50 dark:border-slate-800/60 transition-all duration-300">
        <div class="text-center mb-10">
            @php $logoUrl = storage_url(\App\Domains\Settings\Models\Setting::get('app_logo')); @endphp
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ config('app.name') }}" class="h-16 mx-auto mb-4 object-contain drop-shadow-md">
            @else
                <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-300 tracking-tight font-heading">{{ config('app.name') }}</h1>
            @endif
            <p class="text-slate-500 dark:text-slate-400 mt-3 font-semibold text-sm">{{ __('auth.login_subtitle') }}</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('auth.email') }}</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-5 py-3.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:focus:ring-blue-500/30 dark:focus:border-blue-400 dark:text-white outline-none transition-all shadow-inner dark:shadow-none font-medium">
                @error('email')
                    <p class="text-red-500 dark:text-red-400 text-sm mt-2 font-medium">{{ $message }}</p>
                @enderror
            </div>
            <div x-data="{ showPassword: false }">
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('auth.password') }}</label>
                <div class="relative">
                    <input type="password" name="password" required autocomplete="current-password"
                        x-ref="passwordInput"
                        class="w-full px-5 py-3.5 pe-12 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:focus:ring-blue-500/30 dark:focus:border-blue-400 dark:text-white outline-none transition-all shadow-inner dark:shadow-none font-medium">
                    <button type="button" @click="showPassword = !showPassword; $refs.passwordInput.type = showPassword ? 'text' : 'password'" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors p-1 z-10">
                        <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-3 text-sm font-semibold text-slate-600 dark:text-slate-400 cursor-pointer select-none">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded text-blue-600 border-slate-300 dark:border-slate-700 dark:bg-slate-950 focus:ring-blue-500/20 cursor-pointer">
                    {{ __('auth.remember') }}
                </label>
                <a href="{{ route('password.forgot') }}" class="text-sm font-bold text-blue-600 dark:text-blue-400 hover:underline">
                    {{ __('auth.forgot_password') }}
                </a>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 text-white py-3.5 rounded-xl font-bold transition-all shadow-lg shadow-blue-600/20 hover:shadow-blue-600/40 hover:-translate-y-0.5 duration-200">
                {{ __('auth.login_button') }}
            </button>
        </form>

        <div class="mt-10 flex justify-center gap-2 border-t border-slate-100 dark:border-slate-800/80 pt-6">
            @foreach(['ar' => 'العربية', 'fr' => 'Français', 'en' => 'English'] as $code => $label)
            <form method="POST" action="{{ route('locale.switch', $code) }}" class="inline">
                @csrf
                <button type="submit" class="text-xs font-bold px-4 py-2 rounded-lg transition-colors cursor-pointer {{ session('locale') === $code ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400' : 'text-slate-400 hover:bg-slate-50 dark:text-slate-500 dark:hover:bg-slate-800' }}">{{ $label }}</button>
            </form>
            @endforeach
        </div>
    </div>
</div>
@endsection

