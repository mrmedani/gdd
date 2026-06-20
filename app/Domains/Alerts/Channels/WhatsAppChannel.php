<?php

namespace App\Domains\Alerts\Channels;

use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class WhatsAppChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (!($notifiable instanceof User)) {
            return;
        }

        if (!$notifiable->notify_whatsapp) {
            Log::info('WhatsApp skip: notify_whatsapp off', ['user' => $notifiable->id]);
            return;
        }

        if (empty($notifiable->whatsapp_phone)) {
            Log::info('WhatsApp skip: no phone', ['user' => $notifiable->id]);
            return;
        }

        if (!method_exists($notification, 'toWhatsApp')) {
            return;
        }

        $message = $notification->toWhatsApp($notifiable);

        if (is_string($message) && $message !== '') {
            $sent = app(WhatsAppService::class)->sendTo($notifiable->whatsapp_phone, $message);
            Log::info('WhatsApp send result', [
                'user' => $notifiable->id,
                'phone' => $notifiable->whatsapp_phone,
                'sent' => $sent,
            ]);
        }
    }
}
