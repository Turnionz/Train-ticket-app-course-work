<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConnectedStations extends Model
{
    use HasFactory;

    protected $fillable = ['station_a', 'station_b'];

    public $incrementing = false;

    protected $primaryKey = null;

    public function stationA(): BelongsTo
    {
        return $this->belongsTo(Station::class, 'station_a');
    }

    public function stationB(): BelongsTo
    {
        return $this->belongsTo(Station::class, 'station_b');
    }
}
