<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AreaPesquisa extends Model
{
    use HasUuids;

    protected $table = 'area_pesquisa';
    
    public $timestamps = false;
    protected $fillable = [
        'nome',
        'descricao',
        'criado_por'
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function linhasPesquisa()
    {
        return $this->hasMany(LinhaPesquisa::class, 'id_area_pesquisa');
    }

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
