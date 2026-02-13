<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Song extends Model
{

    protected $fillable = [
    'title',
    'plays',
    'stored_at',
    'cover',
    'user_id',
    'album_id',
];

    public function user() { return $this->belongsTo(User::class); }
    public function album() { return $this->belongsTo(Album::class); }
    public function playlists() { return $this->belongsToMany(Playlist::class)->withTimestamps(); }
    public function queueItems() { return $this->hasMany(QueueItem::class); }
    public function listeningHistoryItems() { return $this->hasMany(ListeningHistoryItem::class); }
}
