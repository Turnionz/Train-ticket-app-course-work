<?php

use App\Models\Train;
use App\Models\Wagon;
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
        Schema::create('wagons', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Train::class)->constrained();
            $table->integer('wagon_number')->nullable();
            $table->enum('type', Wagon::$type);
            $table->json('layout_map');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wagons');
    }
};
