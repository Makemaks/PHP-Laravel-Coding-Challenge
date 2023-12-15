<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanLogout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $this->actingAs($user);

        $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/logout')
            ->assertStatus(200)
            ->assertJson(['message' => 'Successfully logged out']);

        $this->assertTrue(
            $user->tokens->isEmpty() || $user->tokens()->where('expires_at', '<=', now())->exists()
        );
    }
}
