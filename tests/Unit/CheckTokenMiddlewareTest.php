<?php

namespace Tests\Unit;

use App\Http\Middleware\CheckTokenMiddleware;
use App\Models\User;
use Illuminate\Http\Request;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CheckTokenMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new CheckTokenMiddleware();
    }

    // Test: reindirizzamento senza token
    public function test_middleware_redirects_when_token_missing()
    {
        $request = Request::create('/dashboard', 'GET');

        $response = $this->middleware->handle($request, function() {
            return response('OK');
        });

        $this->assertEquals(302, $response->getStatusCode()); // Verifica redirect
        $this->assertEquals(route('login'), $response->headers->get('Location'));
    }

    // Test: accesso consentito con token valido
    public function test_middleware_allows_request_with_valid_token()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $request = Request::create('/dashboard', 'GET');
        session(['token' => $token]);

        $response = $this->middleware->handle($request, function() {
            return response('OK');
        });

        $this->assertEquals('OK', $response->getContent());
    }
}
