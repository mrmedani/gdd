<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected static function newFactory(): \Illuminate\Database\Eloquent\Factories\Factory
    {
        return \Database\Factories\UserFactory::new();
    }

    protected $fillable = [
        'name',
        'email',
        'photo',
        'password',
        'role_id',
        'locale',
        'alert_preferences',
        'whatsapp_phone',
        'notify_whatsapp',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $with = ['role'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'alert_preferences' => 'array',
            'notify_whatsapp' => 'boolean',
        ];
    }

    public function wantsAlertType(string $type): bool
    {
        $prefs = $this->alert_preferences ?? [];
        if (empty($prefs)) return true;
        return in_array($type, $prefs, true);
    }

    public function role(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function isAdmin(): bool
    {
        return $this->role?->name === 'admin';
    }

    public function isAccountant(): bool
    {
        return $this->role?->name === 'accountant';
    }

    public function hasPermission(string $permission): bool
    {
        return $this->role?->hasPermission($permission) ?? false;
    }

    public function expenses()
    {
        return $this->hasMany(\App\Domains\Expenses\Models\Expense::class, 'created_by');
    }
}
