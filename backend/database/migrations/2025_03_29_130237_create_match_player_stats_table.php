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
        Schema::create('match_player_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained()->onDelete('cascade');
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->integer('points')->default(0);
            $table->integer('assists')->default(0);
            $table->decimal('two_point_success', 5, 2)->default(0);
            $table->decimal('three_point_success', 5, 2)->default(0);
            $table->integer('two_point_attempts')->default(0);
            $table->integer('three_point_attempts')->default(0);
            $table->integer('rebounds')->default(0);
            $table->integer('fastbreaks')->default(0);


            $table->decimal('two_point_percentage', 5, 2)->default(0);
            $table->decimal('three_point_percentage', 5, 2)->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_player_stats');
    }
};
