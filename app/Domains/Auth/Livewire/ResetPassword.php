<?php

namespace App\Domains\Auth\Livewire;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Component;

class ResetPassword extends Component
{
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    protected $queryString = ['token'];

    public function mount(): void
    {
        $this->email = request()->query('email', '');
    }

    public function resetPassword(): void
    {
        $this->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|same:password_confirmation',
        ]);

        $status = Password::reset(
            $this->only(['email', 'password', 'password_confirmation', 'token']),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            session()->flash('status', __('auth.password_reset'));
            $this->redirect(route('login'));
        } else {
            $this->addError('email', __($status));
        }
    }

    public function render()
    {
        return view('livewire.reset-password')->layout('layouts.auth');
    }
}
