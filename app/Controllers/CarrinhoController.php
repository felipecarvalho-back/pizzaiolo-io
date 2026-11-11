<?php

namespace App\Controllers;

use App\Models\BordaModel;
use App\Models\PedidoItemModel;
use App\Models\PedidoModel;
use App\Models\ProdutoModel;
use App\Models\TamanhoModel;

class CarrinhoController extends BaseController
{
    /**
     * Retorna os itens do carrinho salvos na sessão
     */
    private function getCarrinho(): array
    {
        return session()->get('carrinho') ?? [];
    }

    private function salvarCarrinho(array $carrinho): void
    {
        session()->set('carrinho', $carrinho);
    }

    /**
     * Retorna o HTML do drawer da sacola
     */
    public function drawer()
    {
        $carrinho = $this->getCarrinho();
        $cliente = null;
        if (session()->get('cliente_logado')) {
            $clienteModel = new \App\Models\ClienteModel();
            $cliente = $clienteModel->find(session()->get('cliente_id'));
        }

        return view('carrinho/_drawer_conteudo', [
            'carrinho' => $carrinho,
            'cliente'  => $cliente,
        ]);
    }

    /**
     * Retorna o contador e total para os badges
     */
    public function contador()
    {
        $carrinho = $this->getCarrinho();
        $qtd = count($carrinho);
        $total = array_sum(array_column($carrinho, 'preco'));

        return view('carrinho/_badge_resumo', [
            'qtd'   => $qtd,
            'total' => $total,
        ]);
    }

    /**
     * Retorna o modal de personalização de pizza renderizado pelo PHP
     */
    public function modalPizza(int $produtoId)
    {
        $produtoModel = new ProdutoModel();
        $bordaModel   = new BordaModel();
        $tamanhoModel = new TamanhoModel();

        $produto = $produtoModel->find($produtoId);
        if (! $produto) {
            return '<div class="p-4 text-center text-red-500">Produto não encontrado.</div>';
        }

        $todasPizzas = $produtoModel->where('tipo', 'pizza')->where('ativo', 1)->findAll();
        $bordas      = $bordaModel->where('ativo', 1)->orderBy('preco', 'ASC')->findAll();
        $tamanhos    = $tamanhoModel->where('ativo', 1)->orderBy('ordem', 'ASC')->findAll();

        return view('cardapio/_modal_pizza', [
            'produto'     => $produto,
            'todasPizzas' => $todasPizzas,
            'bordas'      => $bordas,
            'tamanhos'    => $tamanhos,
        ]);
    }

    /**
     * Adiciona pizza ou item simples ao carrinho via HTMX
     */
    public function adicionar()
    {
        $produtoId = (int) $this->request->getPost('produto_id');
        $tipo      = (string) $this->request->getPost('tipo');

        $produtoModel = new ProdutoModel();
        $produto = $produtoModel->find($produtoId);
        if (! $produto) {
            return $this->drawer();
        }

        $carrinho = $this->getCarrinho();

        if ($tipo === 'pizza') {
            $tamanhoNome = (string) $this->request->getPost('tamanho');
            $fator = match ($tamanhoNome) {
                'Broto' => 0.75,
                'Média' => 0.9,
                default => 1.0,
            };

            $precoBase = (float) $produto->preco_base;
            $titulo = "Pizza {$tamanhoNome} — {$produto->nome}";
            $sabor2Id = (int) $this->request->getPost('sabor_2_id');

            if ($sabor2Id > 0 && $sabor2Id !== $produtoId) {
                $p2 = $produtoModel->find($sabor2Id);
                if ($p2) {
                    $precoBase = max($precoBase, (float) $p2->preco_base);
                    $titulo = "Pizza {$tamanhoNome} (1/2 {$produto->nome} + 1/2 {$p2->nome})";
                }
            }

            // Borda
            $bordaId = (int) $this->request->getPost('borda_id');
            $bordaNome = 'Tradicional';
            $precoBorda = 0.0;
            if ($bordaId > 0) {
                $bordaModel = new BordaModel();
                $borda = $bordaModel->find($bordaId);
                if ($borda) {
                    $bordaNome = $borda->nome;
                    $precoBorda = (float) $borda->preco;
                }
            }

            $obs = trim((string) $this->request->getPost('obs'));
            $precoFinal = ($precoBase * $fator) + $precoBorda;

            $tamanhoModel = new TamanhoModel();
            $tamanhoObj   = $tamanhoModel->where('nome', $tamanhoNome)->first();
            $tamanhoId    = $tamanhoObj ? $tamanhoObj->id : null;

            $carrinho[] = [
                'produto_id' => $produto->id,
                'tamanho_id' => $tamanhoId,
                'borda_id'   => $bordaId > 0 ? $bordaId : null,
                'titulo'     => $titulo,
                'detalhes'   => "Borda: {$bordaNome}" . ($obs ? " • Obs: {$obs}" : ''),
                'preco'      => $precoFinal,
            ];
        } else {
            // Item simples (bebida)
            $carrinho[] = [
                'produto_id' => $produto->id,
                'titulo'     => $produto->nome,
                'detalhes'   => 'Item avulso',
                'preco'      => (float) $produto->preco_base,
            ];
        }

        $this->salvarCarrinho($carrinho);

        // Header do HTMX para disparar atualização automática do contador no header e na barra flutuante
        $this->response->setHeader('HX-Trigger', 'atualizarCarrinho');

        return $this->drawer();
    }

