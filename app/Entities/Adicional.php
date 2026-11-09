<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Adicional extends Entity
{
    protected $dates = ['created_at', 'updated_at'];
    protected $casts = [
        'id'    => 'integer',
        'preco' => 'float',
        'ativo' => 'boolean',
    ];

    public function getPrecoFormatado(): string
    {
        return 'R$ ' . number_format((float) ($this->attributes['preco'] ?? 0), 2, ',', '.');
    }
}
