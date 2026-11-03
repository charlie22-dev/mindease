<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.groq.key' => 'test-key', 'services.groq.model' => 'test-model']);
        Http::preventStrayRequests();
    }

    public function test_guest_message_is_sent_without_requiring_history(): void
    {
        Http::fake(['api.groq.com/*' => Http::response(['choices' => [['message' => ['content' => 'Hello there.']]]])]);

        $this->postJson('/guest-chat', ['message' => 'Hello'])->assertOk()->assertJson(['reply' => 'Hello there.']);

        Http::assertSent(fn ($request) => $request['model'] === 'test-model'
            && array_slice($request['messages'], -1) === [['role' => 'user', 'content' => 'Hello']]);
    }

    public function test_guest_message_already_in_history_is_not_duplicated(): void
    {
        Http::fake(['api.groq.com/*' => Http::response(['choices' => [['message' => ['content' => 'Hello.']]]])]);

        $this->postJson('/guest-chat', ['message' => 'Hello', 'history' => [['role' => 'user', 'content' => 'Hello']]])->assertOk();

        Http::assertSent(fn ($request) => count($request['messages']) === 2);
    }

    public function test_malformed_guest_history_is_rejected(): void
    {
        $this->postJson('/guest-chat', ['message' => 'Hello', 'history' => [['role' => 'user']]])
            ->assertUnprocessable()->assertJsonValidationErrors('history.0.content');
        Http::assertNothingSent();
    }

    public function test_provider_failure_is_reported_as_an_error(): void
    {
        Http::fake(['api.groq.com/*' => Http::response(['error' => ['code' => 'model_not_found']], 404)]);
        $this->postJson('/guest-chat', ['message' => 'Hello'])->assertStatus(502)->assertJsonStructure(['reply']);
    }

    public function test_signed_in_chat_uses_configured_model_and_saves_reply(): void
    {
        Http::fake(['api.groq.com/*' => Http::response(['choices' => [['message' => ['content' => 'How are you?']]]])]);
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/chat/send', ['message' => 'Hello'])
            ->assertOk()->assertJson(['reply' => 'How are you?']);

        Http::assertSent(fn ($request) => $request['model'] === 'test-model');
        $this->assertDatabaseHas('messages', ['user_id' => $user->id, 'role' => 'assistant', 'content' => 'How are you?']);
    }
}
