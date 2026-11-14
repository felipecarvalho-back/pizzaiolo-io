<?php

namespace App\Controllers;

use App\Models\BordaModel;
use App\Models\CategoriaModel;
use App\Models\PedidoItemModel;
use App\Models\PedidoItemSaborModel;
use App\Models\PedidoModel;
use App\Models\ProdutoModel;
use App\Models\TamanhoModel;
use App\Models\UsuarioModel;

class PainelController extends BaseController
{
    /**
     * Sanitiza valores monetários (ex: "69,90", "69.90", "R$ 1.200,50", "0,00").
     */
    public static function sanitizarPreco(?string $valorRaw): ?float
    {
        $raw = trim((string) $valorRaw);
        if ($raw === '' || str_contains($raw, '-')) {
            return null;
        }

        $raw = preg_replace('/[^\d.,]/', '', $raw) ?? '';
        if ($raw === '') {
            return null;
        }

        if (str_contains($raw, ',')) {
            $raw = str_replace('.', '', $raw);
            $raw = str_replace(',', '.', $raw);
        }

        if (! is_numeric($raw)) {
            return null;
        }

        $val = round((float) $raw, 2);
        return $val >= 0.0 ? $val : null;
    }

    private function verificarAcesso()
    {
        if (! session()->get('logado')) {
            return redirect()->to('/login')->with('erro', 'Faça login para acessar o painel.');
        }

        return null;
    }

    private function verificarAdmin()
    {
        if ($redirect = $this->verificarAcesso()) {
            return $redirect;
        }

        $cargo = (string) (session()->get('usuario_cargo') ?? '');
        if ($cargo !== 'admin') {
            return redirect()->to('/painel')->with('erro', 'Acesso restrito ao perfil Administrador.');
        }

        return null;
    }

    public function index()
    {
        if ($redirect = $this->verificarAcesso()) {
            return $redirect;
        }

        $produtoModel    = new ProdutoModel();
        $categoriaModel  = new CategoriaModel();
        $bordaModel      = new BordaModel();
        $tamanhoModel    = new TamanhoModel();
        $usuarioModel    = new UsuarioModel();
        $pedidoModel     = new PedidoModel();
        $pedidoItemModel = new PedidoItemModel();

        $pedidos = $pedidoModel->orderBy('id', 'DESC')->findAll(50);

        // Carregamento em lote (Batch Eager Loading) eliminando Query N+1
        if (! empty($pedidos)) {
            $pedidoIds  = array_map(static fn ($p) => (int) $p->id, $pedidos);
            $todosItens = $pedidoItemModel
                ->select('pedido_itens.*, produtos.nome as produto_nome, tamanhos.nome as tamanho_nome, bordas.nome as borda_nome')
                ->join('produtos', 'produtos.id = pedido_itens.produto_id', 'left')
                ->join('tamanhos', 'tamanhos.id = pedido_itens.tamanho_id', 'left')
                ->join('bordas', 'bordas.id = pedido_itens.borda_id', 'left')
                ->whereIn('pedido_itens.pedido_id', $pedidoIds)
                ->findAll();

            $saboresPorItem = [];
            if (! empty($todosItens)) {
                $itemIds      = array_map(static fn ($i) => (int) $i->id, $todosItens);
                $saborModel   = new PedidoItemSaborModel();
                $todosSabores = $saborModel
                    ->select('pedido_item_sabores.pedido_item_id, produtos.id as produto_id, produtos.nome')
                    ->join('produtos', 'produtos.id = pedido_item_sabores.produto_id')
                    ->whereIn('pedido_item_sabores.pedido_item_id', $itemIds)
                    ->orderBy('pedido_item_sabores.id', 'ASC')
                    ->findAll();

                foreach ($todosSabores as $sabor) {
                    $saboresPorItem[(int) $sabor['pedido_item_id']][] = $sabor['nome'];
                }
            }

            $itensPorPedido = [];
            foreach ($todosItens as $item) {
                $item->sabores_nomes = $saboresPorItem[(int) $item->id] ?? [];
                $itensPorPedido[(int) $item->pedido_id][] = $item;
            }

            foreach ($pedidos as $p) {
                $p->itens = $itensPorPedido[(int) $p->id] ?? [];
            }
        }

        $produtos = $produtoModel
            ->select('produtos.*, categorias.nome as categoria_nome')
            ->join('categorias', 'categorias.id = produtos.categoria_id', 'left')
            ->orderBy('produtos.id', 'DESC')
            ->findAll();

        // Cálculo de Métricas Operacionais Reais via consultas agregadas no banco
        $inicioHoje  = date('Y-m-d 00:00:00');
        $pedidosFila = (int) (new PedidoModel())
            ->whereIn('status', ['pendente', 'confirmado', 'em_preparo', 'saiu_entrega'])
            ->countAllResults();

        $resumoHoje = (new PedidoModel())
            ->select('COUNT(id) as total_pedidos, COALESCE(SUM(total_geral), 0) as faturamento')
            ->where('created_at >=', $inicioHoje)
            ->where('status !=', 'cancelado')
            ->asArray()
            ->first();

        $pedidosHoje     = (int) ($resumoHoje['total_pedidos'] ?? 0);
        $faturamentoHoje = round((float) ($resumoHoje['faturamento'] ?? 0.0), 2);

        $produtosAtivos = 0;
        foreach ($produtos as $prod) {
            if ($prod->ativo) {
                $produtosAtivos++;
            }
        }

        $dados = [
            'produtos'   => $produtos,
            'categorias' => $categoriaModel->orderBy('ordem', 'ASC')->findAll(),
            'bordas'     => $bordaModel->orderBy('id', 'ASC')->findAll(),
            'tamanhos'   => $tamanhoModel->orderBy('ordem', 'ASC')->findAll(),
            'usuarios'   => $usuarioModel->orderBy('id', 'DESC')->findAll(),
            'pedidos'    => $pedidos,
            'metricas'   => [
                'pedidos_fila'     => $pedidosFila,
                'pedidos_hoje'     => $pedidosHoje,
                'faturamento_hoje' => $faturamentoHoje,
                'produtos_ativos'  => $produtosAtivos,
            ],
        ];

        return view('painel/index', $dados);
    }

