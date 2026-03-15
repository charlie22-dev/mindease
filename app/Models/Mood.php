<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mood extends Model
{
    protected $fillable = ['user_id', 'mood', 'score', 'note'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function moodEmoji(string $mood): string
    {
        return match($mood) {
            'very_sad'  => '😢',
            'sad'       => '😕',
            'neutral'   => '😐',
            'happy'     => '🙂',
            'very_happy'=> '😄',
            default     => '😐',
        };
    }

    public static function moodScore(string $mood): int
    {
        return match($mood) {
            'very_sad'  => 1,
            'sad'       => 2,
            'neutral'   => 3,
            'happy'     => 4,
            'very_happy'=> 5,
            default     => 3,
        };
    }
}