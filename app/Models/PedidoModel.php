<?php

namespace App\Models;

use App\Entities\Pedido;
use CodeIgniter\Model;

class PedidoModel extends Model
{
    protected $table            = 'pedidos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Pedido::class;
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'codigo',
        'cliente_nome',
        'cliente_telefone',
        'telegram_chat_id',
        'tipo_entrega',
        'endereco',
        'forma_pagamento',
        'troco_para',
        'status',
        'status_pagamento',
        'total_produtos',
        'taxa_entrega',
        'total_geral',
        'observacoes',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'codigo'           => 'required|is_unique[pedidos.codigo,id,{id}]',
        'cliente_nome'     => 'required|min_length[2]|max_length[100]',
        'cliente_telefone' => 'required',
        'tipo_entrega'     => 'required|in_list[delivery,retirada,mesa]',
        'forma_pagamento'  => 'required|in_list[pix,cartao_credito,cartao_debito,dinheiro]',
        'status'           => 'required|in_list[pendente,confirmado,em_preparo,saiu_entrega,entregue,cancelado]',
        'status_pagamento' => 'required|in_list[aguardando,pago,rejeitado,na_entrega]',
    ];

    /**
     * Gera um código legível único para o pedido (ex: PED-9821)
     */
    public function gerarCodigoUnico(): string
    {
        do {
            $codigo = 'PED-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
        } while ($this->where('codigo', $codigo)->first() !== null);

        return $codigo;
    }

    /**
     * Localiza pedido pelo código amigável
     */
    public function findByCodigo(string $codigo): ?Pedido
    {
        return $this->where('codigo', $codigo)->first();
    }
}
