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
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            // Dueño de la lista de equipo.
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // Usuario agregado como compañero.
            $table->foreignId('member_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            // Evita duplicar el mismo compañero en la misma lista.
            $table->unique(['user_id', 'member_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
