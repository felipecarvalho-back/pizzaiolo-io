<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class AuthController extends BaseController
{
    public function login()
    {
        if (session()->get('logado')) {
            return redirect()->to('/painel');
        }

        return view('auth/login');
    }

    public function autenticar()
    {
        $email = trim((string) $this->request->getPost('email'));
        $senha = (string) $this->request->getPost('senha');

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->buscarPorEmail($email);

        if (! $usuario || ! $usuario->verificarSenha($senha)) {
            return redirect()->back()
                ->withInput()
                ->with('erro', 'E-mail ou senha inválidos.');
        }

        // Atualiza último acesso
        $usuarioModel->update($usuario->id, ['ultimo_acesso' => date('Y-m-d H:i:s')]);

        session()->set([
            'usuario_id'    => $usuario->id,
            'usuario_nome'  => $usuario->nome,
            'usuario_email' => $usuario->email,
            'usuario_cargo' => $usuario->cargo,
            'logado'        => true,
        ]);

        return redirect()->to('/painel')->with('sucesso', 'Bem-vindo de volta, ' . $usuario->nome . '!');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('sucesso', 'Sessão encerrada com segurança.');
    }
}
