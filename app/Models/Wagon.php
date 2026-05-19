<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wagon extends Model
{
    use HasFactory;

    protected $fillable = ['wagon_number', 'type', 'layout_map'];

    public static array $type = ['Сидячий', 'Купейний', 'Плацкартний', 'Люкс'];

    public static array $presets = [
        'Сидячий' =>
        [
            ["seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat"],
            ["seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat"],
            [null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null],
            [null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null],
            ["seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat"],
            ["seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat"],
        ],
        'Купейний' =>
        [
            ["seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat"],
            ["seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat"],
            [null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null],
            [null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null],
            ["seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat"],
        ],
        'Плацкартний' =>
        [
            ["seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat"],
            ["seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat"],
            [null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null],
        ],
        'Люкс' =>
        [
            ["seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat", "seat"],
            [null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null]
        ],
    ];


    public function train(): BelongsTo
    {
        return $this->belongsTo(Train::class);
    }

    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class);
    }
}
