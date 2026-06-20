<?php

namespace Database\Factories;

use App\Domains\Expenses\Models\ExpenseCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseCategoryFactory extends Factory
{
    protected $model = ExpenseCategory::class;

    public function definition(): array
    {
        return [
            'name_ar' => 'تصنيف',
            'name_fr' => 'Catégorie',
            'is_active' => true,
        ];
    }
}
