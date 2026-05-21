<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = ['status', 'seat_id', 'trip_id', 'user_id', 'departing_station', 'arriving_station'];

    public static array $status = ['reserved', 'booked', 'refunded', 'expired'];

    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function departingStation(): BelongsTo
    {
        return $this->belongsTo(Station::class, 'departing_station');
    }

    public function arrivalStation(): BelongsTo
    {
        return $this->belongsTo(Station::class, 'arriving_station');
    }
}
