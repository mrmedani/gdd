<?php

namespace App\Domains\Settings\Observers;

use App\Domains\Alerts\Models\Alert;
use App\Domains\Expenses\Models\AuditLog;
use App\Domains\Settings\Models\Setting;
use App\Domains\Settings\Models\WhatsappMessageTemplate;
use App\Domains\Treasury\Models\MonthlyClosure;
use App\Services\TelegramService;
use App\Services\WhatsAppService;

class MonthlyClosureObserver
{
    private function log(string $action, MonthlyClosure $closure, ?array $old = null, ?array $new = null): void
    {
        if (!$userId = auth()->id()) return;
        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => 'monthly_closure',
            'entity_id' => $closure->id,
            'old_values' => $old,
            'new_values' => $new,
        ]);
    }

    public function created(MonthlyClosure $closure): void
    {
        $this->log('created', $closure, null, $closure->only(['month', 'gains', 'expenses', 'balance']));

        $currency = getCurrency();
        $closureMonthLabel = formatPeriodLabel($closure->month);

        if ($closure->balance < 0) {
            $deficitIncrease = abs($closure->balance);
            $currentDeficit = (float) Setting::get('cash_deficit', 0);
            $newDeficit = $currentDeficit + $deficitIncrease;
            Setting::set('cash_deficit', (string) $newDeficit);

            if (!Alert::where('type', 'deficit_increased')->where('data->closure_month', $closure->month)->exists()) {
                Alert::create([
                    'type' => 'deficit_increased',
                    'message_ar' => "تم إضافة {$deficitIncrease} {$currency} إلى العجز النقدي نتيجة خسارة فترة {$closureMonthLabel}",
                    'message_fr' => "Ajout de {$deficitIncrease} {$currency} au manque en caisse suite à la perte de la période {$closureMonthLabel}",
                    'severity' => 'error',
                    'data' => [
                        'closure_month' => $closure->month,
                        'increase' => $deficitIncrease,
                        'new_total' => $newDeficit,
                        'action_url' => url('/treasury'),
                        'action_label' => 'Voir la trésorerie',
                    ],
                ]);
            }

            try {
                app(TelegramService::class)->send(
                    "<b>⚠️ Augmentation du manque en caisse</b>\n"
                    . "──────────────\n"
                    . "📆 Période : {$closureMonthLabel}\n"
                    . "📈 Augmentation : <b>" . formatMoney($deficitIncrease) . " {$currency}</b>\n"
                    . "💰 Nouveau total : <b>" . formatMoney($newDeficit) . " {$currency}</b>"
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Telegram deficit increased failed: ' . $e->getMessage());
            }

            try {
                $admins = \App\Models\User::whereHas('role', fn($q) => $q->where('name', 'admin'))->get();
                foreach ($admins as $admin) {
                    if (! $admin->notify_whatsapp || ! $admin->whatsapp_phone) continue;
                    $companyName = Setting::get('app_name', config('app.name'));
                    $template = WhatsappMessageTemplate::forType('deficit_increased');
                    $msg = $template
                        ? $template->format([
                            'period' => $closureMonthLabel,
                            'increase' => formatMoney($deficitIncrease),
                            'currency' => $currency,
                            'new_total' => formatMoney($newDeficit),
                            'company_name' => $companyName,
                        ], $admin->locale ?? 'fr')
                        : "⚠️ Augmentation du manque en caisse\n"
                        . "──────────────\n"
                        . "📆 Période : {$closureMonthLabel}\n"
                        . "📈 Augmentation : " . formatMoney($deficitIncrease) . " {$currency}\n"
                        . "💰 Nouveau total : " . formatMoney($newDeficit) . " {$currency}";
                    app(WhatsAppService::class)->sendTo($admin->whatsapp_phone, $msg);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('WhatsApp deficit increased failed: ' . $e->getMessage());
            }
        } elseif ($closure->balance > 0) {
            $currentDeficit = (float) Setting::get('cash_deficit', 0);
            if ($currentDeficit > 0) {
                $deduction = min($currentDeficit, $closure->balance);
                $newDeficit = $currentDeficit - $deduction;
                Setting::set('cash_deficit', (string) $newDeficit);

                if (!Alert::where('type', 'deficit_deducted')->where('data->closure_month', $closure->month)->exists()) {
                    Alert::create([
                        'type' => 'deficit_deducted',
                        'message_ar' => "تم خصم {$deduction} {$currency} من العجز النقدي لفترة {$closureMonthLabel}",
                        'message_fr' => "Manque en caisse déduit de {$deduction} {$currency} pour la période {$closureMonthLabel}",
                        'severity' => 'info',
                        'data' => [
                            'closure_month' => $closure->month,
                            'deduction' => $deduction,
                            'remaining' => $newDeficit,
                            'action_url' => url('/treasury'),
                            'action_label' => 'Voir la trésorerie',
                        ],
                    ]);
                }

                if ($newDeficit <= 0 && !Alert::where('type', 'deficit_covered')->where('data->closure_month', $closure->month)->exists()) {
                    Alert::create([
                        'type' => 'deficit_covered',
                        'message_ar' => 'تم تغطية العجز النقدي بالكامل!',
                        'message_fr' => 'Le manque en caisse a été entièrement comblé !',
                        'severity' => 'success',
                        'data' => [
                            'closure_month' => $closure->month,
                            'closure_balance' => $closure->balance,
                            'action_url' => url('/treasury'),
                            'action_label' => 'Voir la trésorerie',
                        ],
                    ]);
                }

                try {
                    app(TelegramService::class)->send(
                        "<b>✅ Réduction du manque en caisse</b>\n"
                        . "──────────────\n"
                        . "📆 Période : {$closureMonthLabel}\n"
                        . "📉 Déduction : <b>" . formatMoney($deduction) . " {$currency}</b>\n"
                        . "💰 Restant : <b>" . formatMoney($newDeficit) . " {$currency}</b>"
                    );

                    if ($newDeficit <= 0) {
                        app(TelegramService::class)->send(
                            "<b>🎉 Manque en caisse entièrement comblé !</b>\n"
                            . "──────────────\n"
                            . "📆 Période : {$closureMonthLabel}\n"
                            . "💰 Le solde du manque en caisse est désormais à zéro."
                        );
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Telegram deficit deducted failed: ' . $e->getMessage());
                }

                try {
                    $admins = \App\Models\User::whereHas('role', fn($q) => $q->where('name', 'admin'))->get();
                    foreach ($admins as $admin) {
                        if (! $admin->notify_whatsapp || ! $admin->whatsapp_phone) continue;
                        $locale = $admin->locale ?? 'fr';

                        $companyName = Setting::get('app_name', config('app.name'));
                        $deductionTemplate = WhatsappMessageTemplate::forType('deficit_deducted');
                        $deductionMsg = $deductionTemplate
                            ? $deductionTemplate->format([
                                'period' => $closureMonthLabel,
                                'deduction' => formatMoney($deduction),
                                'currency' => $currency,
                                'remaining' => formatMoney($newDeficit),
                                'company_name' => $companyName,
                            ], $locale)
                            : "✅ Réduction du manque en caisse\n"
                            . "──────────────\n"
                            . "📆 Période : {$closureMonthLabel}\n"
                            . "📉 Déduction : " . formatMoney($deduction) . " {$currency}\n"
                            . "💰 Restant : " . formatMoney($newDeficit) . " {$currency}";
                        app(WhatsAppService::class)->sendTo($admin->whatsapp_phone, $deductionMsg);

                        if ($newDeficit <= 0) {
                            $coveredTemplate = WhatsappMessageTemplate::forType('deficit_covered');
                            $coveredMsg = $coveredTemplate
                                ? $coveredTemplate->format([
                                    'period' => $closureMonthLabel,
                                    'company_name' => $companyName,
                                ], $locale)
                                : "🎉 Manque en caisse entièrement comblé !\n"
                                . "──────────────\n"
                                . "📆 Période : {$closureMonthLabel}\n"
                                . "💰 Le solde du manque en caisse est désormais à zéro.";
                            app(WhatsAppService::class)->sendTo($admin->whatsapp_phone, $coveredMsg);
                        }
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('WhatsApp deficit deducted failed: ' . $e->getMessage());
                }
            }
        }
    }

    public function deleted(MonthlyClosure $closure): void
    {
        $this->log('deleted', $closure, $closure->getOriginal(), null);

        $currency = getCurrency();

        if ($closure->balance < 0) {
            $deficitDecrease = abs($closure->balance);
            $currentDeficit = (float) Setting::get('cash_deficit', 0);
            $newDeficit = max(0, $currentDeficit - $deficitDecrease);
            Setting::set('cash_deficit', (string) $newDeficit);

            Alert::where('type', 'deficit_increased')
                ->where('data->closure_month', $closure->month)
                ->delete();
        } elseif ($closure->balance > 0) {
            $currentDeficit = (float) Setting::get('cash_deficit', 0);
            $closureMonthLabel = formatPeriodLabel($closure->month);

            $relatedAlerts = Alert::where('type', 'deficit_deducted')
                ->where('data->closure_month', $closure->month)
                ->get();

            foreach ($relatedAlerts as $alert) {
                $deduction = (float) ($alert->data['deduction'] ?? 0);
                $newDeficit = $currentDeficit + $deduction;
                Setting::set('cash_deficit', (string) $newDeficit);
                $currentDeficit = $newDeficit;
            }

            Alert::where('type', 'deficit_deducted')
                ->where('data->closure_month', $closure->month)
                ->delete();

            Alert::where('type', 'deficit_covered')
                ->where('data->closure_month', $closure->month)
                ->delete();
        }
    }
}
