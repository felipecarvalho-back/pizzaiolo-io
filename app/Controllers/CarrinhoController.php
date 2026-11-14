<?php

namespace App\Controllers;

use App\Models\BordaModel;
use App\Models\ClienteModel;
use App\Models\PedidoItemModel;
use App\Models\PedidoItemSaborModel;
use App\Models\PedidoModel;
use App\Models\ProdutoModel;
use App\Models\TamanhoModel;

class CarrinhoController extends BaseController
{
    /**
     * Sanitiza o valor de troco informado pelo cliente quando o pagamento é em dinheiro.
     */
    public static function sanitizarTroco(?string $pagamento, ?string $trocoParaRaw): ?float
    {
        if ($pagamento !== 'dinheiro' || empty($trocoParaRaw)) {
            return null;
        }

        $raw = trim((string) $trocoParaRaw);
        $raw = preg_replace('/[^\d.,]/', '', $raw) ?? '';
        if (str_contains($raw, ',')) {
            $raw = str_replace('.', '', $raw);
            $raw = str_replace(',', '.', $raw);
        }

        if (is_numeric($raw)) {
            $val = round((float) $raw, 2);
            return $val > 0 ? $val : null;
        }

        return null;
    }

    /**
     * Formata o destino do pedido conforme a modalidade (delivery, retirada ou mesa).
     */
    public static function formatarEnderecoEntrega(string $entrega, ?string $mesaInput, string $enderecoInput): string
    {
        $endereco = trim($enderecoInput);
        $mesa     = trim((string) $mesaInput);

        if ($entrega === 'mesa' && $mesa !== '') {
            $prefixoMesa = stripos($mesa, 'mesa') === 0 ? $mesa : 'Mesa ' . $mesa;
            return $prefixoMesa . ($endereco !== '' ? ' — ' . $endereco : '');
        }

        if ($entrega === 'retirada' && $endereco === '') {
            return 'Retirada no Balcão';
        }

        return $endereco;
    }

    /**
     * Verifica se o tamanho selecionado permite pizza meio a meio com um segundo sabor distinto.
     */
    public static function podeDividirSabores(string $tamanhoNome, int $sabor2Id, int $produtoId, int $maxSabores = 2): bool
    {
        return $tamanhoNome !== 'Broto'
            && $maxSabores >= 2
            && $sabor2Id > 0
            && $sabor2Id !== $produtoId;
    }

    /**
     * Calcula subtotal, desconto PIX (10%), taxa de entrega e total geral do pedido.
     *
     * @return array{subtotal: float, desconto_pix: float, taxa_entrega: float, total_geral: float}
     */
    public static function calcularTotaisPedido(float $subtotalProdutos, string $entrega, string $pagamento): array
    {
        $subtotal    = round(max(0.0, $subtotalProdutos), 2);
        $descontoPix = ($pagamento === 'pix') ? round($subtotal * 0.10, 2) : 0.00;
        $taxaEntrega = ($entrega === 'delivery' && $subtotal > 0) ? 5.00 : 0.00;
        $totalGeral  = round(max(0.0, $subtotal - $descontoPix) + $taxaEntrega, 2);

        return [
            'subtotal'     => $subtotal,
            'desconto_pix' => $descontoPix,
            'taxa_entrega' => $taxaEntrega,
            'total_geral'  => $totalGeral,
        ];
    }

