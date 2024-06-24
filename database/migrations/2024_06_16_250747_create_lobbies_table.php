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

            $table->string('name',length:50);
            $table->foreignId('creator')->constrained('users')->onDelete('cascade');
            $table->foreignId('playerOne')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('playerTwo')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('gameType',length:35);
            $table->integer('turn')->default(1);
            $table->string('speed',length:45)->default("slow");
            $table->integer('foodX')->default(0);
            $table->integer('foodY')->default(0);
            $table->integer('start')->default(0);

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
