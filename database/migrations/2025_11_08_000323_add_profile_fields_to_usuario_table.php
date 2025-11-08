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
        Schema::table('usuario', function (Blueprint $table) {
            $table->string('telefone', 20)->nullable()->after('email');
            $table->integer('periodo')->nullable()->after('id_curso');
            $table->text('biografia')->nullable()->after('periodo');
            $table->string('lattes', 500)->nullable()->after('biografia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuario', function (Blueprint $table) {
            $table->dropColumn(['telefone', 'periodo', 'biografia', 'lattes']);
        });
    }
};
