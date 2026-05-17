<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConnectedStations extends Model
{
    use HasFactory;

    public function stationA(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function stationB(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }
}
