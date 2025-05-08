<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\CheckTokenMiddleware;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

// LOGIN
Route::get('/', function () {
    // VERIFICA CHE NON ARRIVI DA LOGOUT
    $isFromLogout = session()->has('logout_redirect');

    if (!$isFromLogout && session()->has('token')) {
        try {
            // VERIFICA CHE NON ARRIVI DA LOGNOUT ED HA TOKEN
            $token = session('token');
            JWTAuth::setToken($token);
            $user = JWTAuth::authenticate();

            if ($user) {
                Log::info('Route: Utente già autenticato, reindirizzamento automatico alla dashboard', [
                    'username' => $user->name
                ]);
                return redirect()->route('dashboard');
            }
        } catch (\Exception $e) {
            session()->forget('token');
        }
    }

    session()->forget('logout_redirect');

    Log::info("*****************");
    Log::info("*** START APP ***");
    Log::info("*****************");
    Log::info('Route: Accesso alla home page/login', [
        'ip' => request()->ip(),
        'user_agent' => request()->userAgent()
    ]);
    return view('brewery');
})->name('login');

// AUTH/LOGIN
Route::post('/auth/login', [AuthController::class, 'login']);

// ROTTE PROTETTE DA MIDDLEWARE
Route::middleware(CheckTokenMiddleware::class)->group(function () {
    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // LOGOUT
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});
