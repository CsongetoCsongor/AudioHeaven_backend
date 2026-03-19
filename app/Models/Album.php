<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Album extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'album_cover', 'user_id'];

    public function user() { return $this->belongsTo(User::class); }
    public function songs() { return $this->hasMany(Song::class); }
    public function listeningHistoryItems() { return $this->hasMany(ListeningHistoryItem::class); }
}
