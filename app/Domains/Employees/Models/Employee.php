<?php

namespace App\Domains\Employees\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'role_title',
        'base_salary',
        'hired_at',
        'status',
        'created_by',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'hired_at' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function advances()
    {
        return $this->hasMany(SalaryAdvance::class);
    }

    public function payments()
    {
        return $this->hasMany(SalaryPayment::class);
    }

    public function getActiveAdvancesTotalAttribute()
    {
        return $this->attributes['advances_sum_amount']
            ?? $this->advances()->whereIn('status', ['pending', 'approved'])->sum('amount');
    }
}
