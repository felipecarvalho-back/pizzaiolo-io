<?php

namespace App\Models;

use App\Entities\PedidoItem;
use CodeIgniter\Model;

class PedidoItemModel extends Model
{
    protected $table            = 'pedido_itens';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = PedidoItem::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pedido_id',
        'produto_id',
        'tamanho_id',
        'borda_id',
        'quantidade',
        'preco_unitario',
        'subtotal',
        'observacao',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';

    // Validation
    protected $validationRules = [
        'pedido_id'      => 'required|is_natural_no_zero',
        'quantidade'     => 'required|is_natural_no_zero',
        'preco_unitario' => 'required|decimal',
        'subtotal'       => 'required|decimal',
    ];
}