    /**
     * Agrupa itens idênticos na sacola incrementando a quantidade e recalculando o subtotal.
     */
    public static function agruparOuAdicionarItem(array $carrinho, array $novoItem): array
    {
        $qtdAdicionar  = max(1, (int) ($novoItem['quantidade'] ?? 1));
        $precoUnitario = round((float) ($novoItem['preco_unitario'] ?? $novoItem['preco'] ?? 0.0), 2);

        foreach ($carrinho as $idx => $existente) {
            $mesmoProduto  = (int) ($existente['produto_id'] ?? 0) === (int) ($novoItem['produto_id'] ?? 0);
            $mesmoTamanho  = ($existente['tamanho_id'] ?? null) === ($novoItem['tamanho_id'] ?? null);
            $mesmaBorda    = ($existente['borda_id'] ?? null) === ($novoItem['borda_id'] ?? null);
            $mesmoSabor2   = ($existente['sabor_2_id'] ?? null) === ($novoItem['sabor_2_id'] ?? null);
            $mesmoDetalhe  = ($existente['detalhes'] ?? '') === ($novoItem['detalhes'] ?? '');

            if ($mesmoProduto && $mesmoTamanho && $mesmaBorda && $mesmoSabor2 && $mesmoDetalhe) {
                $novaQtd = (int) ($existente['quantidade'] ?? 1) + $qtdAdicionar;
                $unit    = (float) ($existente['preco_unitario'] ?? $existente['preco'] ?? $precoUnitario);
                $carrinho[$idx]['quantidade']     = $novaQtd;
                $carrinho[$idx]['preco_unitario'] = $unit;
                $carrinho[$idx]['preco']          = round($unit * $novaQtd, 2);
                return $carrinho;
            }
        }

        $novoItem['quantidade']     = $qtdAdicionar;
        $novoItem['preco_unitario'] = $precoUnitario;
        $novoItem['preco']          = round($precoUnitario * $qtdAdicionar, 2);
        $carrinho[]                 = $novoItem;

        return $carrinho;
    }

    /**
     * Conta o total de unidades presentes na sacola.
     */
    public static function contarItensCarrinho(array $carrinho): int
    {
        $totalUnidades = 0;
        foreach ($carrinho as $item) {
            $totalUnidades += max(1, (int) ($item['quantidade'] ?? 1));
        }

        return $totalUnidades;
    }

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
    public function drawer(array $extraData = []): string
    {
        $carrinho = $this->getCarrinho();
        $cliente  = null;
        if (session()->get('cliente_logado')) {
            $clienteModel = new ClienteModel();
            $cliente      = $clienteModel->find(session()->get('cliente_id'));
        }

        return view('carrinho/_drawer_conteudo', array_merge([
            'carrinho' => $carrinho,
            'cliente'  => $cliente,
        ], $extraData));
    }

    /**
     * Retorna o contador e total para os badges
     */
    public function contador(): string
    {
        $carrinho = $this->getCarrinho();
        $qtd      = self::contarItensCarrinho($carrinho);
        $total    = array_sum(array_column($carrinho, 'preco'));

        return view('carrinho/_badge_resumo', [
            'qtd'   => $qtd,
            'total' => $total,
        ]);
    }

