<?php

namespace Tests\Feature;

use App\Http\Livewire\BreweryList;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

class BreweryListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    // Test: caricamento dati birrerie
    public function test_component_can_load_breweries()
    {
        Http::fake([
            'api.openbrewerydb.org/v1/breweries*' => Http::response([
                [
                    'id' => '1',
                    'name' => 'Test Brewery 1',
                    'brewery_type' => 'micro',
                    'city' => 'Test City',
                    'country' => 'USA',
                    'website_url' => 'https://example.com'
                ],
                [
                    'id' => '2',
                    'name' => 'Test Brewery 2',
                    'brewery_type' => 'brewpub',
                    'city' => 'Another City',
                    'country' => 'USA',
                    'website_url' => null
                ],
            ], 200)
        ]);

        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        session(['token' => $token]);

        $livewire = Livewire::test(BreweryList::class);
        $livewire->call('loadBreweries');

        $livewire->assertSet('isLoading', false);
        $this->assertCount(2, $livewire->get('breweries'));
        $breweries = $livewire->get('breweries');
        $this->assertEquals('Test Brewery 1', $breweries[0]['name']);
        $this->assertEquals('Test Brewery 2', $breweries[1]['name']);
        $livewire->assertSet('errorMessage', null);
    }

    // Test: funzionalità paginazione
    public function test_pagination_works()
    {
        Http::fake([
            'api.openbrewerydb.org/v1/breweries*' => Http::response([
                [
                    'id' => '1',
                    'name' => 'Test Brewery 1',
                    'brewery_type' => 'micro',
                    'city' => 'Test City',
                    'country' => 'USA',
                    'website_url' => 'https://example.com'
                ],
                [
                    'id' => '2',
                    'name' => 'Test Brewery 2',
                    'brewery_type' => 'brewpub',
                    'city' => 'Another City',
                    'country' => 'USA',
                    'website_url' => null
                ],
            ], 200)
        ]);

        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        session(['token' => $token]);

        Livewire::test(BreweryList::class)
            ->call('loadBreweries')
            ->assertSet('currentPage', 1)
            ->call('nextPage')
            ->assertSet('currentPage', 2)
            ->call('previousPage')
            ->assertSet('currentPage', 1);
    }

    // Test: gestione errori API
    public function test_handles_api_error()
    {
        Http::fake([
            'api.openbrewerydb.org/v1/breweries*' => Http::response(
                ['error' => 'Server Error'],
                500
            )
        ]);

        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        session(['token' => $token]);

        $livewire = Livewire::test(BreweryList::class);
        $livewire->call('loadBreweries');

        $livewire->assertSet('isLoading', false);
        $this->assertNotNull($livewire->get('errorMessage'),
            'Il componente non ha registrato l\'errore HTTP 500');

        $breweries = $livewire->get('breweries');
        $this->assertTrue(
            empty($breweries) || $breweries === null,
            'Il componente non dovrebbe avere birrerie dopo un errore'
        );
    }
}
