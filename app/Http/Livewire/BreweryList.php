<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BreweryList extends Component
{
    public $isLoading = true;
    public $currentPage = 1;
    public $breweries = [];
    public $hasMore = true;
    public $errorMessage = null;
    public $queryInfo = null;
    public $searchTerm = '';

    protected $listeners = [
        'logout' => 'handleLogout'
    ];

    public function mount()
    {
        Log::info('BreweryList: Inizializzazione componente', [
            'page' => $this->currentPage ?? 1,
            'request_time' => now()->format('Y-m-d H:i:s.u')
        ]);

        $this->loadBreweries();
    }


    public function loadBreweries()
    {
        $requestId = uniqid('api-');
        Log::info('BreweryList: Caricamento birrerie', [
            'request_id' => $requestId,
            'page' => $this->currentPage
        ]);

        $this->isLoading = true;
        $this->errorMessage = null;
        $this->queryInfo = null;

        try {
            $apiUrl = 'https://api.openbrewerydb.org/v1/breweries';
            $queryParams = [
                'page' => $this->currentPage,
                'per_page' => config('services.brewery.per_page', 10)
            ];

            $fullUrl = $apiUrl . '?' . http_build_query($queryParams);

            Log::info('BreweryList: Registrazione endpoint e parametri', [
                'endpoint' => $apiUrl,
                'params' => $queryParams,
                'url_completo' => $fullUrl
            ]);

            $startTime = microtime(true);

            $response = Http::timeout(10)
                ->get($apiUrl, $queryParams);

            // VERFICA SE LA RISPOSTA È SUCCESSFUL
            if (!$response->successful()) {
                $statusCode = $response->status();
                $errorBody = null;

                try {
                    $errorBody = $response->json('error');
                } catch (\Exception $e) {
                    $errorBody = 'Risposta non valida';
                }

                // GESTIONE ERRORE API
                $this->errorMessage = 'Errore API: ' . $statusCode . ' - ' . ($errorBody ?? 'Errore sconosciuto');
                $this->isLoading = false;

                Log::error('BreweryList: Errore nella risposta API', [
                    'status' => $statusCode,
                    'body' => $response->body(),
                    'component_id' => $this->id
                ]);

                return;
            }

            // SE OK CALCOLA TEMPO DI RISPOSTA
            $responseTime = round((microtime(true) - $startTime) * 1000);

            $this->queryInfo = [
                'method' => 'GET',
                'url' => $fullUrl,
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'response_time_ms' => $responseTime
            ];

            Log::info('BreweryList: Informazioni sulla richiesta', $this->queryInfo);

            // SE OK MOSTRA LE BIRRERIE
            $data = $response->json();

            $this->breweries = $data;
            $this->hasMore = count($this->breweries) >= config('services.brewery.per_page', 10);
            $this->isLoading = false;

            Log::info('BreweryList: Dati ricevuti dall\'API', [
                'count' => count($this->breweries),
                'from' => 'API',
                'success' => true
            ]);
        } catch (\Exception $e) {
            $this->errorMessage = 'Errore durante il caricamento dei dati: ' . $e->getMessage();
            $this->isLoading = false;

            Log::error('BreweryList: Eccezione durante il caricamento', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }



    public function nextPage()
    {
        $requestId = uniqid('pagination-');

        Log::info("BreweryList: vai a pagina successiva", [
            'request_id' => $requestId,
            'da_pagina' => $this->currentPage,
            'nuova_pagina' => $this->currentPage + 1,
            'timestamp' => now()->format('Y-m-d H:i:s.u')
        ]);

        $this->currentPage++;

        Log::info("BreweryList: Pagina incrementata", [
            'request_id' => $requestId,
            'current_page' => $this->currentPage,
            'timestamp' => now()->format('Y-m-d H:i:s.u')
        ]);

        $this->loadBreweries();

        Log::info("BreweryList: arrivato a pagina successiva", [
            'request_id' => $requestId,
            'page' => $this->currentPage,
            'results_count' => count($this->breweries),
            'has_more' => $this->hasMore
        ]);
    }


    public function previousPage()
    {
        if ($this->currentPage > 1) {
            $requestId = uniqid('pagination-');

            Log::info("BreweryList: vai a pagina precedente", [
                'request_id' => $requestId,
                'da_pagina' => $this->currentPage,
                'nuova_pagina' => $this->currentPage - 1,
                'timestamp' => now()->format('Y-m-d H:i:s.u')
            ]);

            $this->currentPage--;

            Log::info("BreweryList: Pagina decrementata", [
                'request_id' => $requestId,
                'current_page' => $this->currentPage,
                'timestamp' => now()->format('Y-m-d H:i:s.u')
            ]);

            $this->loadBreweries();

            Log::info("BreweryList: arrivato a pagina precedente", [
                'request_id' => $requestId,
                'page' => $this->currentPage,
                'results_count' => count($this->breweries),
                'has_more' => $this->hasMore
            ]);
        } else {
            Log::warning("BreweryList: Tentativo di navigazione prima della pagina 1 bloccato - PULSANTE DISABILITATO", [
                'current_page' => $this->currentPage,
                'timestamp' => now()->format('Y-m-d H:i:s.u')
            ]);
        }
    }

    public function handleLogout()
    {
        Log::info("BreweryList: Gestione logout utente", [
            'timestamp' => now()->format('Y-m-d H:i:s.u')
        ]);

        $this->reset();
    }

    public function render()
    {
        Log::info("BreweryList: Rendering componente", [
            'page' => $this->currentPage,
            'has_results' => count($this->breweries) > 0,
            'loading' => $this->isLoading,
            'has_error' => !empty($this->errorMessage),
            'timestamp' => now()->format('Y-m-d H:i:s.u')
        ]);

        return view('livewire.brewery-list');
    }
}
