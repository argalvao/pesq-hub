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
        Schema::create('usuario_area_interesse', function (Blueprint $table) {
            $table->uuid('id_usuario');
            $table->uuid('id_area_pesquisa');
            $table->timestamps();

            // Definir chaves estrangeiras
            $table->foreign('id_usuario')->references('id')->on('usuario')->onDelete('cascade');
            $table->foreign('id_area_pesquisa')->references('id')->on('area_pesquisa')->onDelete('cascade');
            
            // Chave primária composta
            $table->primary(['id_usuario', 'id_area_pesquisa']);
            
            // Índices
            $table->index('id_usuario');
            $table->index('id_area_pesquisa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario_area_interesse');
    }
};
