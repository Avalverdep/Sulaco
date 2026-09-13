<?php

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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->string('status')->default('pedido');
            $table->unsignedSmallInteger('max_per_user')->default(2);
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
        DB::statement("ALTER TABLE products ADD CONSTRAINT products_status_check CHECK (status IN ('pedido', 'disponible', 'descatalogado'))");
        DB::statement("ALTER TABLE products ADD CONSTRAINT products_price_check CHECK (price >= 0)");
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
