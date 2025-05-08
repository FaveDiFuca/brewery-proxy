<div>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <span class="navbar-brand">Test PHP Laravel</span>

            <div class="ms-auto d-flex align-items-center">
                @if($token)
                    <span class="text-light me-3">
                        <i class="bi bi-person-check"></i>
                        Benvenuto, <strong>{{ $userName }}</strong>
                    </span>
                    <a href="{{ route('logout') }}" class="btn btn-sm btn-light">Logout</a>
                @endif
            </div>
        </div>
    </nav>

    @if($token)
        <div class="container alert alert-info mb-3">
            <strong>Token (solo per test):</strong>
            <button class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToClipboard('{{ $token }}')">Copia</button>
        </div>

        <script>
            function copyToClipboard(text) {
                navigator.clipboard.writeText(text).then(() => {
                    alert('Token copiato negli appunti!');
                });
            }
        </script>
    @endif
</div>
