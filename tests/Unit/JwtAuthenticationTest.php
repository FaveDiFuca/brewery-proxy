<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JwtAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    // Test: generazione token JWT
    public function test_jwt_token_can_be_generated()
    {
        $user = User::factory()->create([
            'name' => 'test_user',
            'password' => bcrypt('password')
        ]);

        $token = JWTAuth::fromUser($user);

        $this->assertNotEmpty($token);
        $this->assertIsString($token);
    }

    // Test: recupero utente da token
    public function test_user_can_be_retrieved_from_token()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        JWTAuth::setToken($token);
        $retrievedUser = JWTAuth::authenticate();

        $this->assertEquals($user->id, $retrievedUser->id);
    }
}
