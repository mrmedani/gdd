<?php

namespace App\Services;

use App\Domains\Settings\Models\Setting;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private ?string $chatId;
    private bool $enabled;
    private string $workerUrl;

    public function __construct()
    {
        $this->chatId = Setting::get('whatsapp_chat_id') ?: '';
        $this->enabled = (bool) Setting::get('whatsapp_enabled', false);
        $this->workerUrl = Setting::get('whatsapp_worker_url') ?: 'http://127.0.0.1:9090';
    }

    public function isEnabled(): bool
    {
        return $this->enabled && $this->chatId;
    }

    public function send(string $message): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        $result = static::sendMessage($this->workerUrl, $this->chatId, $message);

        return $result;
    }

    public function sendTo(string $phone, string $message): bool
    {
        if (!$this->enabled) {
            Log::info('WhatsApp sendTo: service not enabled');
            return false;
        }

        $number = ltrim($phone, '+');
        if (!str_contains($number, '@')) {
            $number .= '@c.us';
        }

        $result = static::sendMessage($this->workerUrl, $number, $message);

        return $result;
    }

    public static function sendMessage(string $workerUrl, string $chatId, string $text): bool
    {
        try {
            $payload = json_encode([
                'chatId' => $chatId,
                'message' => $text,
            ]);

            $ctx = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => "Content-Type: application/json\r\nContent-Length: " . strlen($payload),
                    'content' => $payload,
                    'timeout' => 15,
                    'ignore_errors' => true,
                ],
            ]);

            $result = @file_get_contents($workerUrl . '/send', false, $ctx);

            if ($result === false) {
                Log::warning('WhatsApp: worker request failed');
                return false;
            }

            $json = json_decode($result, true);
            return ($json['ok'] ?? false) === true;
        } catch (\Throwable $e) {
            Log::warning('WhatsApp send error: ' . $e->getMessage());
            return false;
        }
    }

    public static function startWorker(string $workerUrl): bool
    {
        try {
            $ctx = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'timeout' => 5,
                    'ignore_errors' => true,
                ],
            ]);
            $result = @file_get_contents($workerUrl . '/start', false, $ctx);
            return $result !== false;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function getStatus(string $workerUrl): ?array
    {
        try {
            $result = @file_get_contents($workerUrl . '/status', false, stream_context_create([
                'http' => ['timeout' => 5, 'ignore_errors' => true],
            ]));
            if ($result === false) return null;
            return json_decode($result, true);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function getQr(string $workerUrl): ?string
    {
        try {
            $result = @file_get_contents($workerUrl . '/qr', false, stream_context_create([
                'http' => ['timeout' => 5, 'ignore_errors' => true],
            ]));
            if ($result === false) return null;
            $json = json_decode($result, true);
            return $json['qr'] ?? null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function disconnect(string $workerUrl): bool
    {
        try {
            $ctx = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'timeout' => 10,
                    'ignore_errors' => true,
                ],
            ]);
            $result = @file_get_contents($workerUrl . '/disconnect', false, $ctx);
            return $result !== false;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
