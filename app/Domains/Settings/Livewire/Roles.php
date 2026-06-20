<?php

namespace App\Domains\Settings\Livewire;

use App\Models\Role;
use App\Shared\Livewire\WithToast;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Roles extends Component
{
    use WithPagination, WithToast;

    public const PERMISSIONS = [
        'dashboard',
        'expenses',
        'treasury',
        'employees',
        'reports',
        'statistics',
        'settings',
        'categories',
        'users',
        'roles',
        'audit-logs',
        'view-deficit',
        'delete-closure',
    ];

    public ?int $roleId = null;
    public string $name = '';
    public string $label_ar = '';
    public string $label_fr = '';

    public bool $perm_dashboard = false;
    public bool $perm_expenses = false;
    public bool $perm_treasury = false;
    public bool $perm_employees = false;
    public bool $perm_reports = false;
    public bool $perm_statistics = false;
    public bool $perm_settings = false;
    public bool $perm_categories = false;
    public bool $perm_users = false;
    public bool $perm_roles = false;
    public bool $perm_audit_logs = false;
    public bool $perm_view_deficit = false;
    public bool $perm_delete_closure = false;

    public bool $showForm = false;

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:roles,name,' . $this->roleId,
            'label_ar' => 'required|string|max:255',
            'label_fr' => 'nullable|string|max:255',
        ];
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id)
    {
        $role = Role::findOrFail($id);
        $this->roleId = $role->id;
        $this->name = $role->name;
        $this->label_ar = $role->label_ar;
        $this->label_fr = $role->label_fr ?? '';
        $this->loadPermissions($role->permissions ?? []);
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'label_ar' => $this->label_ar,
            'label_fr' => $this->label_fr,
            'permissions' => $this->gatherPermissions(),
        ];

        if ($this->roleId) {
            Role::findOrFail($this->roleId)->update($data);
            $this->notify(__('common.updated'));
        } else {
            Role::create($data);
            $this->notify(__('common.created'));
        }

        $this->resetForm();
    }

    public function delete(int $id)
    {
        $role = Role::withCount('users')->findOrFail($id);

        if ($role->users_count > 0) {
            return;
        }

        $role->delete();
        $this->notify(__('common.deleted'));
    }

    public function togglePermission(string $key)
    {
        $prop = 'perm_' . str_replace('-', '_', $key);
        $this->$prop = !$this->$prop;
    }

    public function resetForm()
    {
        $this->reset(['roleId', 'name', 'label_ar', 'label_fr']);
        foreach (self::PERMISSIONS as $perm) {
            $prop = 'perm_' . str_replace('-', '_', $perm);
            $this->$prop = false;
        }
        $this->perm_delete_closure = false;
        $this->showForm = false;
        $this->resetValidation();
    }

    private function loadPermissions(array $permissions): void
    {
        foreach (self::PERMISSIONS as $perm) {
            $prop = 'perm_' . str_replace('-', '_', $perm);
            $this->$prop = isset($permissions[$perm]) && $permissions[$perm] === true;
        }
    }

    private function gatherPermissions(): array
    {
        $permissions = [];
        foreach (self::PERMISSIONS as $perm) {
            $prop = 'perm_' . str_replace('-', '_', $perm);
            $permissions[$perm] = $this->$prop;
        }
        return $permissions;
    }

    public function mount(): void
    {
        Gate::authorize('manage-roles');
    }

    public function render()
    {
        $permValues = [];
        foreach (self::PERMISSIONS as $perm) {
            $prop = 'perm_' . str_replace('-', '_', $perm);
            $permValues[$perm] = $this->$prop;
        }

        return view('livewire.roles', [
            'roles' => Role::withCount('users')->paginate(10),
            'permValues' => $permValues,
        ])->layout('layouts.app')->title(__('settings.manage_roles'));
    }
}
