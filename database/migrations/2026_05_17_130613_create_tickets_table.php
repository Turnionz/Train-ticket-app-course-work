<?php

use App\Models\Passenger;
use App\Models\Seat;
use App\Models\Station;
use App\Models\Trip;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Seat::class)->constrained();
            $table->foreignIdFor(Passenger::class)->constrained();
            $table->foreignIdFor(Trip::class)->constrained();
            $table->foreignIdFor(Station::class, 'departing_station')->constrained();
            $table->foreignIdFor(Station::class, 'arriving_station')->constrained();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
