<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Route extends Model
{
    use HasFactory;

    public function routeStops(): HasMany
    {
        return $this->hasMany(RouteStop::class);
    }

    public function departStation(): BelongsTo
    {
        return $this->belongsTo(Station::class, 'depart_station');
    }

    public function arrivalStation(): BelongsTo
    {
        return $this->belongsTo(Station::class, 'arrival_station');
    }
}
