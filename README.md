php artisan install:api

php artisan migrate

php artisan make:model Song -mc --api
php artisan make:model Album -mc --api
php artisan make:model Playlist -mc --api
php artisan make:model Queue -mc --api

php artisan make:migration create_playlist_song_table
php artisan make:migration create_queue_song_table

php artisan migrate:refresh