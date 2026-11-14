<?php

namespace Tests\Unit;

use App\Models\CategoriaModel;
use App\Models\ClienteModel;
use App\Models\PedidoItemModel;
use App\Models\PedidoItemSaborModel;
use App\Models\PedidoModel;
use App\Models\ProdutoModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Config\Services;

/**
 * @internal
 */
final class PedidoModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $namespace = 'App';
    public function testPedidoModelAllowsClienteId(): void
    {
        $ref = new \ReflectionClass(PedidoModel::class);
        $defaults = $ref->getDefaultProperties();
        $allowed = $defaults['allowedFields'] ?? [];

        $this->assertContains('cliente_id', $allowed, 'PedidoModel must have cliente_id in allowedFields');
        $this->assertContains('codigo', $allowed);
        $this->assertContains('total_geral', $allowed);
    }

    public function testPedidoItemModelAllowsObservacao(): void
    {
        $ref = new \ReflectionClass(PedidoItemModel::class);
        $defaults = $ref->getDefaultProperties();
        $allowed = $defaults['allowedFields'] ?? [];

        $this->assertContains('observacao', $allowed, 'PedidoItemModel must have observacao in allowedFields');
        $this->assertContains('tamanho_id', $allowed);
        $this->assertContains('borda_id', $allowed);
    }

    public function testPedidoItemSaborModelAllowsSabores(): void
    {
        $ref = new \ReflectionClass(PedidoItemSaborModel::class);
        $defaults = $ref->getDefaultProperties();
        $allowed = $defaults['allowedFields'] ?? [];

        $this->assertContains('pedido_item_id', $allowed);
        $this->assertContains('produto_id', $allowed);
    }

    public function testClienteModelAllowsCepAndCidade(): void
    {
        $ref = new \ReflectionClass(ClienteModel::class);
        $defaults = $ref->getDefaultProperties();
        $allowed = $defaults['allowedFields'] ?? [];

        $this->assertContains('cep', $allowed);
        $this->assertContains('cidade', $allowed);
    }

    public function testPainelPedidoStatusAndSafePostRoutesExist(): void
    {
        $routes = Services::routes();
        $postRoutes = $routes->getRoutes('POST');
        $getRoutes  = $routes->getRoutes('GET');

        $this->assertArrayHasKey('painel/pedido/status', $postRoutes);
        $this->assertSame('\App\Controllers\PainelController::atualizarStatusPedido', $postRoutes['painel/pedido/status']);

        // Exclusões devem ser exclusivamente via POST e nunca via GET
        $this->assertArrayHasKey('painel/produto/excluir/([0-9]+)', $postRoutes);
        $this->assertArrayHasKey('painel/usuario/excluir/([0-9]+)', $postRoutes);
        $this->assertArrayNotHasKey('painel/produto/excluir/([0-9]+)', $getRoutes);
        $this->assertArrayNotHasKey('painel/usuario/excluir/([0-9]+)', $getRoutes);
    }

    public function testGerarCodigoUnicoEPersistenciaMeioAMeioNoBanco(): void
    {
        $categoriaModel = new CategoriaModel();
        $catId = (int) $categoriaModel->insert([
            'nome'  => 'Clássicas Teste',
            'slug'  => 'classicas-teste-' . random_int(100, 999),
            'ordem' => 1,
            'ativo' => 1,
        ]);

        $produtoModel = new ProdutoModel();
        $sabor1Id = (int) $produtoModel->insert([
            'categoria_id' => $catId,
            'nome'         => 'Margherita Teste',
            'slug'         => 'margherita-teste-' . random_int(100, 999),
            'tipo'         => 'pizza',
            'preco_base'   => 68.00,
            'ativo'        => 1,
        ]);
        $sabor2Id = (int) $produtoModel->insert([
            'categoria_id' => $catId,
            'nome'         => 'Pepperoni Teste',
            'slug'         => 'pepperoni-teste-' . random_int(100, 999),
            'tipo'         => 'pizza',
            'preco_base'   => 76.00,
            'ativo'        => 1,
        ]);

        $pedidoModel = new PedidoModel();
        $codigo = $pedidoModel->gerarCodigoUnico();
        $this->assertMatchesRegularExpression('/^#FORNO-[A-F0-9]{4}$/', $codigo);

        $pedidoId = (int) $pedidoModel->insert([
            'codigo'           => $codigo,
            'cliente_nome'     => 'Maria Napolitana',
            'cliente_telefone' => '(11) 98888-7777',
            'tipo_entrega'     => 'delivery',
            'endereco'         => 'Rua Roma, 45',
            'forma_pagamento'  => 'pix',
            'status'           => 'pendente',
            'status_pagamento' => 'aguardando',
            'total_produtos'   => 76.00,
            'taxa_entrega'     => 5.00,
            'total_geral'      => 73.40,
            'observacoes'      => 'Desconto PIX 10%: -R$ 7,60',
        ]);
        $this->assertGreaterThan(0, $pedidoId);

        $itemModel = new PedidoItemModel();
        $itemId = (int) $itemModel->insert([
            'pedido_id'      => $pedidoId,
            'produto_id'     => $sabor1Id,
            'quantidade'     => 1,
            'preco_unitario' => 76.00,
            'subtotal'       => 76.00,
            'observacao'     => 'Sabores: 1/2 Margherita Teste + 1/2 Pepperoni Teste • Borda: Tradicional',
        ]);
        $this->assertGreaterThan(0, $itemId);

        $saborModel = new PedidoItemSaborModel();
        $saborModel->insert(['pedido_item_id' => $itemId, 'produto_id' => $sabor1Id]);
        $saborModel->insert(['pedido_item_id' => $itemId, 'produto_id' => $sabor2Id]);

        $saboresSalvos = $saborModel->where('pedido_item_id', $itemId)->findAll();
        $this->assertCount(2, $saboresSalvos);
        $this->assertSame($sabor1Id, (int) $saboresSalvos[0]['produto_id']);
        $this->assertSame($sabor2Id, (int) $saboresSalvos[1]['produto_id']);

        $pedidoLocalizado = $pedidoModel->findByCodigo($codigo);
        $this->assertNotNull($pedidoLocalizado);
        $this->assertSame('Maria Napolitana', $pedidoLocalizado->cliente_nome);
    }
}
