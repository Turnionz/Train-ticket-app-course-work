<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    use HasFactory;

    public static array $type = ['Керувальник', 'Борт-Провідник', 'Машиніст', 'Механік'];

    public function crew(): BelongsTo
    {
        return $this->belongsTo(Crew::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
