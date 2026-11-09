<?php

namespace App\Models;

use App\Entities\ProdutoPreco;
use CodeIgniter\Model;

class ProdutoPrecoModel extends Model
{
    protected $table            = 'produto_precos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = ProdutoPreco::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'produto_id',
        'tamanho_id',
        'preco',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'produto_id' => 'required|is_natural_no_zero',
        'tamanho_id' => 'required|is_natural_no_zero',
        'preco'      => 'required|decimal',
    ];

    /**
     * Busca os preços por produto trazendo o nome do tamanho
     */
    public function getPrecosDoProduto(int $produtoId)
    {
        return $this->select('produto_precos.*, tamanhos.nome as tamanho_nome, tamanhos.sigla, tamanhos.max_sabores')
            ->join('tamanhos', 'tamanhos.id = produto_precos.tamanho_id')
            ->where('produto_precos.produto_id', $produtoId)
            ->where('tamanhos.ativo', 1)
            ->orderBy('tamanhos.ordem', 'ASC')
            ->findAll();
    }
}
