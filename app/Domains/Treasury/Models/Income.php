<?php

namespace App\Domains\Treasury\Models;

use App\Domains\Expenses\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Income extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'date',
        'amount',
        'source_type',
        'sub_type',
        'source_name',
        'category_id',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date:Y-m-d',
            'amount' => 'decimal:2',
        ];
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function sourceTypeLabel(string $type): string
    {
        return match ($type) {
            'investment' => 'Investissement',
            'franchise_fee' => 'Droits de franchise',
            'sale' => 'Vente',
            default => 'Autre',
        };
    }

    public static function subTypeLabel(string $type): string
    {
        return match ($type) {
            'individual' => 'Investisseur (personne physique)',
            'company' => 'Investisseur (personne morale / entreprise)',
            default => $type,
        };
    }

    public static function subTypeOptions(): array
    {
        return [
            'individual' => 'Investisseur (personne physique)',
            'company' => 'Investisseur (personne morale / entreprise)',
        ];
    }
}
