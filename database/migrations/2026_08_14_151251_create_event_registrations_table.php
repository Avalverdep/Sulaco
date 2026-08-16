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
    // create_event_registrations_table.php
    public function up(): void{
    Schema::create('event_registrations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('event_id')->constrained()->cascadeOnDelete();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('status')->default('confirmada');
        $table->timestamps();

        $table->unique(['event_id', 'user_id']);
    });

    DB::statement("ALTER TABLE event_registrations ADD CONSTRAINT event_registrations_status_check CHECK (status IN ('confirmada', 'cancelada', 'expulsada'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};
