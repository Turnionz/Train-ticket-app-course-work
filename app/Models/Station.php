<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Station extends Model
{
    use HasFactory;

    protected $fillable = ['address', 'capacity'];

    public function routeStops(): HasMany
    {
        return $this->hasMany(RouteStop::class);
    }

    public function departingRoutes(): HasMany
    {
        return $this->hasMany(Route::class, 'depart_station');
    }

    public function arrivingRoutes(): HasMany
    {
        return $this->hasMany(Route::class, 'arrival_station');
    }

    public function connectionsAsA(): HasMany
    {
        return $this->hasMany(ConnectedStations::class, 'station_a');
    }

    public function connectionsAsB(): HasMany
    {
        return $this->hasMany(ConnectedStations::class, 'station_b');
    }

    public function getAllConnectedStationsAttribute()
    {
        $asA = $this->connectionsAsA->map->stationB;
        $asB = $this->connectionsAsB->map->stationA;

        return $asA->merge($asB)->unique('id');
    }
}