    public function atualizarStatusPedido()
    {
        if ($redirect = $this->verificarAcesso()) {
            return $redirect;
        }

        $pedidoId   = (int) $this->request->getPost('pedido_id');
        $novoStatus = (string) $this->request->getPost('status');

        $statusValidos = ['pendente', 'confirmado', 'em_preparo', 'saiu_entrega', 'entregue', 'cancelado'];
        if (! in_array($novoStatus, $statusValidos, true)) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON(['sucesso' => false, 'erro' => 'Status inválido.']);
            }
            return redirect()->to('/painel?aba=pedidos')->with('erro', 'Status inválido.');
        }

        $pedidoModel = new PedidoModel();
        $pedido      = $pedidoModel->find($pedidoId);
        if (! $pedido) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(404)->setJSON(['sucesso' => false, 'erro' => 'Pedido não encontrado.']);
            }
            return redirect()->to('/painel?aba=pedidos')->with('erro', 'Pedido não encontrado.');
        }

        $dadosUpdate = ['status' => $novoStatus];
        if ($novoStatus === 'entregue') {
            $dadosUpdate['status_pagamento'] = 'pago';
        }

        $pedidoModel->update($pedidoId, $dadosUpdate);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'sucesso'     => true,
                'mensagem'    => "Status do pedido {$pedido->codigo} atualizado para {$novoStatus}.",
                'pedido_id'   => $pedidoId,
                'novo_status' => $novoStatus,
                'csrf_hash'   => csrf_hash(),
            ]);
        }

        return redirect()->to('/painel?aba=pedidos')->with('sucesso', "Status do pedido {$pedido->codigo} atualizado com sucesso!");
    }

    public function salvarProduto()
    {
        if ($redirect = $this->verificarAdmin()) {
            return $redirect;
        }

        $id          = (int) $this->request->getPost('id');
        $nome        = trim((string) $this->request->getPost('nome'));
        $categoriaId = (int) $this->request->getPost('categoria_id');
        $tipo        = (string) $this->request->getPost('tipo');
        $precoBase   = self::sanitizarPreco((string) $this->request->getPost('preco_base')) ?? 0.0;
        $descricao   = trim((string) $this->request->getPost('descricao'));
        $imagem      = trim((string) $this->request->getPost('imagem'));
        $destaque    = $this->request->getPost('destaque') ? 1 : 0;

        if (strlen($nome) < 2 || $categoriaId <= 0 || ! in_array($tipo, ['pizza', 'simples'], true) || $precoBase <= 0) {
            return redirect()->to('/painel?aba=produtos')->with('erro', 'Preencha nome, categoria e preço válido para o produto.');
        }

        helper('text');
        $slugBase = url_title(convert_accented_characters($nome), '-', true);
        if ($slugBase === '') {
            $slugBase = 'item';
        }

        if ($imagem === '') {
            $imagem = 'https://lh3.googleusercontent.com/aida-public/AB6AXuDXCJbgOLcySP9RMhpEMCnfbTazeLvI2c-l0JAIyzewcdULRuBPqbtiNNssi44lRnaGQFSf5-qknWyHGDRMzlvSdSMRO66RCgqnvrz5P3Udpc_3DRHftlvK5AzUrJPbxhyrNB0D_lDBdRIQjL-4c7e0Y5mgbCgyq49lqYD7yzlsm3OpDWbixitV2K9rFajtArgCnMC6X11_kimu13zUAYt8CTxblGoq4VaT4j2Dxaw';
        }

        $produtoModel = new ProdutoModel();

        if ($id > 0) {
            $existente = $produtoModel->find($id);
            if (! $existente) {
                return redirect()->to('/painel?aba=produtos')->with('erro', 'Produto não encontrado para edição.');
            }

            $produtoModel->update($id, [
                'categoria_id' => $categoriaId,
                'nome'         => $nome,
                'descricao'    => $descricao,
                'tipo'         => $tipo,
                'preco_base'   => $precoBase,
                'imagem'       => $imagem,
                'destaque'     => $destaque,
            ]);

            return redirect()->to('/painel?aba=produtos')->with('sucesso', 'Produto "' . $nome . '" atualizado com sucesso!');
        }

        $slug = $slugBase . '-' . random_int(10, 99);
        $produtoModel->insert([
            'categoria_id' => $categoriaId,
            'nome'         => $nome,
            'slug'         => $slug,
            'descricao'    => $descricao,
            'tipo'         => $tipo,
            'preco_base'   => $precoBase,
            'imagem'       => $imagem,
            'ativo'        => 1,
            'destaque'     => $destaque,
        ]);

        return redirect()->to('/painel?aba=produtos')->with('sucesso', 'Produto "' . $nome . '" cadastrado com sucesso!');
    }

    public function alternarProduto(int $id)
    {
        if ($redirect = $this->verificarAdmin()) {
            return $redirect;
        }

        $produtoModel = new ProdutoModel();
        $produto      = $produtoModel->find($id);
        if (! $produto) {
            return redirect()->to('/painel?aba=produtos')->with('erro', 'Produto não encontrado.');
        }

        $novoStatus = $produto->ativo ? 0 : 1;
        $produtoModel->update($id, ['ativo' => $novoStatus]);

        $msg = $novoStatus ? 'ativado e disponível no cardápio' : 'pausado no cardápio';
        return redirect()->to('/painel?aba=produtos')->with('sucesso', "Produto \"{$produto->nome}\" {$msg}.");
    }

    public function excluirProduto(int $id)
    {
        if ($redirect = $this->verificarAdmin()) {
            return $redirect;
        }

        $produtoModel = new ProdutoModel();
        $produtoModel->delete($id);

        return redirect()->to('/painel?aba=produtos')->with('sucesso', 'Produto removido com sucesso.');
    }

    public function salvarCategoria()
    {
        if ($redirect = $this->verificarAdmin()) {
            return $redirect;
        }

        $nome      = trim((string) $this->request->getPost('nome'));
        $descricao = trim((string) $this->request->getPost('descricao'));
        $ordem     = (int) $this->request->getPost('ordem');

        if (strlen($nome) < 2) {
            return redirect()->to('/painel?aba=categorias')->with('erro', 'Informe um nome válido para a categoria.');
        }

        helper('text');
        $slug = url_title(convert_accented_characters($nome), '-', true);
        if ($slug === '') {
            $slug = 'cat-' . random_int(100, 999);
        }

        $categoriaModel = new CategoriaModel();
        if ($categoriaModel->where('slug', $slug)->first()) {
            $slug .= '-' . random_int(10, 99);
        }

        $categoriaModel->insert([
            'nome'      => $nome,
            'slug'      => $slug,
            'descricao' => $descricao,
            'ordem'     => $ordem,
            'ativo'     => 1,
        ]);

        return redirect()->to('/painel?aba=categorias')->with('sucesso', 'Categoria "' . $nome . '" cadastrada!');
    }

    public function excluirCategoria(int $id)
    {
        if ($redirect = $this->verificarAdmin()) {
            return $redirect;
        }

        $categoriaModel = new CategoriaModel();
        $categoriaModel->delete($id);

        return redirect()->to('/painel?aba=categorias')->with('sucesso', 'Categoria removida com sucesso.');
    }

    public function salvarBorda()
    {
        if ($redirect = $this->verificarAdmin()) {
            return $redirect;
        }

        $nome  = trim((string) $this->request->getPost('nome'));
        $preco = self::sanitizarPreco((string) $this->request->getPost('preco')) ?? -1.0;

        if (strlen($nome) < 2 || $preco < 0) {
            return redirect()->to('/painel?aba=bordas')->with('erro', 'Informe nome e valor adicional válido para a borda.');
        }

        $bordaModel = new BordaModel();
        $bordaModel->insert([
            'nome'  => $nome,
            'preco' => number_format($preco, 2, '.', ''),
            'ativo' => 1,
        ]);

        return redirect()->to('/painel?aba=bordas')->with('sucesso', 'Borda "' . $nome . '" cadastrada!');
    }

    public function excluirBorda(int $id)
    {
        if ($redirect = $this->verificarAdmin()) {
            return $redirect;
        }

        $bordaModel = new BordaModel();
        $bordaModel->delete($id);

        return redirect()->to('/painel?aba=bordas')->with('sucesso', 'Borda removida com sucesso.');
    }

    public function salvarUsuario()
    {
        if ($redirect = $this->verificarAdmin()) {
            return $redirect;
        }

        $nome  = trim((string) $this->request->getPost('nome'));
        $email = trim((string) $this->request->getPost('email'));
        $senha = (string) $this->request->getPost('senha');
        $cargo = (string) $this->request->getPost('cargo');

        if (strlen($nome) < 2 || ! filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($senha) < 4) {
            return redirect()->to('/painel?aba=usuarios')->with('erro', 'Preencha nome, e-mail válido e senha com no mínimo 4 caracteres.');
        }

        if (! in_array($cargo, ['admin', 'atendente', 'cozinha'], true)) {
            $cargo = 'atendente';
        }

        $usuarioModel = new UsuarioModel();
        if ($usuarioModel->where('email', $email)->first()) {
            return redirect()->to('/painel?aba=usuarios')->with('erro', 'Este e-mail já está cadastrado.');
        }

        $usuarioModel->insert([
            'nome'  => $nome,
            'email' => $email,
            'senha' => password_hash($senha, PASSWORD_DEFAULT),
            'cargo' => $cargo,
            'ativo' => 1,
        ]);

        return redirect()->to('/painel?aba=usuarios')->with('sucesso', 'Usuário "' . $nome . '" cadastrado com sucesso!');
    }

    public function excluirUsuario(int $id)
    {
        if ($redirect = $this->verificarAdmin()) {
            return $redirect;
        }

        if ($id === (int) session()->get('usuario_id')) {
            return redirect()->to('/painel?aba=usuarios')->with('erro', 'Você não pode excluir seu próprio usuário logado.');
        }

        $usuarioModel = new UsuarioModel();
        $usuarioModel->delete($id);

        return redirect()->to('/painel?aba=usuarios')->with('sucesso', 'Usuário removido.');
    }
}
