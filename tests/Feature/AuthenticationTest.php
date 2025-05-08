<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    // Test: accesso pagina login
    public function test_login_page_is_accessible()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    // Test: autenticazione con credenziali valide
    public function test_user_can_login_with_correct_credentials()
    {
        $user = User::create([
            'name' => 'testuser',
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);

        $response = $this->withoutMiddleware()
            ->post('/auth/login', [
                'name' => 'testuser',
                'password' => 'password'
            ]);

        $response->assertRedirect('/dashboard');
    }

    // Test: autenticazione con credenziali non valide
    public function test_user_cannot_login_with_incorrect_credentials()
    {
        $user = User::create([
            'name' => 'testuser',
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);

        $response = $this->withoutMiddleware()
            ->post('/auth/login', [
                'name' => 'testuser',
                'password' => 'wrong_password'
            ]);

        $response->assertStatus(302);
        $this->assertTrue($response->isRedirect(url()->previous()));
    }
}
