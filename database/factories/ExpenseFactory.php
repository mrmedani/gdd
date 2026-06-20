<?php

namespace Database\Factories;

use App\Domains\Expenses\Models\Expense;
use App\Domains\Expenses\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        $keys = ['salaries', 'fuel', 'rent', 'internet', 'electricity', 'vehicle_maintenance', 'supplies', 'advertising', 'other'];

        return [
            'date' => fake()->date(),
            'amount' => fake()->randomFloat(2, 100, 10000),
            'category_id' => ExpenseCategory::factory(),
            'category_key' => fake()->randomElement($keys),
            'description' => fake()->sentence(),
            'payment_method' => fake()->randomElement(['cash', 'bank_transfer', 'check', 'credit_card']),
            'created_by' => User::factory(),
        ];
    }
}
