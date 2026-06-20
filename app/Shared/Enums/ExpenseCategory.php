<?php

namespace App\Shared\Enums;

enum ExpenseCategory: string
{
    case Salaries = 'salaries';
    case Fuel = 'fuel';
    case Rent = 'rent';
    case Internet = 'internet';
    case Electricity = 'electricity';
    case VehicleMaintenance = 'vehicle_maintenance';
    case Supplies = 'supplies';
    case Advertising = 'advertising';
    case Other = 'other';

    public function label(): string
    {
        return __("categories.{$this->value}");
    }

    public static function options(): array
    {
        return collect(self::cases())->map(fn($c) => [
            'value' => $c->value,
            'label' => $c->label(),
        ])->toArray();
    }
}
