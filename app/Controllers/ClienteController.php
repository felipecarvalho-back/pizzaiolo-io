<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\PedidoItemModel;
use App\Models\PedidoItemSaborModel;
use App\Models\PedidoModel;

class ClienteController extends BaseController
{
    public function cadastro()
    {
        if (session()->get('cliente_logado')) {
            return redirect()->to('/cliente/perfil');
        }

        return view('cliente/cadastro');
    }

    public function registrar()
    {
        $nome     = trim((string) $this->request->getPost('nome'));
        $email    = trim((string) $this->request->getPost('email'));
        $telefone = trim((string) $this->request->getPost('telefone'));
        $senha    = (string) $this->request->getPost('senha');

        if (strlen($nome) < 3 || empty($email) || empty($telefone) || strlen($senha) < 4) {
            return redirect()->back()->withInput()->with('erro', 'Preencha todos os campos obrigatórios corretamente.');
        }

        $clienteModel = new ClienteModel();
        if ($clienteModel->where('email', $email)->first()) {
            return redirect()->back()->withInput()->with('erro', 'Este e-mail já está em uso.');
        }

        $clienteId = $clienteModel->insert([
            'nome'        => $nome,
            'email'       => $email,
            'telefone'    => $telefone,
            'senha'       => password_hash($senha, PASSWORD_DEFAULT),
            'cep'         => trim((string) $this->request->getPost('cep')),
            'endereco'    => trim((string) $this->request->getPost('endereco')),
            'numero'      => trim((string) $this->request->getPost('numero')),
            'bairro'      => trim((string) $this->request->getPost('bairro')),
            'complemento' => trim((string) $this->request->getPost('complemento')),
            'cidade'      => trim((string) $this->request->getPost('cidade')),
            'ativo'       => 1,
        ]);

        session()->regenerate(true);
        session()->set([
            'cliente_id'       => $clienteId,
            'cliente_nome'     => $nome,
            'cliente_email'    => $email,
            'cliente_telefone' => $telefone,
            'cliente_logado'   => true,
        ]);

        return redirect()->to('/')->with('sucesso', 'Cadastro realizado com sucesso! Faça seu pedido.');
    }

    public function perfil()
    {
        if (! session()->get('cliente_logado')) {
            return redirect()->to('/login?redirect=/cliente/perfil')->with('erro', 'Faça login para acessar seu perfil.');
        }

        $clienteModel = new ClienteModel();
        $cliente = $clienteModel->find(session()->get('cliente_id'));

        if (! $cliente) {
            session()->remove(['cliente_id', 'cliente_nome', 'cliente_email', 'cliente_telefone', 'cliente_logado']);
            return redirect()->to('/login');
        }

        $pedidoModel = new PedidoModel();
        $pedidos = $pedidoModel->where('cliente_id', $cliente->id)
                               ->orderBy('id', 'DESC')
                               ->findAll();

        // Carrega itens e sabores em lote (sem N+1) para exibir histórico detalhado e meio a meio
        if (! empty($pedidos)) {
            $pedidoIds = array_map(static fn ($p) => (int) $p->id, $pedidos);
            $pedidoItemModel = new PedidoItemModel();
            $todosItens = $pedidoItemModel
                ->select('pedido_itens.*, produtos.nome as produto_nome, tamanhos.nome as tamanho_nome, bordas.nome as borda_nome')
                ->join('produtos', 'produtos.id = pedido_itens.produto_id', 'left')
                ->join('tamanhos', 'tamanhos.id = pedido_itens.tamanho_id', 'left')
                ->join('bordas', 'bordas.id = pedido_itens.borda_id', 'left')
                ->whereIn('pedido_itens.pedido_id', $pedidoIds)
                ->findAll();

            $saboresPorItem = [];
            if (! empty($todosItens)) {
                $itemIds = array_map(static fn ($i) => (int) $i->id, $todosItens);
                $saborModel = new PedidoItemSaborModel();
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

        return view('cliente/perfil', [
            'cliente' => $cliente,
            'pedidos' => $pedidos,
        ]);
    }

    public function atualizarPerfil()
    {
        if (! session()->get('cliente_logado')) {
            return redirect()->to('/login');
        }

        $clienteId = (int) session()->get('cliente_id');
        $nome      = trim((string) $this->request->getPost('nome'));
        $telefone  = trim((string) $this->request->getPost('telefone'));

        if (strlen($nome) < 3 || empty($telefone)) {
            return redirect()->back()->with('erro', 'Informe seu nome completo e telefone.');
        }

        $clienteModel = new ClienteModel();

        $dados = [
            'nome'        => $nome,
            'telefone'    => $telefone,
            'cep'         => trim((string) $this->request->getPost('cep')),
            'endereco'    => trim((string) $this->request->getPost('endereco')),
            'numero'      => trim((string) $this->request->getPost('numero')),
            'bairro'      => trim((string) $this->request->getPost('bairro')),
            'complemento' => trim((string) $this->request->getPost('complemento')),
            'cidade'      => trim((string) $this->request->getPost('cidade')),
        ];

        $novaSenha = (string) $this->request->getPost('nova_senha');
        if (! empty($novaSenha)) {
            if (strlen($novaSenha) < 4) {
                return redirect()->back()->with('erro', 'A nova senha deve ter pelo menos 4 caracteres.');
            }
            $dados['senha'] = password_hash($novaSenha, PASSWORD_DEFAULT);
        }

        $clienteModel->update($clienteId, $dados);
        session()->set([
            'cliente_nome'     => $dados['nome'],
            'cliente_telefone' => $dados['telefone'],
        ]);

        return redirect()->to('/cliente/perfil')->with('sucesso', 'Seus dados foram atualizados com sucesso!');
    }

    public function logout()
    {
        session()->remove(['cliente_id', 'cliente_nome', 'cliente_email', 'cliente_telefone', 'cliente_logado']);
        session()->regenerate(true);

        return redirect()->to('/')->with('sucesso', 'Você saiu da sua conta.');
    }
}
