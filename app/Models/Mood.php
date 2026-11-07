<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mood extends Model
{
    public const OPTIONS = [
        'very_sad' => ['label' => 'Very low', 'score' => 1],
        'sad' => ['label' => 'Low', 'score' => 2],
        'neutral' => ['label' => 'Okay', 'score' => 3],
        'happy' => ['label' => 'Good', 'score' => 4],
        'very_happy' => ['label' => 'Great', 'score' => 5],
    ];

    protected $fillable = ['user_id', 'mood', 'score', 'note'];

    protected function casts(): array
    {
        return ['score' => 'integer'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function moodLabel(string $mood): string
    {
        return self::OPTIONS[$mood]['label'] ?? 'Unknown';
    }

    public static function moodScore(string $mood): int
    {
        return self::OPTIONS[$mood]['score'] ?? throw new \InvalidArgumentException('Invalid mood.');
    }
}