    /**
     * Remove item do carrinho via HTMX
     */
    public function remover(int $index)
    {
        $carrinho = $this->getCarrinho();
        if (isset($carrinho[$index])) {
            array_splice($carrinho, $index, 1);
            $this->salvarCarrinho($carrinho);
        }

        $this->response->setHeader('HX-Trigger', 'atualizarCarrinho');
        return $this->drawer();
    }

    /**
     * Finaliza o pedido no banco de dados e retorna a tela de sucesso
     */
    public function finalizar()
    {
        $carrinho = $this->getCarrinho();
        if (empty($carrinho)) {
            return '<div class="p-4 text-center text-red-600 font-medium">Sua sacola está vazia.</div>';
        }

        $nome      = trim((string) $this->request->getPost('nome'));
        $telefone  = trim((string) $this->request->getPost('telefone'));
        $endereco  = trim((string) $this->request->getPost('endereco'));
        $pagamento = (string) $this->request->getPost('forma_pagamento');
        $entrega   = (string) $this->request->getPost('tipo_entrega');

        $trocoParaRaw = $this->request->getPost('troco_para');
        $trocoPara    = null;
        if (!empty($trocoParaRaw)) {
            $trocoLimpo = str_replace(['R$', ' ', '.'], '', (string) $trocoParaRaw);
            $trocoLimpo = str_replace(',', '.', $trocoLimpo);
            if (is_numeric($trocoLimpo)) {
                $trocoPara = (float) $trocoLimpo;
            }
        }

        if (empty($nome) || empty($telefone)) {
            return '<div class="p-3 bg-red-50 text-red-700 text-xs rounded-xl mb-3">Informe seu Nome e WhatsApp.</div>' . $this->drawer();
        }

        $totalProdutos = array_sum(array_column($carrinho, 'preco'));
        $taxaEntrega   = ($entrega === 'delivery') ? 5.00 : 0.00;
        $totalGeral    = $totalProdutos + $taxaEntrega;
        $codigo        = '#FORNO-' . rand(1000, 9999);

        $clienteId = session()->get('cliente_id') ?? null;

        $pedidoModel = new PedidoModel();
        $pedidoId = $pedidoModel->insert([
            'codigo'           => $codigo,
            'cliente_id'       => $clienteId,
            'cliente_nome'     => $nome,
            'cliente_telefone' => $telefone,
            'tipo_entrega'     => $entrega,
            'endereco'         => $endereco,
            'forma_pagamento'  => in_array($pagamento, ['pix', 'dinheiro', 'cartao_credito', 'cartao_debito']) ? $pagamento : 'pix',
            'troco_para'       => $trocoPara,
            'status'           => 'pendente',
            'status_pagamento' => ($pagamento === 'pix') ? 'aguardando' : 'na_entrega',
            'total_produtos'   => $totalProdutos,
            'taxa_entrega'     => $taxaEntrega,
            'total_geral'      => $totalGeral,
        ]);

        $itemModel = new PedidoItemModel();
        foreach ($carrinho as $item) {
            $itemModel->insert([
                'pedido_id'      => $pedidoId,
                'produto_id'     => $item['produto_id'],
                'tamanho_id'     => $item['tamanho_id'] ?? null,
                'borda_id'       => $item['borda_id'] ?? null,
                'quantidade'     => 1,
                'preco_unitario' => $item['preco'],
                'subtotal'       => $item['preco'],
                'observacao'     => $item['detalhes'] ?? null,
            ]);
        }

        // Limpa o carrinho
        $this->salvarCarrinho([]);
        $this->response->setHeader('HX-Trigger', 'atualizarCarrinho');

        return view('carrinho/_sucesso_pedido', [
            'codigo'     => $codigo,
            'totalGeral' => $totalGeral,
            'pagamento'  => $pagamento,
        ]);
    }
}
