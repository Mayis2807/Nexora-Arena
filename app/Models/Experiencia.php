<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experiencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'valoracion_web',
        'secciones_visitadas',
        'eventos_interes',
        'como_nos_encontraste',
        'recomendaria',
        'mejoras',
        'comentario',
        'volveria_comprar',
    ];

    protected $casts = [
        'secciones_visitadas' => 'array',
        'eventos_interes' => 'array',
        'mejoras' => 'array',
        'recomendaria' => 'boolean',
        'volveria_comprar' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}