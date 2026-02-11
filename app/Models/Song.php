<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    public function user() { return $this->belongsTo(User::class); }
    public function album() { return $this->belongsTo(Album::class); }
    public function playlists() { return $this->belongsToMany(Playlist::class); }
    public function queues() { return $this->belongsToMany(Queue::class)->withPivot('position', 'added_at'); }
}
