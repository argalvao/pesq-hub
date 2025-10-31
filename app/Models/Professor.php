<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Professor extends Model
{
    use HasFactory;
    use HasUuids;

    protected $table = 'professor';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'nome',
        'email',
        'telefone',
        'id_curso',
        'departamento',
        'criado_por'
    ];

    protected $casts = [
        'id' => 'string',
        'id_curso' => 'string'
    ];

    // Relacionamentos

    /**
     * Um professor pertence a um curso
     */
    public function curso()
    {
        return $this->belongsTo(Curso::class, 'id_curso');
    }

    /**
     * Um professor tem várias linhas de pesquisa (many-to-many)
     */
    public function linhasPesquisa()
    {
        return $this->belongsToMany(
            LinhaPesquisa::class,
            'professor_has_linha_pesquisa',
            'id_professor',
            'id_linha_pesquisa'
        );
    }

    /**
     * Um professor tem várias áreas de interesse (many-to-many)
     */
    public function areasInteresse()
    {
        return $this->belongsToMany(
            AreaPesquisa::class,
            'professor_has_area_interesse',
            'id_professor',
            'area_pesquisa'
        );
    }
}
