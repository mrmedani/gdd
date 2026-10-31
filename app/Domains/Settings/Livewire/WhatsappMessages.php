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

            // PREVIEW LIVE : rendu du texte TEL QUE SAISI (pas la ligne DB),
            // la prévisualisation suit l'édition après la maj Livewire
            $liveFr = (string) ($tpl['message_fr'] ?? '');
            $msgPreview = strtr($liveFr, array_combine(
                array_map(fn ($k) => '{' . $k . '}', array_keys($demo)),
                array_map(fn ($v) => (string) $v, $demo)
            ));
            $previews[$type] = str_replace("\r\n", "\n", $msgPreview); // le div preview est whitespace-pre-line

            // WARN : placeholders dans le message qui ne sont PAS dans la liste
            // variables[] du template (incompris par l'expéditeur => resteront
            // affichés comme du texte brut {x} dans le WhatsApp).
            preg_match_all('/\{([a-z_]+)\}/', (string) $tpl['message_fr'] . (string) ($tpl['message_ar'] ?? ''), $m);
            $unknown = array_values(array_diff(array_unique($m[1]), (array) ($tpl['variables'] ?? [])));
            $notFilled[$type] = $unknown;
        }

        return view('livewire.whatsapp-messages', [
            'previews' => $previews,
            'notFilled' => $notFilled,
        ]);
    }
}
