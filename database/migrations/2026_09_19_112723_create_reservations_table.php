<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('quantity');
            $table->string('status')->default('activa');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('collected_at')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();

            $table->index(['status', 'expires_at']);
            $table->index(['user_id', 'status']);
        });

        DB::statement("ALTER TABLE reservations ADD CONSTRAINT reservations_status_check CHECK (status IN ('activa', 'recogida', 'cancelada', 'caducada'))");
        DB::statement("ALTER TABLE reservations ADD CONSTRAINT reservations_quantity_check CHECK (quantity > 0)");
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};