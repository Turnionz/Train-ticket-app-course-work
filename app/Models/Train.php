<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Train extends Model
{
    use HasFactory;

    protected $fillable = ['train_number', 'type'];

    public static array $type = ['Інтерсіті', 'Пасажирський', 'Регіональний', 'Нічний'];

    public function wagons(): HasMany
    {
        return $this->hasMany(Wagon::class);
    }

    public function trip(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    public function seats()
    {
        return $this->hasManyThrough(Seat::class, Wagon::class);
    }
}
