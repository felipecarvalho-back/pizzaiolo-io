<?php

namespace App\Models;

use CodeIgniter\Model;

class PedidoItemAdicionalModel extends Model
{
    protected $table            = 'pedido_item_adicionais';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pedido_item_id',
        'adicional_id',
        'preco',
    ];

    public function getAdicionaisDoItem(int $pedidoItemId)
    {
        return $this->select('adicionais.nome, pedido_item_adicionais.preco')
            ->join('adicionais', 'adicionais.id = pedido_item_adicionais.adicional_id')
            ->where('pedido_item_adicionais.pedido_item_id', $pedidoItemId)
            ->findAll();
    }
}
