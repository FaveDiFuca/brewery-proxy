#!/bin/bash
# Script di avvio Docker per Brewery-Proxy
# Max - 7/5/2025

echo "Brewery-Proxy - Preparazione ambiente..."

# Controllo se esiste già il file .env
if [ ! -f .env ]; then
    echo "File .env non trovato, creo da template..."
    cp .env.example .env
    # Generiamo la chiave dell'app
    php artisan key:generate
    # Generiamo il secret JWT
    php artisan jwt:secret
fi

# Controllo se esiste già il database
if [ ! -f database/database.sqlite ]; then
    echo "Database non trovato, lo creo..."
    touch database/database.sqlite
    chmod 666 database/database.sqlite

    # Eseguiamo le migrazioni
    php artisan migrate --force

    # Creiamo l'utente di default se non esiste
    php artisan db:seed --force
fi

# Pulizia cache (fix per i problemi di permessi)
php artisan cache:clear
php artisan config:clear

echo "Brewery-Proxy è pronto! Avvio server Apache..."
# Avvio Apache in foreground
apache2-foreground
