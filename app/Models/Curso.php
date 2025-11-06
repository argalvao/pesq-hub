<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Curso extends Model
{
    use HasUuids;

    protected $table = 'curso';
    
    public $timestamps = false;

    protected $fillable = [
        'nome'
    ];

    protected $casts = [
        'id' => 'string'
    ];

  
    public function professores()
    {
        return $this->hasMany(Professor::class, 'id_curso');
    }

 
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_curso');
    }
}
