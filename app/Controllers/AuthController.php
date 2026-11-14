<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\UsuarioModel;

class AuthController extends BaseController
{
    /**
     * Valida URL de redirecionamento pós-login prevenindo vulnerabilidades de Open Redirect.
     */
    public static function validarRedirect(?string $redirectUrl): ?string
    {
        $url = trim((string) $redirectUrl);
        if ($url === '') {
            return null;
        }

        if (
            ! str_starts_with($url, '/')
            || str_starts_with($url, '//')
            || str_starts_with($url, '/\\')
            || str_contains($url, '://')
            || preg_match('/[\r\n]/', $url)
        ) {
            return null;
        }

        return $url;
    }

    /**
     * Tela de Login Única (para Clientes e Administradores)
     */
    public function login()
    {
        if (session()->get('logado')) {
            return redirect()->to('/painel');
        }

        if (session()->get('cliente_logado')) {
            return redirect()->to('/cliente/perfil');
        }

        return view('auth/login', [
            'redirect' => self::validarRedirect((string) $this->request->getGet('redirect')) ?? '',
        ]);
    }

    /**
     * Autenticação Inteligente:
     * 1. Se for Administrador/Equipe -> Painel (/painel)
     * 2. Se for Cliente -> Espaço do Cliente (/cliente/perfil ou cardápio)
     */
    public function autenticar()
    {
        $email = trim((string) $this->request->getPost('email'));
        $senha = (string) $this->request->getPost('senha');

        if (empty($email) || empty($senha)) {
            return redirect()->back()->withInput()->with('erro', 'Informe seu e-mail e senha.');
        }

        // 1. Tenta como Administrador / Equipe
        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->buscarPorEmail($email);

        if ($usuario && $usuario->verificarSenha($senha)) {
            $usuarioModel->update($usuario->id, ['ultimo_acesso' => date('Y-m-d H:i:s')]);

            session()->regenerate(true);
            session()->remove(['cliente_id', 'cliente_nome', 'cliente_email', 'cliente_telefone', 'cliente_logado']);
            session()->set([
                'usuario_id'    => $usuario->id,
                'usuario_nome'  => $usuario->nome,
                'usuario_email' => $usuario->email,
                'usuario_cargo' => $usuario->cargo ?: 'admin',
                'logado'        => true,
            ]);

            return redirect()->to('/painel')->with('sucesso', 'Bem-vindo ao Painel, ' . $usuario->nome . '!');
        }

        // 2. Se não for admin, tenta como Cliente
        $clienteModel = new ClienteModel();
        $cliente = $clienteModel->buscarPorEmail($email);

        if ($cliente && $cliente->verificarSenha($senha)) {
            session()->regenerate(true);
            session()->remove(['usuario_id', 'usuario_nome', 'usuario_email', 'usuario_cargo', 'logado']);
            session()->set([
                'cliente_id'       => $cliente->id,
                'cliente_nome'     => $cliente->nome,
                'cliente_email'    => $cliente->email,
                'cliente_telefone' => $cliente->telefone,
                'cliente_logado'   => true,
            ]);

            $redirectUrl = self::validarRedirect((string) $this->request->getPost('redirect'));
            if ($redirectUrl !== null) {
                return redirect()->to($redirectUrl)->with('sucesso', 'Olá, ' . explode(' ', $cliente->nome)[0] . '!');
            }

            return redirect()->to('/cliente/perfil')->with('sucesso', 'Bem-vindo ao seu espaço, ' . explode(' ', $cliente->nome)[0] . '!');
        }

        // 3. Credenciais inválidas em ambos
        return redirect()->back()
            ->withInput()
            ->with('erro', 'E-mail ou senha incorretos.');
    }

    /**
     * Logout seguro preservando a sacola de compras na sessão
     */
    public function logout()
    {
        session()->remove([
            'usuario_id',
            'usuario_nome',
            'usuario_email',
            'usuario_cargo',
            'logado',
            'cliente_id',
            'cliente_nome',
            'cliente_email',
            'cliente_telefone',
            'cliente_logado',
        ]);
        session()->regenerate(true);

        return redirect()->to('/login')->with('sucesso', 'Sua sessão foi encerrada.');
    }
}
