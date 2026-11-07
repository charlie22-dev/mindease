<?php

namespace Tests\Feature;

use App\Models\Mood;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MoodTrackerTest extends TestCase
{
    use RefreshDatabase;

    private function entry(User $user, string $mood, string $at, ?string $note = null): Mood
    {
        $entry = new Mood(['user_id' => $user->id, 'mood' => $mood, 'score' => Mood::moodScore($mood), 'note' => $note]);
        $entry->created_at = CarbonImmutable::parse($at, 'UTC');
        $entry->updated_at = $entry->created_at;
        $entry->save();

        return $entry;
    }

    public function test_empty_tracker_renders_without_inventing_a_neutral_mood(): void
    {
        $this->actingAs(User::factory()->create())->get('/mood')
            ->assertOk()->assertSee('Save your first check-in')
            ->assertViewHas('todayMood', null)->assertViewHas('recordedDays', 0)
            ->assertViewHas('chartData', fn ($days) => $days->count() === 14 && $days->every(fn ($day) => $day['score'] === null));
    }

    public function test_chart_uses_recent_local_days_and_averages_multiple_check_ins(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-11-07 02:00:00', 'UTC'));
        $user = User::factory()->create();
        $other = User::factory()->create();
        $this->entry($user, 'very_sad', '2026-10-24 15:59:59'); // Before local Oct 25.
        $this->entry($user, 'neutral', '2026-10-24 16:00:00'); // Local Oct 25 boundary.
        $this->entry($user, 'sad', '2026-11-06 15:59:59'); // Local Nov 6.
        $this->entry($user, 'very_sad', '2026-11-06 16:00:00'); // Local Nov 7.
        $latest = $this->entry($user, 'very_happy', '2026-11-07 01:00:00');
        $this->entry($other, 'happy', '2026-11-07 01:00:00');
        $this->entry($user, 'happy', '2026-11-07 16:00:00'); // Next local day is excluded.

        $this->actingAs($user)->get('/mood?timezone=Asia/Manila')
            ->assertOk()->assertViewHas('recordedDays', 3)
            ->assertViewHas('todayMood', fn ($mood) => $mood->id === $latest->id)
            ->assertViewHas('chartData', function ($days) {
                return $days->first()['date'] === '2026-10-25'
                    && $days->first()['score'] === 3.0
                    && $days[1]['score'] === null
                    && $days[12]['score'] === 2.0
                    && $days->last()['score'] === 3.0
                    && $days->last()['count'] === 2;
            })->assertSessionHas('mood_timezone', 'Asia/Manila');
    }

    public function test_saving_uses_the_selected_mood_score_and_preserves_previous_check_ins(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post('/mood', ['mood' => 'sad', 'score' => 5, 'note' => 'A difficult morning.', 'timezone' => 'Asia/Manila'])
            ->assertRedirect('/mood')->assertSessionHas('success', 'Check-in saved.');
        $this->post('/mood', ['mood' => 'happy'])->assertRedirect('/mood');
        $this->assertDatabaseHas('moods', ['user_id' => $user->id, 'mood' => 'sad', 'score' => 2, 'note' => 'A difficult morning.']);
        $this->assertDatabaseHas('moods', ['user_id' => $user->id, 'mood' => 'happy', 'score' => 4]);
        $this->assertDatabaseCount('moods', 2);
    }

    public function test_invalid_input_is_rejected_and_previous_input_is_preserved(): void
    {
        $this->actingAs(User::factory()->create())->from('/mood')->post('/mood', [
            'mood' => 'invalid', 'note' => str_repeat('x', 501), 'timezone' => 'invalid',
        ])->assertRedirect('/mood')->assertSessionHasErrors(['mood', 'note', 'timezone']);
        $this->assertDatabaseCount('moods', 0);
    }

    public function test_history_is_paginated_private_and_escaped(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        for ($day = 1; $day <= 11; $day++) {
            $this->entry($user, 'neutral', sprintf('2026-10-%02d 10:00:00', $day), '<script>alert(1)</script>');
        }
        $this->entry($other, 'happy', '2026-11-01 10:00:00', 'Private other-user note');
        $this->actingAs($user)->get('/mood')->assertOk()
            ->assertDontSee('Private other-user note')
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertViewHas('recentMoods', fn ($entries) => $entries->total() === 11 && $entries->count() === 10);
        $this->get('/mood?page=2')->assertOk()->assertViewHas('recentMoods', fn ($entries) => $entries->count() === 1);
    }

    public function test_tracker_requires_authentication(): void
    {
        $this->get('/mood')->assertRedirect('/login');
        $this->post('/mood', ['mood' => 'happy'])->assertRedirect('/login');
    }
}
