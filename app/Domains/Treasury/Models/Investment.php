<?php

namespace App\Domains\Treasury\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Investment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'date',
        'amount',
        'type',
        'beneficiary',
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function typeLabel(string $type): string
    {
        return match ($type) {
            'equipment' => __('investments.type_equipment'),
            'project' => __('investments.type_project'),
            default => __('investments.type_other'),
        };
    }
}
