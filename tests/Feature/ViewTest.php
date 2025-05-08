<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ViewTest extends TestCase
{
    use RefreshDatabase;

    // Test: rendering dashboard con componente brewery_list
    public function test_dashboard_view_renders_with_brewery_list()
    {
        // Prepara mock API
        Http::fake([
            'api.openbrewerydb.org/v1/breweries*' => Http::response([
                [
                    'id' => '1',
                    'name' => 'Test Brewery 1',
                    'brewery_type' => 'micro',
                    'city' => 'Test City',
                    'country' => 'USA',
                ],
                [
                    'id' => '2',
                    'name' => 'Test Brewery 2',
                    'brewery_type' => 'brewpub',
                    'city' => 'Another City',
                    'country' => 'USA',
                ],
            ], 200)
        ]);

        // Autentica utente
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        session(['token' => $token]);

        // Test della risposta HTTP
        $response = $this->get('/dashboard');

        // Verifica stato e contenuto
        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
        $response->assertSee('Dashboard', false);
        $response->assertSeeText($user->username);
        $response->assertSee('wire:id', false);
    }

    // Test: reindirizzamento utenti non autenticati
    public function test_dashboard_redirects_when_not_authenticated()
    {
        $response = $this->get('/dashboard');
        $this->assertTrue($response->isRedirect(), 'Gli utenti non autenticati dovrebbero essere reindirizzati');
    }
}
