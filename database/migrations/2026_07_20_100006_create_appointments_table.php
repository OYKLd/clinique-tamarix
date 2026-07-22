<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_code', 20)->unique()->comment('Code de suivi patient, ex. TMX-2607-0451');
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
            $table->foreignId('specialty_id')->constrained()->restrictOnDelete();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('status', 20)->default('pending')->index();
            $table->string('reason')->nullable()->comment('Motif de consultation (facultatif)');
            $table->boolean('is_new_patient')->default(true);
            $table->string('source', 20)->default('online')->comment('online = site web, reception = accueil');
            $table->string('cancelled_by', 20)->nullable()->comment('patient ou clinic');
            $table->string('cancellation_reason')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['doctor_id', 'date', 'start_time']);
            $table->index(['date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
