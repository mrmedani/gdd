<?php

namespace App\Domains\Treasury\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class MonthlyClosure extends Model
{
    protected $fillable = [
        'month',
        'gains',
        'expenses',
        'balance',
        'closed_by',
    ];

    protected function casts(): array
    {
        return [
            'gains' => 'decimal:2',
            'expenses' => 'decimal:2',
            'balance' => 'decimal:2',
        ];
    }

    public function closer()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
}
