<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class);
    }

    public function passenger(): BelongsTo
    {
        return $this->belongsTo(Passenger::class);
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function departingStation(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function arrivalStation(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }
}
