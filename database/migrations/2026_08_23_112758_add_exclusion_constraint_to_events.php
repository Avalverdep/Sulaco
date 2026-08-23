<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS btree_gist');

        DB::statement("
            ALTER TABLE events
            ADD CONSTRAINT events_sin_solapamiento
            EXCLUDE USING gist (
                tsrange(starts_at, ends_at) WITH &&
            )
            WHERE (status IN ('pendiente', 'aprobado'))
        ");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE events DROP CONSTRAINT IF EXISTS events_sin_solapamiento');
    }
};