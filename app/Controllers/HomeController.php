<?php

namespace App\Controllers;

use App\Models\BordaModel;
use App\Models\CategoriaModel;
use App\Models\ClienteModel;
use App\Models\ProdutoModel;

class HomeController extends BaseController
{
    public function index(): string
    {
        $produtoModel   = new ProdutoModel();
        $categoriaModel = new CategoriaModel();
        $bordaModel     = new BordaModel();

        $produtos = $produtoModel
            ->select('produtos.*, categorias.slug as categoria_slug, categorias.nome as categoria_nome')
            ->join('categorias', 'categorias.id = produtos.categoria_id', 'left')
            ->where('produtos.ativo', 1)
            ->orderBy('produtos.destaque', 'DESC')
            ->orderBy('produtos.id', 'ASC')
            ->findAll();

        $categorias = $categoriaModel
            ->where('ativo', 1)
            ->orderBy('ordem', 'ASC')
            ->findAll();

        $bordas = $bordaModel
            ->where('ativo', 1)
            ->orderBy('preco', 'ASC')
            ->findAll();

        $cliente = null;
        if (session()->get('cliente_logado')) {
            $clienteModel = new ClienteModel();
            $cliente = $clienteModel->find(session()->get('cliente_id'));
        }

        return view('cardapio', [
            'produtos'   => $produtos,
            'categorias' => $categorias,
            'bordas'     => $bordas,
            'cliente'    => $cliente,
        ]);
    }
}
