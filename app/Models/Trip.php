<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

class Trip extends Model
{
    use HasFactory;

    public function train(): BelongsTo
    {
        return $this->belongsTo(Train::class);
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * 
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'depart_time' => 'datetime:Y-m-d-H-i-s',
            'arrival_time' => 'datetime:Y-m-d-H-i-s',
        ];
    }
}
