<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = ['class'];

    public static array $class = ['1-й клас', '2-й клас', '3-й клас', 'Спляче', 'Люкс'];

    public function wagon(): BelongsTo
    {
        return $this->belongsTo(Wagon::class);
    }

    public function ticket(): HasOne
    {
        return $this->hasOne(Ticket::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
