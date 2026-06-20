<?php

namespace App\Domains\Settings\Livewire;

use App\Models\Role;
use App\Models\User;
use App\Shared\Livewire\WithToast;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination, WithToast;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $passwordConfirmation = '';
    public string $role = 'accountant';
    public string $locale = 'ar';
    public string $search = '';
    public ?int $editingUserId = null;

    protected function rules(): array
    {
        $roleNames = Role::pluck('name')->implode(',');
        $rules = [
            'name' => 'required|string|max:255',
            'role' => 'required|in:' . $roleNames,
            'locale' => 'required|in:ar,fr,en',
        ];

        if ($this->editingUserId) {
            $rules['email'] = 'required|email|unique:users,email,' . $this->editingUserId;
        } else {
            $rules['email'] = 'required|email|unique:users,email';
            $rules['password'] = 'required|min:8|same:passwordConfirmation';
        }

        return $rules;
    }

    public function mount(): void
    {
        Gate::authorize('manage-users');
    }

    public function saveUser(): void
    {
        Gate::authorize('manage-users');
        $this->validate();

        $roleModel = Role::where('name', $this->role)->first();

        if ($this->editingUserId) {
            $user = User::findOrFail($this->editingUserId);
            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'role_id' => $roleModel?->id,
                'locale' => $this->locale,
            ];
            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }
            $user->update($data);
            $this->notify(__('common.updated'));
        } else {
            User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role_id' => $roleModel?->id,
                'locale' => $this->locale,
            ]);
            $this->notify(__('common.created'));
        }

        $this->resetForm();
    }

    public function editUser(int $id): void
    {
        $user = User::findOrFail($id);

        if ($user->isAdmin() && $user->id !== auth()->id()) {
            return;
        }

        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role?->name ?? 'accountant';
        $this->locale = $user->locale;
        $this->password = '';
        $this->passwordConfirmation = '';
        $this->dispatch('scroll-to-form');
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
    }

    public function loginAsUser(int $id): void
    {
        Gate::authorize('login-as');

        if ($id === auth()->id()) {
            return;
        }

        $targetUser = User::findOrFail($id);

        session()->put('impersonator_id', auth()->id());
        session()->put('impersonator_name', auth()->user()->name);

        Auth::login($targetUser);
        session()->regenerate();

        $this->redirect(route('dashboard'));
    }

    public function deleteUser(int $id): void
    {
        if ($id === auth()->id()) {
            return;
        }

        User::findOrFail($id)->delete();
        $this->notify(__('common.deleted'));
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    private function resetForm(): void
    {
        $this->reset(['name', 'email', 'password', 'passwordConfirmation', 'editingUserId']);
        $this->role = Role::first()?->name ?? 'accountant';
        $this->locale = 'ar';
    }

    public function render()
    {
        $query = User::with('role')->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $users = $query->paginate(10);
        $roles = Role::orderBy('name')->get();

        return view('livewire.users', compact('users', 'roles'))
            ->layout('layouts.app')
            ->title(__('settings.manage_users'));
    }
}
