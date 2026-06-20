<?php

namespace App\Domains\Auth\Livewire;

use App\Domains\Auth\Notifications\ResetPasswordNotification;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Livewire\Component;

class ForgotPassword extends Component
{
    public string $email = '';

    public function sendResetLink(): void
    {
        $this->validate(['email' => 'required|email']);

        $key = request()->ip() . '|' . $this->email;
        $executed = \Illuminate\Support\Facades\RateLimiter::attempt(
            'forgot-password:' . $key,
            3,
            function () {},
            60
        );

        if (!$executed) {
            $this->addError('email', __('auth.throttled'));
            return;
        }

        $user = User::where('email', $this->email)->first();
        if ($user) {
            $token = Password::createToken($user);
            $user->notify(new ResetPasswordNotification($token));
        }

        session()->flash('status', __('auth.reset_link_sent'));
    }

    public function render()
    {
        return view('livewire.forgot-password')->layout('layouts.auth');
    }
}
