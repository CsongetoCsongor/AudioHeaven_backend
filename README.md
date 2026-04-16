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

php.ini:
upload_max_filesize = 100M
post_max_size = 120M
memory_limit = 256M

php artisan migrate:fresh --seed

php artisan vendor:publish --tag=laravel-notifications
