<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListeningHistory extends Model
{
    protected $fillable = ['user_id', 'song_id', 'album_id', 'playlist_id'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function song(): BelongsTo { return $this->belongsTo(Song::class); }
    public function album(): BelongsTo { return $this->belongsTo(Album::class); }
    public function playlist(): BelongsTo { return $this->belongsTo(Playlist::class); }
}
