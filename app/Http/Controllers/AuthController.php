<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $requestId = uniqid('login-');

        Log::info("AuthController: Tentativo di login", [
            'request_id' => $requestId,
            'username' => $request->input('name')
        ]);

        try {
            $credentials = $request->validate([
                'name' => 'required|string',
                'password' => 'required|string',
            ]);

            if ($token = auth('api')->attempt($credentials)) {
                // LOGIN OK
                $user = auth('api')->user();
                session(['token' => $token]);
                Log::info("AuthController: Login riuscito", [
                    'request_id' => $requestId,
                    'user_id' => $user->id,
                    'username' => $user->name
                ]);

                return redirect('/dashboard');
            }

            // ERRORE LOGIN
            Log::warning("AuthController: Login fallito - credenziali errate", [
                'request_id' => $requestId,
                'username' => $request->input('name')
            ]);

            return back()->withErrors(['password' => 'Credenziali non valide']);
        } catch (\Exception $e) {
            Log::error("AuthController: Eccezione durante login", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            Log::error('Eccezione durante login: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Errore di sistema: ' . $e->getMessage()]);
        }
    }

    public function logout()
    {
        Log::info("AuthController: Logout utente", [
            'user_id' => auth('api')->check() ? auth('api')->user()->id : 'guest'
        ]);

        session()->flash('logout_redirect', true);

        // Rimuove il token
        session()->forget('token');

        Log::info("AuthController: Sessione e token eliminati");

        return redirect()->route('login');
    }
}
