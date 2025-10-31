<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class LinhaPesquisa extends Model
{
    use HasUuids;

    protected $table = 'linha_pesquisa';
    
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'descricao',
        'id_area_pesquisa',
        'criado_por'
    ];

    protected $casts = [
        'id' => 'string',
        'id_area_pesquisa' => 'string'
    ];

    // Relacionamentos
    
    /**
     * Uma linha de pesquisa pertence a uma área de pesquisa
     */
    public function areaPesquisa()
    {
        return $this->belongsTo(AreaPesquisa::class, 'id_area_pesquisa');
    }

    /**
     * Uma linha de pesquisa tem vários professores (many-to-many)
     */
    public function professores()
    {
        return $this->belongsToMany(
            Professor::class,
            'professor_has_linha_pesquisa',
            'id_linha_pesquisa',
            'id_professor'
        );
    }
}