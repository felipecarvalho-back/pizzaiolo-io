<?php

namespace App\Models;

use CodeIgniter\Model;

class PedidoItemSaborModel extends Model
{
    protected $table            = 'pedido_item_sabores';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pedido_item_id',
        'produto_id',
    ];

    public function getSaboresDoItem(int $pedidoItemId)
    {
        return $this->select('produtos.id, produtos.nome, produtos.descricao')
            ->join('produtos', 'produtos.id = pedido_item_sabores.produto_id')
            ->where('pedido_item_sabores.pedido_item_id', $pedidoItemId)
            ->findAll();
    }
}
