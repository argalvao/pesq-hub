<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Hash;

class Usuario extends Model
{
    use HasUuids;

    protected $table = 'usuario';
    
    const CREATED_AT = 'data_criacao';
    const UPDATED_AT = 'data_atualizacao';

    protected $fillable = [
        'nome',
        'email',
        'senha',
        'ativo',
        'tipo_permissao',
        'id_curso'
    ];

    protected $hidden = [
        'senha'
    ];

    protected $casts = [
        'id' => 'string',
        'id_curso' => 'string',
        'ativo' => 'boolean',
        'data_criacao' => 'datetime',
        'data_atualizacao' => 'datetime',
        'senha' => 'hashed'
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'id_curso');
    }


    public function isSuperAdmin()
    {
        return $this->tipo_permissao === 'SUPER';
    }

 
    public function isDepartamentoAcademico()
    {
        return $this->tipo_permissao === 'DA';
    }


    public function isBasico()
    {
        return $this->tipo_permissao === 'BASICO';
    }
}
