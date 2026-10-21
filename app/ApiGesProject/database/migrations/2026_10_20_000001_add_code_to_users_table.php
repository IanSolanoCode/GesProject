<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * La tabla `users` ya existe por la migración por defecto de Laravel,
     * por eso el código de invitación se agrega con una migración aparte.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('code', 10)->nullable();
            });
        }

        // Los usuarios existentes deben tener código antes de crear el índice único.
        $this->backfillMissingCodes();

        if (! Schema::hasIndex('users', ['code'], 'unique')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('code');
            });
        }

        // El código es obligatorio: es el identificador para agregar a un equipo.
        Schema::table('users', function (Blueprint $table) {
            $table->string('code', 10)->nullable(false)->default('')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasIndex('users', ['code'], 'unique')) {
                $table->dropUnique(['code']);
            }

            if (Schema::hasColumn('users', 'code')) {
                $table->dropColumn('code');
            }
        });
    }

    /**
     * Asigna un código único de 10 caracteres a los usuarios que no lo tienen.
     */
    protected function backfillMissingCodes(): void
    {
        DB::table('users')
            ->select('id')
            ->where(fn ($query) => $query->whereNull('code')->orWhere('code', ''))
            ->orderBy('id')
            ->chunkById(100, function ($users) {
                foreach ($users as $user) {
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['code' => $this->generateUniqueCode()]);
                }
            });
    }

    /**
     * Genera un código único de 10 caracteres (A-Z y 0-9).
     */
    protected function generateUniqueCode(): string
    {
        do {
            $code = Str::upper(Str::random(10));
        } while (DB::table('users')->where('code', $code)->exists());

        return $code;
    }
};
