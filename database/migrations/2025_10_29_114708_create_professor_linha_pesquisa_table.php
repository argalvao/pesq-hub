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
        Schema::create('professor_linha_pesquisa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('professor_id');
            $table->unsignedBigInteger('linha_pesquisa_id');
            $table->timestamps();
            
            // Chaves estrangeiras
            $table->foreign('professor_id')->references('id')->on('professores')->onDelete('cascade');
            $table->foreign('linha_pesquisa_id')->references('id')->on('linhas_pesquisa')->onDelete('cascade');
            
            // Evitar duplicatas
            $table->unique(['professor_id', 'linha_pesquisa_id']);
            
            // Índices para performance
            $table->index('professor_id');
            $table->index('linha_pesquisa_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professor_linha_pesquisa');
    }
};
