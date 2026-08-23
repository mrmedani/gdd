<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = DB::table('users')->where('email', 'admin@chronorex.ma')->first();
        $accountant = DB::table('users')->where('email', 'accountant@chronorex.ma')->first();
        $adminId = $admin?->id ?? 1;
        $accountantId = $accountant?->id ?? 2;

        $cats = DB::table('expense_categories')->get();
        $catByKey = [];
        foreach ($cats as $c) {
            $key = $c->key ?: \Str::slug($c->name_fr);
            $catByKey[$key] = $c;
        }

        // ---------------------------------------------------------------
        // 1) EMPLOYEES
        // ---------------------------------------------------------------
        if (DB::table('employees')->count() === 0) {
            $employees = [
                ['Yassine El Amrani',   'yassine.elamrani@chronorex.ma', '0612-345-001', 'Livreur',                4500.00, '2025-01-15'],
                ['Fatima Zahra Benali', 'fatima.benali@chronorex.ma',    '0612-345-002', 'Comptable',              7000.00, '2024-09-01'],
                ['Karim Idrissi',       'karim.idrissi@chronorex.ma',    '0612-345-003', 'Chef de flotte',         9000.00, '2023-03-20'],
                ['Salma Naciri',        'salma.naciri@chronorex.ma',     '0612-345-004', 'Assistante',             5000.00, '2025-06-10'],
                ['Mehdi Chakir',        'mehdi.chakir@chronorex.ma',     '0612-345-005', 'Livreur',                4200.00, '2025-02-05'],
                ['Noura Belkacem',      'noura.belkacem@chronorex.ma',   '0612-345-006', 'Resp. logistique',       8500.00, '2024-01-12'],
                ['Omar Tazi',           'omar.tazi@chronorex.ma',        '0612-345-007', 'Mécanicien',             6000.00, '2024-11-03'],
                ['Imane Filali',        'imane.filali@chronorex.ma',     '0612-345-008', 'Commerciale',            5500.00, '2025-04-22'],
            ];
            foreach ($employees as $e) {
                DB::table('employees')->insert([
                    'name'         => $e[0],
                    'email'        => $e[1],
                    'phone'        => $e[2],
                    'role_title'   => $e[3],
                    'base_salary'  => $e[4],
                    'hired_at'     => $e[5],
                    'status'       => 'active',
                    'created_by'   => $adminId,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
            $this->command->info('  + 8 employés créés');
        } else {
            $this->command->info('  (employés déjà présents - ignoré)');
        }

        $employees = DB::table('employees')->get();

        // ---------------------------------------------------------------
        // 2) EXPENSES (4 mois : mai -> aout 2026)
        // ---------------------------------------------------------------
        if (DB::table('expenses')->count() <= 30) { // seeder de base en a ~194, on ajoute quand meme un jeu propre
            $methods = ['cash', 'bank_transfer', 'check', 'credit_card'];
            $months = [
                '2026-05' => 32,
                '2026-06' => 30,
                '2026-07' => 34,
                '2026-08' => 22,
            ];
            $created = 0;
            foreach ($months as $ym => $count) {
                $daysInMonth = Carbon::parse($ym . '-01')->daysInMonth;
                for ($i = 0; $i < $count; $i++) {
                    $day = rand(1, $daysInMonth);
                    $date = Carbon::parse($ym . '-' . str_pad($day, 2, '0', STR_PAD_LEFT));
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
                        'salaries'            => 'Masse salariale ' . $date->format('F Y'),
                        'fuel'                => 'Carburant véhicule ' . ['A-101', 'B-205', 'C-318', 'D-442'][rand(0, 3)],
                        'vehicle_maintenance' => 'Entretien véhicule ' . ['A-101', 'B-205', 'C-318', 'D-442'][rand(0, 3)],
                        'internet'            => 'Abonnement Internet fibre',
                        'electricity'         => 'Facture électricité',
                        'advertising'         => 'Publicité réseaux sociaux',
                        'supplies'            => 'Fournitures de bureau',
                        default               => 'Frais divers',
                    };
                    $empId = (in_array($catKey, ['salaries', 'fuel', 'vehicle_maintenance'])) ? $employees[rand(0, count($employees) - 1)]->id : null;
                    DB::table('expenses')->insert([
                        'date'            => $date->format('Y-m-d'),
                        'amount'          => $amount,
                        'category_id'     => $cat->id,
                        'category_key'    => $catKey,
                        'description'     => $desc,
                        'payment_method'  => $methods[rand(0, 3)],
                        'receipt_path'    => null,
                        'notes'           => null,
                        'created_by'      => (rand(0, 1) ? $adminId : $accountantId),
                        'employee_id'     => $empId,
                        'created_at'      => $date,
                        'updated_at'      => $date,
                    ]);
                    $created++;
                }
            }
            $this->command->info("  + {$created} dépenses créées (mai-août 2026)");
        } else {
            $this->command->info('  (dépenses déjà présentes - ignoré)');
        }

        // ---------------------------------------------------------------
        // 3) SALARY ADVANCES
        // ---------------------------------------------------------------
        if (DB::table('salary_advances')->count() === 0) {
            $advStatuses = ['paid', 'deducted', 'approved', 'pending'];
            $created = 0;
            foreach ($employees as $emp) {
                $n = rand(1, 3);
                for ($i = 0; $i < $n; $i++) {
                    $month = ['2026-05', '2026-06', '2026-07'][rand(0, 2)];
                    $day = rand(1, 27);
                    $date = Carbon::parse($month . '-' . str_pad($day, 2, '0', STR_PAD_LEFT));
                    DB::table('salary_advances')->insert([
                        'employee_id' => $emp->id,
                        'amount'      => round(rand(500, 3000) + rand(0, 99) / 100, 2),
                        'date'        => $date->format('Y-m-d'),
                        'status'      => $advStatuses[rand(0, 3)],
                        'notes'       => 'Avance sur salaire - demande employé',
                        'created_by'  => $adminId,
                        'created_at'  => $date,
                        'updated_at'  => $date,
                    ]);
                    $created++;
                }
            }
            $this->command->info("  + {$created} avances sur salaire créées");
        } else {
            $this->command->info('  (avances déjà présentes - ignoré)');
        }

        // ---------------------------------------------------------------
        // 4) SALARY PAYMENTS (mai, juin, juillet 2026)
        // ---------------------------------------------------------------
        if (DB::table('salary_payments')->count() === 0) {
            $created = 0;
            foreach (['2026-05', '2026-06', '2026-07'] as $ym) {
                [$y, $m] = explode('-', $ym);
                foreach ($employees as $emp) {
                    $deducted = round(rand(0, 1500) + rand(0, 99) / 100, 2);
                    $net = round($emp->base_salary - $deducted, 2);
                    $payDay = rand(25, 30);
                    DB::table('salary_payments')->insert([
                        'employee_id'         => $emp->id,
                        'month'               => (int) $m,
                        'year'                => (int) $y,
                        'base_amount'         => $emp->base_salary,
                        'advances_deducted'   => $deducted,
                        'net_amount'          => $net,
                        'payment_method'      => ['cash', 'bank_transfer'][rand(0, 1)],
                        'transaction_reference' => 'PAY-' . $ym . '-' . $emp->id,
                        'paid_at'             => $ym . '-' . str_pad($payDay, 2, '0', STR_PAD_LEFT),
                        'created_by'          => $adminId,
                        'created_at'           => now(),
                        'updated_at'           => now(),
                    ]);
                    $created++;
                }
            }
            $this->command->info("  + {$created} paiements de salaire créés");
        } else {
            $this->command->info('  (paiements déjà présents - ignoré)');
        }

        // ---------------------------------------------------------------
        // 5) MONTHLY CLOSURES
        // ---------------------------------------------------------------
        if (DB::table('monthly_closures')->count() === 0) {
            $created = 0;
            foreach (['2026-05', '2026-06', '2026-07'] as $ym) {
                $exp = DB::table('expenses')
                    ->whereBetween('date', [$ym . '-01', $ym . '-31'])
                    ->sum('amount');
                $gains = round($exp * (1 + rand(20, 60) / 100), 2); // revenue > expenses
                $balance = round($gains - $exp, 2);
                DB::table('monthly_closures')->insert([
                    'month'      => $ym,
                    'gains'      => $gains,
                    'expenses'   => $exp,
                    'balance'    => $balance,
                    'closed_by'  => $adminId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $created++;
            }
            $this->command->info("  + {$created} clôtures mensuelles créées");
        } else {
            $this->command->info('  (clôtures déjà présentes - ignoré)');
        }

        // ---------------------------------------------------------------
        // 6) ALERTS (7 types)
        // ---------------------------------------------------------------
        if (DB::table('alerts')->count() === 0) {
            $alerts = [
                ['expense_created', 'info',     false, 'Nouvelle dépense enregistrée : Carburant véhicule A-101 (450,00 MAD)'],
                ['expense_updated', 'info',     false, 'Dépense modifiée : Loyer local bureaux'],
                ['expense_deleted', 'warning',  false, 'Dépense supprimée : Frais divers (120,00 MAD)'],
                ['high_expense',    'danger',   false, '⚠️ Dépense élevée détectée : Publicité réseaux sociaux (4 800,00 MAD)'],
                ['salary_reminder', 'warning',  false, '💼 Rappel de paie : salaires du mois à régler avant le 30'],
                ['daily_report',    'info',     true,  '📊 Rapport journalier généré pour le ' . now()->format('d/m/Y')],
                ['monthly_closure', 'success',  false, '📦 Clôture mensuelle de juillet 2026 terminée (solde 12 340,00 MAD)'],
                ['expense_created', 'info',     true,  'Nouvelle dépense : Fournitures de bureau (780,00 MAD)'],
                ['high_expense',    'danger',   true,  '⚠️ Dépense élevée : Loyer local bureaux (8 000,00 MAD)'],
                ['salary_reminder', 'warning',  true,  '💼 Rappel de paie envoyé à 8 employés'],
                ['monthly_closure', 'success',  true,  '📦 Clôture de juin 2026 : solde positif'],
                ['daily_report',    'info',     false, '📊 Rapport journalier : 14 dépenses, 9 230 MAD'],
            ];
            foreach ($alerts as $a) {
                DB::table('alerts')->insert([
                    'type'       => $a[0],
                    'message_ar' => $a[3],
                    'message_fr' => $a[3],
                    'severity'   => $a[1],
                    'is_read'    => $a[2],
                    'read_at'    => $a[2] ? now() : null,
                    'data'       => json_encode(['demo' => true]),
                    'created_at' => now()->subHours(rand(1, 72)),
                    'updated_at' => now(),
                ]);
            }
            $this->command->info('  + ' . count($alerts) . ' alertes créées');
        } else {
            $this->command->info('  (alertes déjà présentes - ignoré)');
        }

        // ---------------------------------------------------------------
        // 7) AUDIT LOGS
        // ---------------------------------------------------------------
        if (DB::table('audit_logs')->count() === 0) {
            $actions = ['login', 'create', 'update', 'login', 'create', 'delete', 'update', 'login'];
            $entities = [
                ['expense', 1], ['user', $adminId], ['expense', 5], ['employee', 2],
                ['expense', 9], ['expense', 12], ['employee', 4], ['user', $accountantId],
            ];
            for ($i = 0; $i < count($actions); $i++) {
                DB::table('audit_logs')->insert([
                    'user_id'     => ($i % 2 === 0) ? $adminId : $accountantId,
                    'action'      => $actions[$i],
                    'entity_type' => $entities[$i][0],
                    'entity_id'   => $entities[$i][1],
                    'old_values'  => null,
                    'new_values'  => json_encode(['demo' => true, 'at' => now()->subHours(rand(1, 48))->toDateTimeString()]),
                    'created_at'  => now()->subHours(rand(1, 48)),
                    'updated_at'  => now(),
                ]);
            }
            $this->command->info('  + ' . count($actions) . ' entrées journal d\'audit créées');
        } else {
            $this->command->info('  (journal d\'audit déjà présent - ignoré)');
        }

        $this->command->info('✅ Données de démonstration terminées.');
    }
}
