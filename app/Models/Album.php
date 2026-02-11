<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Album extends Model
{
    public function user() { return $this->belongsTo(User::class); }
    public function songs() { return $this->hasMany(Song::class); }
}
