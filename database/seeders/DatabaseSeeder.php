<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Domains\Expenses\Models\Expense;
use App\Domains\Expenses\Models\ExpenseCategory;
use App\Domains\Settings\Models\Setting;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['label_ar' => 'مدير النظام', 'label_fr' => 'Administrateur']);
        $accountantRole = Role::firstOrCreate(['name' => 'accountant'], ['label_ar' => 'محاسب', 'label_fr' => 'Comptable']);

        $adminPassword = env('ADMIN_PASSWORD', 'admin123');
        $accountantPassword = env('ACCOUNTANT_PASSWORD', 'accountant123');

        User::firstOrCreate(
            ['email' => 'admin@chronorex.ma'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make($adminPassword),
                'role_id' => $adminRole->id,
                'locale' => 'ar',
            ]
        );

        User::firstOrCreate(
            ['email' => 'accountant@chronorex.ma'],
            [
                'name' => 'محاسب',
                'password' => Hash::make($accountantPassword),
                'role_id' => $accountantRole->id,
                'locale' => 'fr',
            ]
        );

        if (!app()->environment('testing')) {
            $this->command->warn('*** WARNING: Change default passwords in production! Set ADMIN_PASSWORD and ACCOUNTANT_PASSWORD in .env ***');
        }

        $categories = [
            ['key' => 'salaries', 'name_ar' => 'الرواتب', 'name_fr' => 'Salaires', 'name_en' => 'Salaries'],
            ['key' => 'fuel', 'name_ar' => 'الوقود', 'name_fr' => 'Carburant', 'name_en' => 'Fuel'],
            ['key' => 'rent', 'name_ar' => 'الإيجار', 'name_fr' => 'Loyer', 'name_en' => 'Rent'],
            ['key' => 'internet', 'name_ar' => 'الإنترنت', 'name_fr' => 'Internet', 'name_en' => 'Internet'],
            ['key' => 'electricity', 'name_ar' => 'الكهرباء', 'name_fr' => 'Électricité', 'name_en' => 'Electricity'],
            ['key' => 'vehicle_maintenance', 'name_ar' => 'صيانة المركبات', 'name_fr' => 'Entretien véhicules', 'name_en' => 'Vehicle Maintenance'],
            ['key' => 'supplies', 'name_ar' => 'اللوازم والمشتريات', 'name_fr' => 'Fournitures et achats', 'name_en' => 'Supplies & Purchases'],
            ['key' => 'advertising', 'name_ar' => 'الإعلانات', 'name_fr' => 'Publicité', 'name_en' => 'Advertising'],
            ['key' => 'other', 'name_ar' => 'مصاريف أخرى', 'name_fr' => 'Autres dépenses', 'name_en' => 'Other Expenses'],
        ];

        $inserted = [];
        foreach ($categories as $cat) {
            $inserted[$cat['key']] = ExpenseCategory::firstOrCreate(
                ['key' => $cat['key']],
                ['name_ar' => $cat['name_ar'], 'name_fr' => $cat['name_fr'], 'name_en' => $cat['name_en'], 'is_active' => true]
            );
        }

        Setting::set('currency', 'MAD');
        Setting::set('threshold', 10000);

        $descriptions = [
            'salaries' => ['رواتب الموظفين', 'Salaires du personnel'],
            'fuel' => ['تعبئة وقود للسيارات', 'Carburant véhicules'],
            'rent' => ['إيجار المقر', 'Loyer local'],
            'internet' => ['اشتراك الإنترنت', 'Abonnement internet'],
            'electricity' => ['فاتورة الكهرباء', 'Facture électricité'],
            'vehicle_maintenance' => ['صيانة السيارة', 'Entretien véhicule'],
            'supplies' => ['مشتريات مكتبية', 'Fournitures bureau'],
            'advertising' => ['إعلان على وسائل التواصل', 'Publicité réseaux sociaux'],
            'other' => ['مصاريف متنوعة', 'Dépenses diverses'],
        ];

        $admin = User::first();
        $catKeys = array_keys($inserted);

        $methods = ['cash', 'bank_transfer', 'check', 'credit_card'];
        foreach (range(1, 30) as $i) {
            $key = $catKeys[array_rand($catKeys)];
            $cat = $inserted[$key];
            $desc = $descriptions[$key];
            Expense::create([
                'date' => Carbon::now()->subDays(rand(0, 60))->format('Y-m-d'),
                'amount' => round(rand(100, 15000) + rand(0, 99) / 100, 2),
                'category_id' => $cat->id,
                'category_key' => $key,
                'description' => $desc[array_rand($desc)],
                'payment_method' => $methods[array_rand($methods)],
                'created_by' => $admin->id,
            ]);
        }
    }
}
