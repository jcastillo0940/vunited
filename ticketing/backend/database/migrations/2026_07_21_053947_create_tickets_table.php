<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->ulid('public_id')->unique();
            $table->foreignId('order_id')->constrained()->restrictOnDelete();
            $table->foreignId('order_item_id')->constrained()->restrictOnDelete();
            $table->foreignId('event_id')->constrained()->restrictOnDelete();
            $table->foreignId('zone_id')->constrained()->restrictOnDelete();
            $table->foreignId('seat_id')->nullable()->constrained()->restrictOnDelete();
            $table->enum('status', ['issued', 'used', 'revoked'])->default('issued');
            // Token opaco firmado (HMAC) que va dentro del QR. Nunca contiene
            // nombre/correo/telefono/precio - ver Domain/Ticketing/Support/TicketQrSigner.
            $table->string('qr_token', 128)->unique();
            $table->dateTime('issued_at');
            $table->dateTime('used_at')->nullable();
            $table->dateTime('revoked_at')->nullable();
            $table->string('revoked_reason')->nullable();
            $table->foreignId('reissue_of_ticket_id')->nullable()->constrained('tickets')->nullOnDelete();
            $table->timestamps();

            // No emitir dos tickets para el mismo asiento y evento: en MySQL/
            // MariaDB, NULL no colisiona consigo mismo en una UNIQUE key, asi
            // que esto solo aplica cuando seat_id no es null (zonas seated) -
            // exactamente el comportamiento que necesitamos.
            $table->unique(['event_id', 'seat_id']);
            $table->index(['order_id']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
