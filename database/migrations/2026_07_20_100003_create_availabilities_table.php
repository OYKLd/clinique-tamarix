<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('weekday')->comment('1 = lundi … 7 = dimanche (ISO-8601)');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('slot_duration')->default(30)->comment('Durée d\'un créneau en minutes');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['doctor_id', 'weekday']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('availabilities');
    }
};
