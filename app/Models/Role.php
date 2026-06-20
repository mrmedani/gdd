<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'label_ar', 'label_fr', 'permissions'];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->permissions !== null) {
            return isset($this->permissions[$permission]) && $this->permissions[$permission] === true;
        }

        if ($this->name === 'admin') {
            return true;
        }

        $accountantPermissions = ['dashboard', 'expenses', 'treasury', 'reports'];
        if ($this->name === 'accountant') {
            return in_array($permission, $accountantPermissions, true);
        }

        return false;
    }
}
