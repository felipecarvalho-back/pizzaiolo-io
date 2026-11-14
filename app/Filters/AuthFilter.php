<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Verifica se o membro da equipe está autenticado e possui o cargo exigido (RBAC).
     *
     * @param list<string>|null $arguments
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('logado')) {
            if ($request instanceof IncomingRequest && $request->isAJAX()) {
                return service('response')
                    ->setStatusCode(401)
                    ->setJSON(['sucesso' => false, 'erro' => 'Sessão expirada. Faça login novamente.']);
            }

            return redirect()->to('/login')->with('erro', 'Faça login para acessar o painel.');
        }

        if (! empty($arguments)) {
            $cargoUsuario = (string) (session()->get('usuario_cargo') ?: 'admin');
            if (! in_array($cargoUsuario, $arguments, true)) {
                if ($request instanceof IncomingRequest && $request->isAJAX()) {
                    return service('response')
                        ->setStatusCode(403)
                        ->setJSON(['sucesso' => false, 'erro' => 'Você não tem permissão para realizar esta ação.']);
                }

                return redirect()->to('/painel')->with('erro', 'Acesso restrito: seu cargo não permite realizar esta operação.');
            }
        }

        return null;
    }

    /**
     * @param list<string>|null $arguments
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
