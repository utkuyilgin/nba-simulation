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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team1_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('team2_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('fixture_id')->constrained('fixtures')->onDelete('cascade');
            $table->timestamp('start_time');
            $table->string('status'); // Ongoing, Completed, etc.
            $table->integer('score_team1')->default(0);
            $table->integer('score_team2')->default(0);
            $table->tinyInteger('week')->nullable();
            $table->tinyInteger('completed')->default(0);
            $table->integer('current_minute')->default(0);
            $table->integer('attack_count_team1')->default(0);
            $table->integer('attack_count_team2')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
