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
        Schema::create('professores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('nome');
            $table->string('email')->unique();
            $table->string('telefone')->nullable();
            $table->string('curso');
            $table->text('areas_interesse')->nullable();
            $table->timestamps();
            
            // Chave estrangeira
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Índices para performance
            $table->index('user_id');
            $table->index('email');
            $table->index('nome');
            $table->index('curso');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professores');
    }
};
