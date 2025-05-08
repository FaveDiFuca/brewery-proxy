<div>
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">Lista Birrerie</h2>
        </div>

        @if($queryInfo)
        <div class="card-body bg-light border-bottom">
            <div class="small text-muted mb-2">Debug informazioni:</div>

            <div class="mb-1">
                <strong>Query HTTP:</strong>
                <div class="mt-2 p-2 bg-dark text-light rounded font-monospace">
                    <div>{{ $queryInfo['method'] }} {{ $queryInfo['url'] }}</div>
                </div>
            </div>

            <div class="mb-1">
                <strong>Timestamp:</strong>
                @php
                    \Carbon\Carbon::setLocale('it');
                    $data = \Carbon\Carbon::parse($queryInfo['timestamp']);
                @endphp
                {{ $data->format('d') }} {{ $data->translatedFormat('F') }} {{ $data->format('Y H:i:s') }}
            </div>

            @if(isset($queryInfo['response_time_ms']))
            <div class="mb-1">
                <strong>Tempo di risposta:</strong>
                <span class="badge bg-info">{{ $queryInfo['response_time_ms'] }} ms</span>
            </div>
            @endif
        </div>
        @endif

        <div class="card-body">
            @if($isLoading)
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Caricamento...</span>
                    </div>
                </div>
            @elseif($errorMessage)
                <div class="alert alert-danger">{{ $errorMessage }}</div>
            @else
                @if(count($breweries) === 0)
                    <div class="alert alert-info">Nessuna birreria trovata</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Tipo</th>
                                    <th>Città</th>
                                    <th>Stato</th>
                                    <th>Sito Web</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($breweries as $brewery)
                                    <tr>
                                        <td>{{ $brewery['name'] }}</td>
                                        <td>{{ $brewery['brewery_type'] }}</td>
                                        <td>{{ $brewery['city'] }}</td>
                                        <td>{{ $brewery['country'] }}</td>
                                        <td>
                                            @if(isset($brewery['website_url']) && $brewery['website_url'])
                                                <a href="{{ $brewery['website_url'] }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    Visita
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between mt-3">
                        <button wire:click="previousPage" class="btn btn-outline-primary" {{ $currentPage <= 1 ? 'disabled' : '' }}>
                            &laquo; Precedente
                        </button>
                        <span>Pagina {{ $currentPage }}</span>
                        <button wire:click="nextPage" class="btn btn-outline-primary" {{ !$hasMore ? 'disabled' : '' }}>
                            Successiva &raquo;
                        </button>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
