<?php

use App\Models\Station;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('connected_stations', function (Blueprint $table) {
            $table->foreignIdFor(Station::class, 'station_a')->constrained('stations')->constrained('stations')->onDelete('cascade');
            $table->foreignIdFor(Station::class, 'station_b')->constrained('stations')->constrained('stations')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['station_a', 'station_b']);
        });

        DB::statement('ALTER TABLE connected_stations ADD CONSTRAINT check_station_order CHECK (station_a < station_b)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('connected_stations');
    }
};
