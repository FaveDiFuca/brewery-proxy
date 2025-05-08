<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckTokenMiddleware
{
    public function handle($request, Closure $next)
    {
        $requestId = uniqid('req-');

        Log::info("CheckTokenMiddleware: Verifica token", [
            'request_id' => $requestId,
            'path' => $request->path(),
            'method' => $request->method(),
            'has_token' => session()->has('token')
        ]);

        // VERIFICA TOKEN IN SESSIONE
        if (!session()->has('token')) {
            Log::warning("CheckTokenMiddleware: Token mancante, reindirizzamento al login", [
                'request_id' => $requestId
            ]);

            return redirect()->route('login')->with('error', 'Sessione scaduta. Effettua nuovamente il login.');
        }

        try {
            // PRENDE TOKEN DA SESSIONE
            $token = session('token');

            // STAMPA PER VERIFICA IN LOG TOKEN 1234 + BLINDED + 4321
            Log::info("CheckTokenMiddleware: *** TOKEN *** >>>", [
                'token_preview' => $this->sanitizeTokenForLog($token)
            ]);

            // AUTENTICA TRAMITE JWT
            JWTAuth::setToken($token);
            $user = JWTAuth::authenticate();

            // VERIFICA UTENTE E TOKEN
            if (!$user) {
                throw new \Exception('Token non valido o utente non trovato');
            }

            Log::info("CheckTokenMiddleware: Token valido, continuo", [
                'request_id' => $requestId,
                'user_id' => $user->id,
                'username' => $user->name
            ]);

            return $next($request);
        } catch (\Exception $e) {
            Log::error("CheckTokenMiddleware: Eccezione durante verifica token", [
                'request_id' => $requestId,
                'error' => $e->getMessage(),
                'token_existed' => !empty($token)
            ]);

            // SE TOKEN NON VALIDO O SCADUTO LO RIMUOVO
            session()->forget('token');

            return redirect()->route('login')->with('error', 'Sessione scaduta o non valida. Effettua nuovamente il login.');
        }
    }

    // FUNZIONE PER SANIFICARE IL TOKEN NEI LOG - MOSTRA SOLO I PRIMI E GLI ULTIMI 4 CARATTERI
    private function sanitizeTokenForLog($token)
    {
        if (empty($token)) {
            return 'empty';
        }

        $length = strlen($token);
        if ($length <= 10) {
            return substr($token, 0, 2) . '...[nascosto]';
        }
        return substr($token, 0, 4) . '...[' . ($length - 8) . ' caratteri]...' . substr($token, -4);
    }
}
