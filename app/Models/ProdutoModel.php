<?php

namespace App\Models;

use App\Entities\Produto;
use CodeIgniter\Model;

class ProdutoModel extends Model
{
    protected $table            = 'produtos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Produto::class;
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'categoria_id',
        'nome',
        'slug',
        'descricao',
        'tipo',
        'preco_base',
        'imagem',
        'ativo',
        'destaque',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'categoria_id' => 'required|is_natural_no_zero',
        'nome'         => 'required|min_length[2]|max_length[120]',
        'slug'         => 'required|alpha_dash|is_unique[produtos.slug,id,{id}]',
        'tipo'         => 'required|in_list[pizza,simples]',
    ];

    /**
     * Retorna produtos com os seus preços e tamanhos vinculados
     */
    public function getComPrecos(?int $categoriaId = null)
    {
        $builder = $this->select('produtos.*, categorias.nome as categoria_nome')
            ->join('categorias', 'categorias.id = produtos.categoria_id')
            ->where('produtos.ativo', 1);

        if ($categoriaId) {
            $builder->where('produtos.categoria_id', $categoriaId);
        }

        return $builder->findAll();
    }
}
