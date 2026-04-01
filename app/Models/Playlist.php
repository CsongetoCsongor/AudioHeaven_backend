<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Playlist extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'user_id'];

    public function user() { return $this->belongsTo(User::class); }
    public function songs() { return $this->belongsToMany(Song::class)->withTimestamps(); }
    public function listeningHistoryItems() { return $this->hasMany(ListeningHistoryItem::class); }
}
