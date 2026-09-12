<?php

namespace App\Domains\AI\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AiConversation extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'messages',
        'last_activity',
    ];

    protected function casts(): array
    {
        return [
            'messages' => 'array',
            'last_activity' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Titre auto à partir du premier message user. */
    public static function titleFromMessages(array $messages): string
    {
        foreach ($messages as $m) {
            if (($m['role'] ?? '') === 'user') {
                $c = trim((string) ($m['content'] ?? ''));
                if ($c !== '') {
                    return mb_substr($c, 0, 60);
                }
            }
        }
        return __('ai.title');
    }
}
