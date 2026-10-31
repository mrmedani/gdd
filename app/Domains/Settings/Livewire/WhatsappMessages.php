<?php

namespace App\Domains\Settings\Livewire;

use App\Domains\Settings\Models\WhatsappMessageTemplate;
use Database\Seeders\WhatsappMessageTemplateSeeder;
use Livewire\Component;

class WhatsappMessages extends Component
{
    public array $templates = [];

    public function mount(): void
    {
        $this->loadTemplates();
    }

    public function loadTemplates(): void
    {
        $this->templates = WhatsappMessageTemplate::orderBy('id')->get()->toArray();
    }

    public function save(string $type): void
    {
        $data = collect($this->templates)->firstWhere('type', $type);
        if (! $data) return;

        $validated = $this->validate([
            "templates.*.message_fr" => 'nullable|string',
            "templates.*.message_ar" => 'nullable|string',
            "templates.*.enabled" => 'boolean',
        ]);

        $template = WhatsappMessageTemplate::where('type', $type)->first();
        if ($template) {
            $template->update([
                'message_fr' => $data['message_fr'] ?? '',
                'message_ar' => $data['message_ar'] ?? '',
                'enabled' => (bool) ($data['enabled'] ?? true),
            ]);
        }

        $this->dispatch('saved');
    }

    /** Valeurs d'usine : /settings/whatsapp-messages peut restaurer un template raté. */
    public function resetTemplate(string $type): void
    {
        $default = collect(WhatsappMessageTemplateSeeder::defaults())->firstWhere('type', $type);
        if (! $default) return;

        // NE touche QUE message_fr/ar (l'admin garde son toggle enabled)
        WhatsappMessageTemplate::where('type', $type)->update([
            'message_fr' => $default['message_fr'],
            'message_ar' => $default['message_ar'],
        ]);
        $this->loadTemplates();
    }

    private function demoData(): array
    {
        // Valeurs de démo RÉALISTES, mêmes conventions que les vrais envois
        $period = getPeriodRange(getPeriodFromDate(now()));
        return [
            'description' => __('settings.wa_demo.description'),
            'amount' => '16 000',
            'currency' => getCurrency(),
            'category' => __('settings.wa_demo.category'),
            'date' => now()->format('d/m/Y'),
            'payment_method' => 'Cash',
            'user' => auth()->user()->name ?? '—',
            'company_name' => config('app.name'),
            'station' => 'STATION ORAN',
            'period' => formatPeriodLabel(getPeriodFromDate(now())),
            'total' => '59 510',
            'count' => '12',
            'threshold' => '5 000',
            'employees' => '6',
            'period_label' => 'oct. 2026',
            'period_start' => $period['start']->format('d/m/Y'),
            'period_end' => $period['end']->format('d/m/Y'),
            'gains' => '137 997',
            'expenses' => '129 681',
            'balance' => '25 510',
            'increase' => '500',
            'new_total' => '26 010',
            'deduction' => '500',
            'remaining' => '0',
            'source_name' => __('settings.wa_demo.source_name'),
            'source_type' => __('incomes.source_type.sale'),
            'label' => __('settings.wa_demo.label'),
            'due_date' => now()->addDays(4)->format('d/m/Y'),
            'due_in' => 'J-4',
            'title' => __('settings.wa_demo.title'),
            'party' => 'ECOTRACK',
            'end_date' => now()->addDays(4)->format('d/m/Y'),
        ];
    }

    public function render()
    {
        $demo = $this->demoData();
        $previews = [];
        $notFilled = [];

        foreach ($this->templates as $tpl) {
            $type = $tpl['type'];
            $template = WhatsappMessageTemplate::where('type', $type)->first();
            if (! $template) continue;

            $previews[$type] = str_replace(["\r\n", "\n"], ' • ', $template->format($demo, 'fr'));

            // WARN : une variable listée qui n'apparaît NI en FR NI en AR = template cassé
            $missing = [];
            foreach ($template->variables ?? [] as $var) {
                if (! str_contains((string) $tpl['message_fr'], '{' . $var . '}')
                    && ! str_contains((string) $tpl['message_ar'], '{' . $var . '}')) {
                    $missing[] = $var;
                }
            }
            $notFilled[$type] = $missing;
        }

        return view('livewire.whatsapp-messages', [
            'previews' => $previews,
            'notFilled' => $notFilled,
        ]);
    }
}
