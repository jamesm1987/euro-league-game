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
        Schema::create('fixtures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('home_team_id');
            $table->foreign('home_team_id')->references('id')->on('teams');
            $table->unsignedBigInteger('away_team_id');
            $table->foreign('away_team_id')->references('id')->on('teams');
            $table->timestamp('kickoff_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixtures');
    }
};
