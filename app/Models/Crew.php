<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Crew extends Model
{
    use HasFactory;

    public function assignment(): HasOne
    {
        return $this->hasOne(Assignment::class);
    }
}
