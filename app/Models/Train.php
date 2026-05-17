<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Train extends Model
{
    use HasFactory;

    public static array $type = ['Інтерсіті', 'Пасажирський', 'Регіональний', 'Нічний'];

    public function wagons(): HasMany
    {
        return $this->hasMany(Wagon::class);
    }
}
