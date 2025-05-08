<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class DashboardController extends Controller
{
    public function index()
    {
        // CONTROLLO AUSILIARIO TOKEN
        if (!session()->has('token')) {
            Log::warning('DashboardController: Tentativo di accesso senza token');
            return redirect()->route('login')->with('error', 'Accesso non autorizzato.');
        }

        // PRENDE TOKEN DA SESSIONE
        $token = session('token');
        JWTAuth::setToken($token);
        // AUTENTICA L'UTENTE
        $user = JWTAuth::authenticate();

        Log::info('DashboardController: Accesso alla dashboard completato', [
            'user' => $user ? $user->name : 'sconosciuto'
        ]);

        return view('dashboard');
    }
}
