<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Exemplo da estrutura esperada
class Task extends Model
{
    protected $fillable = [
        'nome', 'descricao', 'finalizado', 'data_limite'
    ];

    protected $casts = [
        'finalizado' => 'boolean',
        'data_limite' => 'date:Y-m-d', // Serializa como YYYY-MM-DD
    ];
}
