<?php

namespace App\Controllers;

use App\Models\BordaModel;
use App\Models\CategoriaModel;
use App\Models\PedidoItemModel;
use App\Models\PedidoModel;
use App\Models\ProdutoModel;
use App\Models\TamanhoModel;
use App\Models\UsuarioModel;

class PainelController extends BaseController
{
    private function verificarAcesso()
    {
        if (! session()->get('logado')) {
            return redirect()->to('/login')->with('erro', 'Faça login para acessar o painel.');
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->verificarAcesso()) {
            return $redirect;
        }

        $produtoModel   = new ProdutoModel();
        $categoriaModel = new CategoriaModel();
        $bordaModel     = new BordaModel();
        $tamanhoModel   = new TamanhoModel();
        $usuarioModel   = new UsuarioModel();
        $pedidoModel    = new PedidoModel();
        $pedidoItemModel = new PedidoItemModel();

        $pedidos = $pedidoModel->orderBy('id', 'DESC')->findAll(50);
        foreach ($pedidos as $p) {
            $p->itens = $pedidoItemModel->select('pedido_itens.*, produtos.nome as produto_nome, tamanhos.nome as tamanho_nome, bordas.nome as borda_nome')
                                       ->join('produtos', 'produtos.id = pedido_itens.produto_id', 'left')
                                       ->join('tamanhos', 'tamanhos.id = pedido_itens.tamanho_id', 'left')
                                       ->join('bordas', 'bordas.id = pedido_itens.borda_id', 'left')
                                       ->where('pedido_itens.pedido_id', $p->id)
                                       ->findAll();
        }

        $dados = [
            'produtos'   => $produtoModel->select('produtos.*, categorias.nome as categoria_nome')
                                         ->join('categorias', 'categorias.id = produtos.categoria_id', 'left')
                                         ->orderBy('produtos.id', 'DESC')
                                         ->findAll(),
            'categorias' => $categoriaModel->orderBy('ordem', 'ASC')->findAll(),
            'bordas'     => $bordaModel->orderBy('id', 'ASC')->findAll(),
            'tamanhos'   => $tamanhoModel->orderBy('ordem', 'ASC')->findAll(),
            'usuarios'   => $usuarioModel->orderBy('id', 'DESC')->findAll(),
            'pedidos'    => $pedidos,
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
        $pedido = $pedidoModel->find($pedidoId);
        if (! $pedido) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(404)->setJSON(['sucesso' => false, 'erro' => 'Pedido não encontrado.']);
            }
            return redirect()->to('/painel?aba=pedidos')->with('erro', 'Pedido não encontrado.');
        }

        $pedidoModel->update($pedidoId, ['status' => $novoStatus]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'sucesso'     => true,
                'mensagem'    => "Status do pedido {$pedido->codigo} atualizado para {$novoStatus}.",
                'pedido_id'   => $pedidoId,
                'novo_status' => $novoStatus,
            ]);
        }

        return redirect()->to('/painel?aba=pedidos')->with('sucesso', "Status do pedido {$pedido->codigo} atualizado com sucesso!");
    }

    public function salvarProduto()
    {
        if ($redirect = $this->verificarAcesso()) {
            return $redirect;
        }

        $nome        = trim((string) $this->request->getPost('nome'));
        $categoriaId = (int) $this->request->getPost('categoria_id');
        $tipo        = (string) $this->request->getPost('tipo');
        $precoBase   = (float) str_replace(',', '.', (string) $this->request->getPost('preco_base'));
        $descricao   = trim((string) $this->request->getPost('descricao'));
        $imagem      = trim((string) $this->request->getPost('imagem'));
        $destaque    = $this->request->getPost('destaque') ? 1 : 0;

        helper('text');
        $slug = url_title(convert_accented_characters($nome), '-', true) . '-' . rand(10, 99);

        if (empty($imagem)) {
            $imagem = 'https://lh3.googleusercontent.com/aida-public/AB6AXuDXCJbgOLcySP9RMhpEMCnfbTazeLvI2c-l0JAIyzewcdULRuBPqbtiNNssi44lRnaGQFSf5-qknWyHGDRMzlvSdSMRO66RCgqnvrz5P3Udpc_3DRHftlvK5AzUrJPbxhyrNB0D_lDBdRIQjL-4c7e0Y5mgbCgyq49lqYD7yzlsm3OpDWbixitV2K9rFajtArgCnMC6X11_kimu13zUAYt8CTxblGoq4VaT4j2Dxaw';
        }

        $produtoModel = new ProdutoModel();
        $produtoModel->skipValidation(true)->insert([
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

    public function excluirProduto(int $id)
    {
        if ($redirect = $this->verificarAcesso()) {
            return $redirect;
        }

        $produtoModel = new ProdutoModel();
        $produtoModel->delete($id);

        return redirect()->to('/painel?aba=produtos')->with('sucesso', 'Produto removido com sucesso.');
    }

    public function salvarCategoria()
    {
        if ($redirect = $this->verificarAcesso()) {
            return $redirect;
        }

        $nome      = trim((string) $this->request->getPost('nome'));
        $descricao = trim((string) $this->request->getPost('descricao'));
        $ordem     = (int) $this->request->getPost('ordem');

        helper('text');
        $slug = url_title(convert_accented_characters($nome), '-', true);

        $categoriaModel = new CategoriaModel();
        $categoriaModel->skipValidation(true)->insert([
            'nome'      => $nome,
            'slug'      => $slug,
            'descricao' => $descricao,
            'ordem'     => $ordem,
            'ativo'     => 1,
        ]);

        return redirect()->to('/painel?aba=categorias')->with('sucesso', 'Categoria "' . $nome . '" cadastrada!');
    }

    public function salvarBorda()
    {
        if ($redirect = $this->verificarAcesso()) {
            return $redirect;
        }

        $nome  = trim((string) $this->request->getPost('nome'));
        $preco = (float) str_replace(',', '.', (string) $this->request->getPost('preco'));

        $bordaModel = new BordaModel();
        $bordaModel->skipValidation(true)->insert([
            'nome'  => $nome,
            'preco' => $preco,
            'ativo' => 1,
        ]);

        return redirect()->to('/painel?aba=bordas')->with('sucesso', 'Borda "' . $nome . '" cadastrada!');
    }

    public function salvarUsuario()
    {
        if ($redirect = $this->verificarAcesso()) {
            return $redirect;
        }

        $nome  = trim((string) $this->request->getPost('nome'));
        $email = trim((string) $this->request->getPost('email'));
        $senha = (string) $this->request->getPost('senha');
        $cargo = (string) $this->request->getPost('cargo');

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
        if ($redirect = $this->verificarAcesso()) {
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
