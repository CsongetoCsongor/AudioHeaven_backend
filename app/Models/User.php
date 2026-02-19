<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'profile_picture',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

//     protected function profilePicture(): Attribute
// {
//         return Attribute::make(
//         get: function ($value) {
//             // Ha nincs érték, az alapértelmezettet adjuk
//             if (!$value) {
//                 return asset('storage/defaults/default_profile_picture.png');
//             }

//             // Kitisztítjuk a hibás "app/public/" részt a stringből
//             // Így a "app/public/defaults/kép.png"-ből "defaults/kép.png" lesz
//             $cleanPath = str_replace('app/public/', '', $value);

//             // Az asset() függvény csinál belőle teljes URL-t:
//             // http://localhost:8000/storage/defaults/default_profile_picture.png
//             return asset('storage/' . $cleanPath);
//         },
//     );
// }

    public function songs() { return $this->hasMany(Song::class); }
    public function albums() { return $this->hasMany(Album::class); }
    public function playlists() { return $this->hasMany(Playlist::class); }
    public function queueItems() { return $this->hasMany(QueueItem::class); }
    public function listeningHistoryItems() { return $this->hasMany(ListeningHistoryItem::class); }
}
