@extends('layouts.auth')

@section('title', __('auth.reset_password'))

@push('styles')
<style>
    .auth-card { animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1); }
</style>
@endpush

@section('content')
<div class="w-full max-w-md mx-auto auth-card">
    <div class="absolute -top-10 -left-10 w-40 h-40 bg-blue-500/10 dark:bg-blue-500/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-purple-500/10 dark:bg-purple-500/5 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative bg-white/85 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark p-8 sm:p-10 border border-slate-200/50 dark:border-slate-800/60">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-300 font-heading">{{ __('auth.reset_password') }}</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-3 font-semibold text-sm">{{ __('auth.reset_password_desc') }}</p>
        </div>

        @if (session('status'))
            <div class="p-4 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-xl text-sm font-semibold mb-6">
                {{ session('status') }}
            </div>
        @endif

        <form wire:submit="sendResetLink" class="space-y-6">
            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('auth.email') }}</label>
                <input type="email" wire:model="email" required autofocus
                    class="w-full px-5 py-3.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:focus:ring-blue-500/30 dark:focus:border-blue-400 dark:text-white outline-none transition-all shadow-inner dark:shadow-none font-medium">
                @error('email') <p class="text-red-500 dark:text-red-400 text-sm mt-2 font-medium">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3.5 rounded-xl font-bold transition-all shadow-lg shadow-blue-600/20 hover:shadow-blue-600/40 hover:-translate-y-0.5 duration-200">
                {{ __('auth.send_reset_link') }}
            </button>
        </form>

        <div class="mt-8 text-center">
            <a href="{{ route('login') }}" class="text-sm font-bold text-blue-600 dark:text-blue-400 hover:underline">
                {{ __('auth.back_to_login') }}
            </a>
        </div>
    </div>
</div>
@endsection
