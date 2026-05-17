<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Seat extends Model
{
    use HasFactory;

    public static array $class = ['1-й клас', '2-й клас', '3-й клас', 'Спляче', 'Люкс'];

    public function wagon(): BelongsTo
    {
        return $this->belongsTo(Wagon::class);
    }
}
