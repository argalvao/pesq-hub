<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinhaPesquisa extends Model
{
    use HasFactory;

    protected $table = 'linhas_pesquisa';

    protected $fillable = [
        'nome',
        'descricao'
    ];

    /**
     * Relacionamento many-to-many com Professor
     */
    public function professores()
    {
        return $this->belongsToMany(Professor::class, 'professor_linha_pesquisa');
    }
}
