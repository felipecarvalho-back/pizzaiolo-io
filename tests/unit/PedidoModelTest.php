<?php

namespace Tests\Unit;

use App\Models\ClienteModel;
use App\Models\PedidoItemModel;
use App\Models\PedidoItemSaborModel;
use App\Models\PedidoModel;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Services;

/**
 * @internal
 */
final class PedidoModelTest extends CIUnitTestCase
{
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
}
