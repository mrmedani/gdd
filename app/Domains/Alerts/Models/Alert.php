<?php

namespace App\Domains\Alerts\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $fillable = [
        'type',
        'message_ar',
        'message_fr',
        'message_en',
        'severity',
        'is_read',
        'read_at',
        'data',
        'action_url',
        'action_label',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
            'data' => 'array',
        ];
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOfSeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeTypes($query, array $types)
    {
        return $query->whereIn('type', $types);
    }

    public function scopeCreatedAfter($query, $date)
    {
        return $query->where('created_at', '>=', $date);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public static function alreadySentToday(string $type, ?array $dataCheck = null): bool
    {
        $q = static::today()->where('type', $type);
        if ($dataCheck) {
            foreach ($dataCheck as $key => $value) {
                $q->where("data->{$key}", $value);
            }
        }
        return $q->exists();
    }
}
