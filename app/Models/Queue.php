<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    public function user() { return $this->belongsTo(User::class); }
    public function songs() { return $this->belongsToMany(Song::class)->withPivot('position', 'added_at'); }
}
