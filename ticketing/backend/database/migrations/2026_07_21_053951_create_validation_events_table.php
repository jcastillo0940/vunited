<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('validation_events', function (Blueprint $table) {
            $table->id();
            // Nullable: un QR invalido/no encontrado no tiene ticket real que referenciar.
            $table->foreignId('ticket_id')->nullable()->constrained()->nullOnDelete();
            $table->string('scanned_token', 128)->nullable();
            $table->enum('result', [
                'valid', 'already_used', 'revoked', 'wrong_event', 'wrong_door', 'invalid', 'not_found',
            ]);
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('door_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('operator_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('device_id')->nullable()->constrained()->nullOnDelete();
            $table->string('correlation_id')->nullable();
            $table->dateTime('occurred_at');
            $table->timestamps();

            $table->index(['ticket_id', 'result']);
            $table->index('occurred_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validation_events');
    }
};
