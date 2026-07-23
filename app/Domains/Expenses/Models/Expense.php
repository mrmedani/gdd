<?php

namespace App\Domains\Expenses\Models;

use App\Models\User;
use App\Shared\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected static function newFactory(): \Illuminate\Database\Eloquent\Factories\Factory
    {
        return \Database\Factories\ExpenseFactory::new();
    }

    protected $fillable = [
        'date',
        'amount',
        'category_id',
        'category_key',
        'description',
        'payment_method',
        'receipt_path',
        'notes',
        'employee_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date:Y-m-d',
            'amount' => 'decimal:2',
        ];
    }

    public static function assertPeriodNotClosed(string $date): void
    {
        $period = getPeriodFromDate($date);
        if (\App\Domains\Treasury\Models\MonthlyClosure::where('month', $period)->exists()) {
            throw new \Exception(__('expenses.month_closed', ['default' => 'Ce mois a déjà été clôturé et ne peut plus être modifié.']));
        }
    }

    protected static function booted()
    {
        static::creating(fn ($expense) => static::assertPeriodNotClosed($expense->date));
        static::updating(fn ($expense) => static::assertPeriodNotClosed($expense->date));
        static::deleting(fn ($expense) => static::assertPeriodNotClosed($expense->date));
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeByPeriod($query, string $yearMonth)
    {
        $range = getPeriodRange($yearMonth);
        return $query->whereBetween('date', [$range['start'], $range['end']]);
    }

    public function scopeByMonth($query, int $year, int $month)
    {
        return $query->whereYear('date', $year)->whereMonth('date', $month);
    }

    public function scopeByYear($query, int $year)
    {
        return $query->whereYear('date', $year);
    }

    public function scopeByCategory($query, string $categoryKey)
    {
        return $query->where('category_key', $categoryKey);
    }

    public function scopeAmountBetween($query, float $min, float $max)
    {
        return $query->whereBetween('amount', [$min, $max]);
    }

    public function employee()
    {
        return $this->belongsTo(\App\Domains\Employees\Models\Employee::class, 'employee_id');
    }

}

