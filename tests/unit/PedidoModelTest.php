<?php

namespace Tests\Unit;

use App\Models\PedidoItemModel;
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

    public function testPainelPedidoStatusRouteExists(): void
    {
        $routes = Services::routes();
        $definedRoutes = $routes->getRoutes('POST');

        $this->assertArrayHasKey('painel/pedido/status', $definedRoutes);
        $this->assertSame('\App\Controllers\PainelController::atualizarStatusPedido', $definedRoutes['painel/pedido/status']);
    }
}
