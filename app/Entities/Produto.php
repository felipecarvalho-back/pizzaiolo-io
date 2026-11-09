<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Produto extends Entity
{
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts = [
        'id'           => 'integer',
        'categoria_id' => 'integer',
        'preco_base'   => '?float',
        'ativo'        => 'boolean',
        'destaque'     => 'boolean',
    ];

    public function isPizza(): bool
    {
        return ($this->attributes['tipo'] ?? 'pizza') === 'pizza';
    }

    public function getPrecoBaseFormatado(): string
    {
        if (isset($this->attributes['preco_base']) && $this->attributes['preco_base'] !== null) {
            return 'R$ ' . number_format((float) $this->attributes['preco_base'], 2, ',', '.');
        }

        return '';
    }
}
