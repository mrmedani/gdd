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

    private static function curlRequest(string $method, string $url, ?string $payload = null, int $timeout = 15): ?string
    {
        // Try curl_exec first (IPv4 forced)
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => $timeout,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                CURLOPT_FAILONERROR => false,
            ]);
            if ($method === 'POST') {
                curl_setopt_array($ch, [
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $payload ?? '',
                    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                ]);
            }
            $result = curl_exec($ch);
            $errno = curl_errno($ch);
            curl_close($ch);
            if ($result !== false && $errno === 0) {
                return $result;
            }
        }

        // Fallback: system curl via exec
        $safeUrl = escapeshellarg($url);
        $cmd = "curl -s --connect-timeout 5 --max-time {$timeout} -4";
        if ($method === 'POST') {
            $safePayload = escapeshellarg($payload ?? '');
            $cmd .= " -X POST -H 'Content-Type: application/json' -d {$safePayload}";
        }
        $cmd .= " {$safeUrl}";
        $output = @shell_exec($cmd);
        if ($output !== null && $output !== '') {
            return $output;
        }

        return null;
    }

    public static function sendMessage(string $workerUrl, string $chatId, string $text): bool
    {
        $payload = json_encode(['chatId' => $chatId, 'message' => $text]);
        $result = static::curlRequest('POST', $workerUrl . '/send', $payload, 15);
        if ($result === null) {
            Log::warning('WhatsApp: worker request failed');
            return false;
        }
        $json = json_decode($result, true);
        return ($json['ok'] ?? false) === true;
    }

    public static function startWorker(string $workerUrl): bool
    {
        return static::curlRequest('POST', $workerUrl . '/start', null, 5) !== null;
    }

    public static function getStatus(string $workerUrl): ?array
    {
        $result = static::curlRequest('GET', $workerUrl . '/status', null, 5);
        if ($result === null) return null;
        return json_decode($result, true);
    }

    public static function getQr(string $workerUrl): ?string
    {
        $result = static::curlRequest('GET', $workerUrl . '/qr', null, 5);
        if ($result === null) return null;
        $json = json_decode($result, true);
        return $json['qr'] ?? null;
    }

    public static function disconnect(string $workerUrl): bool
    {
        return static::curlRequest('POST', $workerUrl . '/disconnect', null, 10) !== null;
    }
}
