<?php

namespace App\Domains\Settings\Livewire;

use App\Domains\Settings\Models\WhatsappMessageTemplate;
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

    public function render()
    {
        return view('livewire.whatsapp-messages');
    }
}