    /**
     * Retorna o modal de personalização de pizza renderizado pelo PHP
     */
    public function modalPizza(int $produtoId): string
    {
        $produtoModel = new ProdutoModel();
        $bordaModel   = new BordaModel();
        $tamanhoModel = new TamanhoModel();

        $produto = $produtoModel->find($produtoId);
        if (! $produto || ! $produto->ativo) {
            return '<div class="p-6 bg-white rounded-2xl text-center text-red-600 text-sm font-semibold">Produto não encontrado ou indisponível no momento.</div>';
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
    public function adicionar(): string
    {
        $produtoId = (int) $this->request->getPost('produto_id');
        $tipo      = (string) $this->request->getPost('tipo');

        $produtoModel = new ProdutoModel();
        $produto      = $produtoModel->find($produtoId);
        if (! $produto || ! $produto->ativo) {
            return $this->drawer(['erroCheckout' => 'Este produto não está mais disponível.']);
        }

        $carrinho = $this->getCarrinho();

        if ($tipo === 'pizza') {
            $tamanhoNome  = (string) ($this->request->getPost('tamanho') ?: 'Grande');
            $tamanhoModel = new TamanhoModel();
            $tamanhoObj   = $tamanhoModel->where('nome', $tamanhoNome)->first();
            $tamanhoId    = $tamanhoObj ? (int) $tamanhoObj->id : null;
            $maxSabores   = $tamanhoObj ? (int) $tamanhoObj->max_sabores : ($tamanhoNome === 'Broto' ? 1 : 2);

            $fator = match ($tamanhoNome) {
                'Broto' => 0.75,
                'Média' => 0.90,
                default => 1.00,
            };

            $precoBase       = (float) $produto->preco_base;
            $titulo          = "Pizza {$tamanhoNome} — {$produto->nome}";
            $sabor2IdInput   = (int) $this->request->getPost('sabor_2_id');
            $sabor2FinalId   = null;
            $sabor2FinalNome = null;

            if (self::podeDividirSabores($tamanhoNome, $sabor2IdInput, $produtoId, $maxSabores)) {
                $p2 = $produtoModel->find($sabor2IdInput);
                if ($p2 && $p2->ativo) {
                    $precoBase       = max($precoBase, (float) $p2->preco_base);
                    $sabor2FinalId   = (int) $p2->id;
                    $sabor2FinalNome = (string) $p2->nome;
                    $titulo          = "Pizza {$tamanhoNome} (1/2 {$produto->nome} + 1/2 {$p2->nome})";
                }
            }

            // Borda
            $bordaId    = (int) $this->request->getPost('borda_id');
            $bordaNome  = 'Tradicional';
            $precoBorda = 0.0;
            if ($bordaId > 0) {
                $bordaModel = new BordaModel();
                $borda      = $bordaModel->find($bordaId);
                if ($borda && $borda->ativo) {
                    $bordaNome  = $borda->nome;
                    $precoBorda = (float) $borda->preco;
                } else {
                    $bordaId = 0;
                }
            }

            $obs           = trim((string) $this->request->getPost('obs'));
            $precoUnitario = round(($precoBase * $fator) + $precoBorda, 2);

            $detalhesPartes = [];
            if ($sabor2FinalNome !== null) {
                $detalhesPartes[] = "Sabores: 1/2 {$produto->nome} + 1/2 {$sabor2FinalNome}";
            }
            $detalhesPartes[] = "Borda: {$bordaNome}";
            if ($obs !== '') {
                $detalhesPartes[] = "Obs: {$obs}";
            }

            $novoItem = [
                'tipo'           => 'pizza',
                'produto_id'     => (int) $produto->id,
                'sabor_1_nome'   => (string) $produto->nome,
                'sabor_2_id'     => $sabor2FinalId,
                'sabor_2_nome'   => $sabor2FinalNome,
                'tamanho_id'     => $tamanhoId,
                'tamanho_nome'   => $tamanhoNome,
                'borda_id'       => $bordaId > 0 ? $bordaId : null,
                'borda_nome'     => $bordaNome,
                'titulo'         => $titulo,
                'detalhes'       => implode(' • ', $detalhesPartes),
                'quantidade'     => 1,
                'preco_unitario' => $precoUnitario,
                'preco'          => $precoUnitario,
            ];
        } else {
            $precoUnitario = round((float) $produto->preco_base, 2);
            $novoItem      = [
                'tipo'           => 'simples',
                'produto_id'     => (int) $produto->id,
                'tamanho_id'     => null,
                'borda_id'       => null,
                'sabor_2_id'     => null,
                'sabor_2_nome'   => null,
                'titulo'         => (string) $produto->nome,
                'detalhes'       => 'Item avulso',
                'quantidade'     => 1,
                'preco_unitario' => $precoUnitario,
                'preco'          => $precoUnitario,
            ];
        }

        $carrinho = self::agruparOuAdicionarItem($carrinho, $novoItem);
        $this->salvarCarrinho($carrinho);

        $this->response->setHeader('HX-Trigger', 'atualizarCarrinho');

        return $this->drawer();
    }

    /**
     * Incrementa ou decrementa a quantidade de um item na sacola via HTMX
     */
    public function alterarQuantidade(int $index): string
    {
        $carrinho = $this->getCarrinho();
        $acao     = (string) $this->request->getPost('acao');

        if (isset($carrinho[$index])) {
            $qtdAtual  = max(1, (int) ($carrinho[$index]['quantidade'] ?? 1));
            $precoUnit = (float) ($carrinho[$index]['preco_unitario'] ?? ($carrinho[$index]['preco'] / $qtdAtual));
            $novaQtd   = ($acao === 'menos') ? ($qtdAtual - 1) : ($qtdAtual + 1);

            if ($novaQtd <= 0) {
                array_splice($carrinho, $index, 1);
            } else {
                $carrinho[$index]['quantidade']     = $novaQtd;
                $carrinho[$index]['preco_unitario'] = round($precoUnit, 2);
                $carrinho[$index]['preco']          = round($precoUnit * $novaQtd, 2);
            }

            $this->salvarCarrinho($carrinho);
        }

        $this->response->setHeader('HX-Trigger', 'atualizarCarrinho');

        return $this->drawer();
    }

    /**
     * Remove item do carrinho via HTMX
     */
    public function remover(int $index): string
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
     * Consulta rápida de status de pedido por código via HTMX / JSON
     */
    public function statusPedido(string $codigo)
    {
        $codigoLimpo = trim(urldecode($codigo));
        if ($codigoLimpo !== '' && ! str_starts_with($codigoLimpo, '#') && ! str_starts_with(strtoupper($codigoLimpo), 'PED-')) {
            $codigoLimpo = '#' . $codigoLimpo;
        }

        $pedidoModel = new PedidoModel();
        $pedido      = $pedidoModel->findByCodigo($codigoLimpo);

        if (! $pedido) {
            return $this->response->setStatusCode(404)->setJSON([
                'encontrado' => false,
                'erro'       => 'Pedido não localizado.',
            ]);
        }

        $labels = [
            'pendente'     => 'Recebido — Aguardando confirmação',
            'confirmado'   => 'Confirmado — Na fila do forno',
            'em_preparo'   => 'No Forno a Lenha (480°C)',
            'saiu_entrega' => 'Saiu para Entrega / Pronto',
            'entregue'     => 'Pedido Concluído e Entregue',
            'cancelado'    => 'Pedido Cancelado',
        ];

        return $this->response->setJSON([
            'encontrado'   => true,
            'codigo'       => $pedido->codigo,
            'status'       => $pedido->status,
            'status_label' => $labels[$pedido->status] ?? ucfirst($pedido->status),
            'updated_at'   => $pedido->updated_at ? date('H:i', strtotime((string) $pedido->updated_at)) : date('H:i'),
        ]);
    }

    /**
     * Finaliza o pedido no banco de dados com transação, validações e suporte completo a 2 sabores e desconto PIX
     */
    public function finalizar(): string
    {
        $carrinho = $this->getCarrinho();
        if (empty($carrinho)) {
            return '<div class="p-4 text-center text-red-600 font-medium">Sua sacola está vazia.</div>';
        }

        $nome         = trim((string) $this->request->getPost('nome'));
        $telefone     = trim((string) $this->request->getPost('telefone'));
        $enderecoRaw  = trim((string) $this->request->getPost('endereco'));
        $mesa         = trim((string) $this->request->getPost('mesa'));
        $pagamentoRaw = (string) $this->request->getPost('forma_pagamento');
        $entregaRaw   = (string) $this->request->getPost('tipo_entrega');
        $trocoParaRaw = (string) $this->request->getPost('troco_para');

        $pagamento = in_array($pagamentoRaw, ['pix', 'dinheiro', 'cartao_credito', 'cartao_debito'], true) ? $pagamentoRaw : 'pix';
        $entrega   = in_array($entregaRaw, ['delivery', 'retirada', 'mesa'], true) ? $entregaRaw : 'delivery';

        $formInput = [
            'nome'            => $nome,
            'telefone'        => $telefone,
            'endereco'        => $enderecoRaw,
            'mesa'            => $mesa,
            'forma_pagamento' => $pagamento,
            'tipo_entrega'    => $entrega,
            'troco_para'      => $trocoParaRaw,
        ];

        if ($nome === '' || $telefone === '') {
            return $this->drawer([
                'erroCheckout' => 'Informe seu Nome Completo e WhatsApp para continuar.',
                'formInput'    => $formInput,
            ]);
        }

        if ($entrega === 'delivery' && $enderecoRaw === '') {
            return $this->drawer([
                'erroCheckout' => 'Informe o endereço completo para entrega.',
                'formInput'    => $formInput,
            ]);
        }

        if ($entrega === 'mesa' && $mesa === '') {
            return $this->drawer([
                'erroCheckout' => 'Informe o número da mesa para atendimento no salão.',
                'formInput'    => $formInput,
            ]);
        }

        $subtotalProdutos = (float) array_sum(array_column($carrinho, 'preco'));
        $totais           = self::calcularTotaisPedido($subtotalProdutos, $entrega, $pagamento);
        $trocoPara        = self::sanitizarTroco($pagamento, $trocoParaRaw);

        if ($pagamento === 'dinheiro' && $trocoPara !== null && $trocoPara < $totais['total_geral']) {
            $totalFmt = number_format($totais['total_geral'], 2, ',', '.');
            return $this->drawer([
                'erroCheckout' => "O valor para troco deve ser maior ou igual ao total do pedido (R$ {$totalFmt}).",
                'formInput'    => $formInput,
            ]);
        }

        $enderecoFinal = self::formatarEnderecoEntrega($entrega, $mesa, $enderecoRaw);
        $clienteId     = session()->get('cliente_id') ? (int) session()->get('cliente_id') : null;

        $pedidoModel = new PedidoModel();
        $itemModel   = new PedidoItemModel();
        $saborModel  = new PedidoItemSaborModel();

        $codigo = $pedidoModel->gerarCodigoUnico();

        $db = \Config\Database::connect();
        $db->transStart();

        $pedidoId = $pedidoModel->insert([
            'codigo'           => $codigo,
            'cliente_id'       => $clienteId,
            'cliente_nome'     => $nome,
            'cliente_telefone' => $telefone,
            'tipo_entrega'     => $entrega,
            'endereco'         => $enderecoFinal,
            'forma_pagamento'  => $pagamento,
            'troco_para'       => $trocoPara,
            'status'           => 'pendente',
            'status_pagamento' => ($pagamento === 'pix') ? 'aguardando' : 'na_entrega',
            'total_produtos'   => $totais['subtotal'],
            'taxa_entrega'     => $totais['taxa_entrega'],
            'total_geral'      => $totais['total_geral'],
            'observacoes'      => $totais['desconto_pix'] > 0
                ? 'Desconto PIX 10%: -R$ ' . number_format($totais['desconto_pix'], 2, ',', '.')
                : null,
        ]);

        if ($pedidoId) {
            foreach ($carrinho as $item) {
                $quantidade    = max(1, (int) ($item['quantidade'] ?? 1));
                $precoUnitario = round((float) ($item['preco_unitario'] ?? ($item['preco'] / $quantidade)), 2);
                $subtotalItem  = round((float) ($item['preco'] ?? ($precoUnitario * $quantidade)), 2);

                $pedidoItemId = $itemModel->insert([
                    'pedido_id'      => (int) $pedidoId,
                    'produto_id'     => (int) $item['produto_id'],
                    'tamanho_id'     => ! empty($item['tamanho_id']) ? (int) $item['tamanho_id'] : null,
                    'borda_id'       => ! empty($item['borda_id']) ? (int) $item['borda_id'] : null,
                    'quantidade'     => $quantidade,
                    'preco_unitario' => $precoUnitario,
                    'subtotal'       => $subtotalItem,
                    'observacao'     => $item['detalhes'] ?? null,
                ]);

                if ($pedidoItemId && (($item['tipo'] ?? '') === 'pizza' || ! empty($item['tamanho_id']) || ! empty($item['sabor_2_id']))) {
                    // Registra o 1º sabor
                    $saborModel->insert([
                        'pedido_item_id' => (int) $pedidoItemId,
                        'produto_id'     => (int) $item['produto_id'],
                    ]);

                    // Registra o 2º sabor quando for pizza Meio a Meio
                    if (! empty($item['sabor_2_id'])) {
                        $saborModel->insert([
                            'pedido_item_id' => (int) $pedidoItemId,
                            'produto_id'     => (int) $item['sabor_2_id'],
                        ]);
                    }
                }
            }
        }

        $db->transComplete();

        if (! $pedidoId || ! $db->transStatus()) {
            return $this->drawer([
                'erroCheckout' => 'Não foi possível registrar seu pedido agora. Tente novamente.',
                'formInput'    => $formInput,
            ]);
        }

        $itensConfirmados = $carrinho;

        // Limpa o carrinho após confirmação da transação
        $this->salvarCarrinho([]);
        $this->response->setHeader('HX-Trigger', 'atualizarCarrinho');

        return view('carrinho/_sucesso_pedido', [
            'codigo'       => $codigo,
            'nome'         => $nome,
            'telefone'     => $telefone,
            'entrega'      => $entrega,
            'endereco'     => $enderecoFinal,
            'pagamento'    => $pagamento,
            'trocoPara'    => $trocoPara,
            'subtotal'     => $totais['subtotal'],
            'descontoPix'  => $totais['desconto_pix'],
            'taxaEntrega'  => $totais['taxa_entrega'],
            'totalGeral'   => $totais['total_geral'],
            'itens'        => $itensConfirmados,
        ]);
    }
}
