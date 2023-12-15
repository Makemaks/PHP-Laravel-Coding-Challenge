<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanLogin()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $credentials = [
            'email'    => $user->email,
            'password' => 'password123',
        ];

        $this->assertGuest();

        $this->postJson('/api/login', $credentials)
            ->assertStatus(200)
            ->assertJsonStructure(['token']);

        $this->assertAuthenticated();
    }
}
