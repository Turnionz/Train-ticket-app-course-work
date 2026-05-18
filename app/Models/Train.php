<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Train extends Model
{
    use HasFactory;

    public static array $type = ['Інтерсіті', 'Пасажирський', 'Регіональний', 'Нічний'];

    public function wagons(): HasMany
    {
        return $this->hasMany(Wagon::class);
    }

    public function trip(): HasOne
    {
        return $this->hasOne(Trip::class);
    }
}
