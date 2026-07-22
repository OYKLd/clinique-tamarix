<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('channel', 20)->comment('whatsapp, sms, mail');
            $table->string('template', 60)->comment('Nom du modèle de message, ex. rdv_recu, rdv_confirme, rappel_j1');
            $table->string('recipient', 60)->comment('Numéro ou e-mail du destinataire');
            $table->text('content')->nullable();
            $table->string('status', 20)->default('queued')->index();
            $table->string('provider_message_id')->nullable()->comment('Identifiant Meta/WhatsApp du message');
            $table->text('error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
