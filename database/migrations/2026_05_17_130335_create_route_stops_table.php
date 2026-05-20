<?php

use App\Models\Route;
use App\Models\Station;
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
        Schema::create('route_stops', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Route::class)->constrained();
            $table->foreignIdFor(Station::class)->constrained()->constrained('stations')->onDelete('cascade');
            $table->unsignedInteger('order');
            $table->time('stop_time');

            // It should be noted that the user should set this value for certain speed for next calculations of travel time for each type of train
            $table->time('travel_time_to_next_station');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_stops');
    }
};
