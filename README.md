# AudioHeaven - Music Streaming Backend

**AudioHeaven** is a robust, high-performance music streaming backend built with Laravel 11. It provides a complete infrastructure for managing songs, albums, and playlists, featuring advanced audio streaming capabilities and automated metadata processing.

## 🚀 Key Features

-   **Advanced Audio Streaming:** Implements **HTTP 206 Partial Content** (Range Requests), allowing for seamless seeking and efficient bandwidth usage.
-   **Automated Media Analysis:** Uses the **getID3** library to automatically extract track duration and technical metadata upon upload.
-   **Smart Content Management:** -   Secure file uploads for songs and high-quality cover art.
    -   Protection logic to prevent the deletion of system-default assets.
    -   Cascading updates (e.g., updating an album cover automatically updates all associated song covers).
-   **Dynamic Queue System:** A position-based playback queue allowing users to reorder, add, and clear tracks dynamically.
-   **Secure Authentication:** Powered by **Laravel Sanctum**, featuring:
    -   Email verification and password reset flows.
    -   Profile picture management with default fallback.
-   **History Tracking:** Automatically logs user listening habits for "Recently Played" features.

## 🛠 Technology Stack

-   **Framework:** [Laravel 11](https://laravel.com/)
-   **Database:** MySQL / MariaDB
-   **Auth:** Laravel Sanctum (Token-based)
-   **Media Processing:** getID3
-   **Testing Suite:** [Pest PHP](https://pestphp.com/)
-   **Storage:** Laravel Storage (Public disk with symbolic linking)

## Used commands

php artisan install:api

php artisan migrate

php artisan make:model Song -mc --api
php artisan make:model Album -mc --api
php artisan make:model Playlist -mc --api
php artisan make:model Queue -mc --api

php artisan make:migration create_playlist_song_table
php artisan make:migration create_queue_song_table

php artisan migrate:refresh

php artisan migrate:fresh

php artisan storage:link

php artisan test --testsuite=Unit

php artisan migrate:fresh --seed

php artisan vendor:publish --tag=laravel-notifications

## php.ini:
upload_max_filesize = 100M
post_max_size = 120M
memory_limit = 256M

## Setup
(A .env-et a hozzá jogosultak megkapják)
A rendszergazdaként elindított Powershellben futtassuk a következő parancsot a Laravel keretrendszer letöltéséhez:

# Run as administrator...
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.4'))

Githubról klónozzuk le az AudioHeaven_backend repositoryt:
git clone https://github.com/CsongetoCsongor/AudioHeaven_backend.git

A projekt mappájában futtassuk a következő parancsot a függőségek letöltéséhez:
composer i

(Amennyiben ez a parancs, vagy bármely másik ne működne, a VS Code terminálja helyett nyissuk meg a fájlkezelőből, a projekt mappájából a cmd-t és oda írjuk be!)

Majd futtassuk a következő parancsot a szimbolikus link létrehozásához:
 php artisan storage:link
 
Annak érdekében hogy nagyobb fájlokat is tudjunk küldeni a backendnek a php.ini fájlunkat is módosítani kell (C:\Users\username\.config\herd-lite\bin\php.ini):
upload_max_filesize = 100M
post_max_size = 120M
memory_limit = 256M

Ezeket a sorokat kell a fájl végére illeszteni. A számok lehetnek nagyobbak is, de kisebb számok nem ajánlottak.
A backend futtatásához futtassuk a php artisan serve parancsot!
Backendünk fut a http://localhost:8000/ útvonalon.


