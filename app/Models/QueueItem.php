<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class QueueItem extends Model
{
    public function user() { return $this->belongsTo(User::class); }
    public function song() { return $this->belongsTo(Song::class); }
}
