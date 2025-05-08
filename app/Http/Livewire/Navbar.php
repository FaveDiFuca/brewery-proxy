<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class Navbar extends Component
{
    public $token;
    public $userName;

    public function mount()
    {
        Log::info("Navbar: Inizializzazione componente");

        $this->token = session('token');

        Log::info("Navbar: Token recuperato dalla sessione", [
            'token_exists' => !empty($this->token)
        ]);

        // SE TOKEN OK PESCA NOME UTENTE
        if ($this->token) {
            Log::info("Navbar: Token presente, recupero info utente");

            // IMPOSTA TOKEN PER JWTAuth
            JWTAuth::setToken($this->token);

            try {
                $user = JWTAuth::authenticate();
                $this->userName = $user ? $user->name : 'Utente';

                Log::info("Navbar: Info utente recuperate", [
                    'username' => $this->userName
                ]);
            } catch (\Exception $e) {
                Log::error("Navbar: Errore recupero info utente", [
                    'error' => $e->getMessage()
                ]);
                $this->userName = 'Utente';
            }
        } else {
            Log::info("Navbar: Nessun token presente");
        }
    }

    public function render()
    {
        Log::info("Navbar: Rendering componente", [
            'has_token' => !empty($this->token),
            'username' => $this->userName ?? 'Nessuno'
        ]);

        return view('livewire.navbar');
    }
}
