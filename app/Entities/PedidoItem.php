<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class PedidoItem extends Entity
{
    protected $dates = ['created_at'];
    protected $casts = [
        'id'             => 'integer',
        'pedido_id'      => 'integer',
        'produto_id'     => '?integer',
        'tamanho_id'     => '?integer',
        'borda_id'       => '?integer',
        'quantidade'     => 'integer',
        'preco_unitario' => 'float',
        'subtotal'       => 'float',
    ];

    public function getSubtotalFormatado(): string
    {
        return 'R$ ' . number_format((float) ($this->attributes['subtotal'] ?? 0), 2, ',', '.');
    }
}
