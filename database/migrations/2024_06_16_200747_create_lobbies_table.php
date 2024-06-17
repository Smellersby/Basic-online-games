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
        Schema::create('lobbies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('playerOne')->constrained()->onDelete('cascade');
            $table->foreignId('playerTwo')->constrained()->onDelete('cascade');
            $table->string('gameType',length:35);
            $table->string('turn',length:35);
            $table->string('speed',length:45);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lobbies');
    }
};
