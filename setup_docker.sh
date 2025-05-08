#!/bin/bash

# Assicurati di essere nel container e di avere accesso alla shell

echo "Avvio del processo di configurazione nel container..."

# 1. Esegui composer install per installare le dipendenze
echo "Eseguendo composer install..."
composer install

# 2. Esegui artisan key:generate per generare la chiave dell'app
echo "Generando la chiave dell'app..."
php artisan key:generate

# 3. Esegui artisan jwt:secret per generare la chiave JWT (se necessario)
echo "Generando la chiave JWT..."
php artisan jwt:secret

# 4. Avvia tinker per creare un utente nel database
echo "Avviando Tinker per creare un utente..."
php artisan tinker <<EOF
App\Models\User::create([
    'name' => 'root',
    'email' => 'utente@esempio.com',
    'password' => bcrypt('password')
]);
EOF

# 5. Avvia il server Laravel sulla porta 80
echo "Avviando il server Laravel..."
php artisan serve --host=0.0.0.0 --port=80
