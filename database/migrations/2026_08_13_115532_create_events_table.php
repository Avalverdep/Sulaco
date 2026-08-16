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
    Schema::create('events', function (Blueprint $table) {
        $table->id();
        $table->string('kind');
        $table->foreignId('event_type_id')->nullable()->constrained();
        $table->foreignId('created_by')->constrained('users');
        $table->uuid('series_id')->nullable()->index();
        $table->string('title');
        $table->text('description')->nullable();
        $table->dateTime('starts_at');
        $table->dateTime('ends_at');
        $table->unsignedSmallInteger('capacity')->nullable();
        $table->string('status')->default('pendiente');
        $table->timestamps();

        $table->index(['starts_at', 'ends_at']);
    });

    DB::statement("ALTER TABLE events ADD CONSTRAINT events_kind_check CHECK (kind IN ('evento_tienda', 'reserva_usuario'))");
    DB::statement("ALTER TABLE events ADD CONSTRAINT events_status_check CHECK (status IN ('pendiente', 'aprobado', 'rechazado', 'cancelado'))");
    DB::statement("ALTER TABLE events ADD CONSTRAINT events_dates_check CHECK (ends_at > starts_at)");
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
