<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\PedidoModel;

class ClienteController extends BaseController
{
    public function login()
    {
        if (session()->get('cliente_logado')) {
            return redirect()->to('/cliente/perfil');
        }

        return view('cliente/login');
    }

    public function autenticar()
    {
        $email = trim((string) $this->request->getPost('email'));
        $senha = (string) $this->request->getPost('senha');

        $clienteModel = new ClienteModel();
        $cliente = $clienteModel->buscarPorEmail($email);

        if (! $cliente || ! $cliente->verificarSenha($senha)) {
            return redirect()->back()->withInput()->with('erro', 'E-mail ou senha incorretos.');
        }

        session()->set([
            'cliente_id'       => $cliente->id,
            'cliente_nome'     => $cliente->nome,
            'cliente_email'    => $cliente->email,
            'cliente_telefone' => $cliente->telefone,
            'cliente_logado'   => true,
        ]);

        return redirect()->to('/')->with('sucesso', 'Olá, ' . explode(' ', $cliente->nome)[0] . '! Que bom te ver de volta.');
    }

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
            'endereco'    => trim((string) $this->request->getPost('endereco')),
            'numero'      => trim((string) $this->request->getPost('numero')),
            'bairro'      => trim((string) $this->request->getPost('bairro')),
            'complemento' => trim((string) $this->request->getPost('complemento')),
            'ativo'       => 1,
        ]);

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
            return redirect()->to('/cliente/login')->with('erro', 'Faça login para acessar seu perfil.');
        }

        $clienteModel = new ClienteModel();
        $cliente = $clienteModel->find(session()->get('cliente_id'));

        if (! $cliente) {
            session()->destroy();
            return redirect()->to('/cliente/login');
        }

        $pedidoModel = new PedidoModel();
        $pedidos = $pedidoModel->where('cliente_id', $cliente->id)
                               ->orderBy('id', 'DESC')
                               ->findAll();

        return view('cliente/perfil', [
            'cliente' => $cliente,
            'pedidos' => $pedidos,
        ]);
    }

    public function atualizarPerfil()
    {
        if (! session()->get('cliente_logado')) {
            return redirect()->to('/cliente/login');
        }

        $clienteId = (int) session()->get('cliente_id');
        $clienteModel = new ClienteModel();

        $dados = [
            'nome'        => trim((string) $this->request->getPost('nome')),
            'telefone'    => trim((string) $this->request->getPost('telefone')),
            'cep'         => trim((string) $this->request->getPost('cep')),
            'endereco'    => trim((string) $this->request->getPost('endereco')),
            'numero'      => trim((string) $this->request->getPost('numero')),
            'bairro'      => trim((string) $this->request->getPost('bairro')),
            'complemento' => trim((string) $this->request->getPost('complemento')),
        ];

        $novaSenha = (string) $this->request->getPost('nova_senha');
        if (! empty($novaSenha)) {
            $dados['senha'] = password_hash($novaSenha, PASSWORD_DEFAULT);
        }

        $clienteModel->update($clienteId, $dados);
        session()->set('cliente_nome', $dados['nome']);

        return redirect()->to('/cliente/perfil')->with('sucesso', 'Seus dados foram atualizados com sucesso!');
    }

    public function logout()
    {
        session()->remove(['cliente_id', 'cliente_nome', 'cliente_email', 'cliente_telefone', 'cliente_logado']);
        return redirect()->to('/')->with('sucesso', 'Você saiu da sua conta.');
    }
}
