<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanRegister()
    {
        $userData = [
            'name'     => 'John Mark',
            'email'    => 'johnmark@gmail.com',
            'password' => 'password123',
        ];

        $user = User::factory()->make($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseCount('users', 0);

        $user->save();

        $this->assertDatabaseCount('users', 1);
    }
}
