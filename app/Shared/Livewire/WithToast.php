<?php

namespace App\Shared\Livewire;

trait WithToast
{
    public function notify(string $message, string $type = 'success', int $duration = 3000): void
    {
        $this->dispatch('notify', message: $message, type: $type, duration: $duration);
    }
}
