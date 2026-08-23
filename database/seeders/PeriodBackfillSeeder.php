<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PeriodBackfillSeeder extends Seeder
{
    public function run(): void
    {
        $admin = DB::table('users')->where('email', 'admin@chronorex.ma')->first();
        $adminId = $admin?->id ?? 1;
        $cats = DB::table('expense_categories')->get();
        $catByKey = [];
        foreach ($cats as $c) {
            $key = $c->key ?: \Str::slug($c->name_fr);
            $catByKey[$key] = $c;
        }
        $methods = ['cash', 'bank_transfer', 'check', 'credit_card'];

        // ---------------------------------------------------------------
        // Current accounting period: 21 Aug 2026 -> 20 Sep 2026
        // Backfill so the dashboard is not empty on first open.
        // ---------------------------------------------------------------
        $start = Carbon::parse('2026-08-21');
        $end   = Carbon::parse('2026-09-20');
        $existing = DB::table('expenses')
            ->whereBetween('date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->count();

        if ($existing === 0) {
            $created = 0;
            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                // ~1-2 expenses per day
                $perDay = rand(1, 2);
                for ($i = 0; $i < $perDay; $i++) {
                    $catKey = array_rand($catByKey);
                    $cat = $catByKey[$catKey];
                    $amount = match ($catKey) {
                        'rent'                => 8000.00,
                        'salaries'            => round(rand(4000, 9000) + rand(0, 99) / 100, 2),
                        'fuel', 'vehicle_maintenance' => round(rand(200, 1800) + rand(0, 99) / 100, 2),
                        'internet'            => 499.00,
                        'electricity'         => round(rand(700, 1600) + rand(0, 99) / 100, 2),
                        'advertising'         => round(rand(500, 5000) + rand(0, 99) / 100, 2),
                        'supplies'            => round(rand(150, 2500) + rand(0, 99) / 100, 2),
                        default               => round(rand(100, 4000) + rand(0, 99) / 100, 2),
                    };
                    $desc = match ($catKey) {
                        'rent'                => 'Loyer local bureaux',
                        'salaries'            => 'Masse salariale',
                        'fuel'                => 'Carburant véhicule ' . ['A-101', 'B-205', 'C-318', 'D-442'][rand(0, 3)],
                        'vehicle_maintenance' => 'Entretien véhicule ' . ['A-101', 'B-205', 'C-318', 'D-442'][rand(0, 3)],
                        'internet'            => 'Abonnement Internet fibre',
                        'electricity'         => 'Facture électricité',
                        'advertising'         => 'Publicité réseaux sociaux',
                        'supplies'            => 'Fournitures de bureau',
                        default               => 'Frais divers',
                    };
                    DB::table('expenses')->insert([
                        'date'           => $d->format('Y-m-d'),
                        'amount'         => $amount,
                        'category_id'    => $cat->id,
                        'category_key'   => $catKey,
                        'description'    => $desc,
                        'payment_method' => $methods[rand(0, 3)],
                        'receipt_path'   => null,
                        'notes'          => null,
                        'created_by'     => $adminId,
                        'employee_id'    => null,
                        'created_at'     => $d,
                        'updated_at'     => $d,
                    ]);
                    $created++;
                }
            }
            $this->command->info("  + {$created} dépenses créées pour la période courante (21/08 -> 20/09)");
        } else {
            $this->command->info("  (période courante déjà peuplée - ignoré)");
        }

        // ---------------------------------------------------------------
        // Rebuild monthly closures for May, Jun, Jul, Aug 2026
        // ---------------------------------------------------------------
        foreach (['2026-05', '2026-06', '2026-07', '2026-08'] as $ym) {
            $exp = DB::table('expenses')
                ->whereBetween('date', [$ym . '-01', $ym . '-31'])
                ->sum('amount');
            $gains = round($exp * (1 + rand(20, 60) / 100), 2);
            $balance = round($gains - $exp, 2);
            DB::table('monthly_closures')->updateOrInsert(
                ['month' => $ym],
                [
                    'gains'     => $gains,
                    'expenses'  => $exp,
                    'balance'   => $balance,
                    'closed_by' => $adminId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
        $this->command->info('  + clôtures mensuelles (mai-août 2026) mises à jour');

        $this->command->info('✅ Période courante remplie.');
    }
}
