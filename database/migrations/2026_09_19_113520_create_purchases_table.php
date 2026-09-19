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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('supplier');
            $table->string('invoice_ref')->nullable();
            $table->date('purchased_at');
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('vat_total', 10, 2)->default(0);
            $table->string('status')->default('pedido');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('purchased_at');
        });

        DB::statement("ALTER TABLE purchases ADD CONSTRAINT purchases_status_check CHECK (status IN ('pedido', 'recibido', 'cancelado'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
