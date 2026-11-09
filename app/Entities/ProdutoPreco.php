<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class ProdutoPreco extends Entity
{
    protected $dates = ['created_at', 'updated_at'];
    protected $casts = [
        'id'         => 'integer',
        'produto_id' => 'integer',
        'tamanho_id' => 'integer',
        'preco'      => 'float',
    ];

    public function getPrecoFormatado(): string
    {
        return 'R$ ' . number_format((float) ($this->attributes['preco'] ?? 0), 2, ',', '.');
    }
}
