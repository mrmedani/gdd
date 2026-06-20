<?php

namespace App\Domains\Employees\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'month',
        'year',
        'base_amount',
        'advances_deducted',
        'net_amount',
        'payment_method',
        'transaction_reference',
        'paid_at',
        'created_by',
        'expense_id',
    ];

    protected $casts = [
        'base_amount' => 'decimal:2',
        'advances_deducted' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'paid_at' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
