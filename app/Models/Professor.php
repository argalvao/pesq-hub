<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    use HasFactory;

    protected $table = 'professores';

    protected $fillable = [
        'user_id',
        'nome',
        'email',
        'telefone',
        'curso',
        'areas_interesse'
    ];

    /**
     * Relacionamento com User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento many-to-many com LinhaPesquisa
     */
    public function linhasPesquisa()
    {
        return $this->belongsToMany(LinhaPesquisa::class, 'professor_linha_pesquisa');
    }

    /**
     * Accessor para áreas de interesse como array
     */
    public function getAreasInteresseArrayAttribute()
    {
        return $this->areas_interesse ? explode(',', $this->areas_interesse) : [];
    }

    /**
     * Mutator para áreas de interesse
     */
    public function setAreasInteresseAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['areas_interesse'] = implode(',', $value);
        } else {
            $this->attributes['areas_interesse'] = $value;
        }
    }
}
