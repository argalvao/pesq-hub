<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AreaPesquisa extends Model
{
    use HasUuids;

    protected $table = 'area_pesquisa';
    
    // Desabilitar timestamps automáticos (created_at, updated_at)
    public $timestamps = false;

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'nome',
        'descricao',
        'criado_por'
    ];

    // Cast de tipos
    protected $casts = [
        'id' => 'string'
    ];

    // Relacionamentos
    
    /**
     * Uma área de pesquisa tem várias linhas de pesquisa
     */
    public function linhasPesquisa()
    {
        return $this->hasMany(LinhaPesquisa::class, 'id_area_pesquisa');
    }

    /**
     * Uma área de pesquisa pode ter vários professores interessados
     */
    public function professores()
    {
        return $this->belongsToMany(
            Professor::class,
            'professor_has_area_interesse',
            'area_pesquisa',
            'id_professor'
        );
    }
}