<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wagon extends Model
{
    use HasFactory;

    protected $fillable = ['train_id', 'wagon_number', 'type', 'layout_map'];

    public static array $type = ['Сидячий', 'Купейний', 'Плацкартний', 'Люкс'];

    public static function getPresets(): array
    {
        $path = database_path('data/wagon_presets.json');

        if (!file_exists($path)) {
            throw new \Exception("JSON file not found at: " . $path);
        }

        $jsonString = file_get_contents($path);
        return json_decode($jsonString, true);
    }

    public function train(): BelongsTo
    {
        return $this->belongsTo(Train::class);
    }

    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class);
    }


    protected $casts = [
        'layout_map' => 'array',
    ];
}
