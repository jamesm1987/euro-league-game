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
        Schema::table('api_requests', function (Blueprint $table) {
            $table->index(['league_id', 'request_type', 'deleted_at'], 'idx_league_id_request_type_deleted_at');        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table->dropIndex('idx_league_id_request_type_deleted_at');
    }
};
