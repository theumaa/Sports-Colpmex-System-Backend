<?php

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
        Schema::create('resource_trackings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->date('date');
            $table->time('from');
            $table->foreignId('sport_id')->constrained();
            $table->tinyInteger('court');
            $table->enum('status',['ongoing','completed','rejected','paid']);
            $table->morphs('trackable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_trackings');
    }
};
