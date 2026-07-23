<?php

namespace App\Domains\Settings\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappMessageTemplate extends Model
{
    protected $fillable = [
        'type',
        'label_fr',
        'label_ar',
        'message_fr',
        'message_ar',
        'variables',
        'enabled',
    ];

    protected function casts(): array
    {
        return [
            'variables' => 'array',
            'enabled' => 'boolean',
        ];
    }

    public static function forType(string $type): ?self
    {
        return static::where('type', $type)->where('enabled', true)->first();
    }

    public function format(array $data, string $locale = 'fr'): string
    {
        $message = $locale === 'ar' ? $this->message_ar : $this->message_fr;

        $replacements = [];
        foreach ($data as $key => $value) {
            $replacements['{' . $key . '}'] = (string) $value;
        }

        return strtr($message, $replacements);
    }
}
