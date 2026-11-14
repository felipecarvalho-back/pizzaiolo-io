<?php

namespace Tests\Unit;

use App\Controllers\AuthController;
use App\Controllers\CarrinhoController;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class CarrinhoSanitizationTest extends CIUnitTestCase
{
    /**
     * Testa o algoritmo real de sanitização de troco decimal em CarrinhoController::sanitizarTroco()
     */
    public function testTrocoDecimalParsing(): void
    {
        // Pagamento não-dinheiro deve sempre resultar em null
        $this->assertNull(CarrinhoController::sanitizarTroco('pix', '100,00'));
        $this->assertNull(CarrinhoController::sanitizarTroco('cartao_credito', '150.00'));
        $this->assertNull(CarrinhoController::sanitizarTroco('cartao_debito', '50,00'));

        // Dinheiro com formatação brasileira (vírgula)
        $this->assertSame(100.50, CarrinhoController::sanitizarTroco('dinheiro', 'R$ 100,50'));
        $this->assertSame(1000.50, CarrinhoController::sanitizarTroco('dinheiro', '1.000,50'));

        // Dinheiro com formatação decimal pura (ponto)
        $this->assertSame(75.25, CarrinhoController::sanitizarTroco('dinheiro', '75.25'));

        // Dinheiro número inteiro
        $this->assertSame(50.0, CarrinhoController::sanitizarTroco('dinheiro', '50'));

        // Inválido / vazio / negativo
        $this->assertNull(CarrinhoController::sanitizarTroco('dinheiro', ''));
        $this->assertNull(CarrinhoController::sanitizarTroco('dinheiro', 'abc'));
        $this->assertNull(CarrinhoController::sanitizarTroco('dinheiro', '0'));
    }

    /**
     * Testa a formatação real de endereço para entrega na mesa / retirada / delivery
     */
    public function testEntregaMesaFormatting(): void
    {
        // Entrega na mesa sem endereço adicional
        $this->assertSame('Mesa 04', CarrinhoController::formatarEnderecoEntrega('mesa', '04', ''));

        // Entrega na mesa com detalhe de localização
        $this->assertSame('Mesa 12 — Varanda externa', CarrinhoController::formatarEnderecoEntrega('mesa', '12', 'Varanda externa'));

        // Retirada no balcão sem endereço informado
        $this->assertSame('Retirada no Balcão', CarrinhoController::formatarEnderecoEntrega('retirada', '', ''));

        // Delivery normal não deve adicionar prefixo de mesa
        $this->assertSame('Rua das Flores, 123', CarrinhoController::formatarEnderecoEntrega('delivery', '12', 'Rua das Flores, 123'));
    }

    /**
     * Testa a regra real de que Broto nunca pode receber meio-a-meio
     */
    public function testBrotoNeverAllowsHalfAndHalf(): void
    {
        $this->assertFalse(CarrinhoController::podeDividirSabores('Broto', 2, 1, 1), 'Broto não pode aceitar segundo sabor');
        $this->assertTrue(CarrinhoController::podeDividirSabores('Média', 2, 1, 2), 'Média pode aceitar segundo sabor');
        $this->assertTrue(CarrinhoController::podeDividirSabores('Grande', 2, 1, 2), 'Grande pode aceitar segundo sabor');
        $this->assertFalse(CarrinhoController::podeDividirSabores('Grande', 1, 1, 2), 'Mesmo sabor não deve acionar meio-a-meio');
        $this->assertFalse(CarrinhoController::podeDividirSabores('Grande', 0, 1, 2), 'Sem segundo sabor não deve acionar meio-a-meio');
    }

    /**
     * Testa o cálculo real de 10% de desconto no PIX e taxa de entrega
     */
    public function testCalculoTotaisPedidoComDescontoPix(): void
    {
        // PIX + Delivery: R$ 100,00 -> -R$ 10,00 (10%) + R$ 5,00 (frete) = R$ 95,00
        $totaisPixDelivery = CarrinhoController::calcularTotaisPedido(100.00, 'delivery', 'pix');
        $this->assertSame(100.00, $totaisPixDelivery['subtotal']);
        $this->assertSame(10.00, $totaisPixDelivery['desconto_pix']);
        $this->assertSame(5.00, $totaisPixDelivery['taxa_entrega']);
        $this->assertSame(95.00, $totaisPixDelivery['total_geral']);

        // Cartão + Retirada: R$ 68,00 -> sem desconto + sem frete = R$ 68,00
        $totaisCartaoRetirada = CarrinhoController::calcularTotaisPedido(68.00, 'retirada', 'cartao_credito');
        $this->assertSame(0.00, $totaisCartaoRetirada['desconto_pix']);
        $this->assertSame(0.00, $totaisCartaoRetirada['taxa_entrega']);
        $this->assertSame(68.00, $totaisCartaoRetirada['total_geral']);
    }

    /**
     * Testa o agrupamento de itens idênticos na sacola e contagem de unidades
     */
    public function testAgrupamentoEContagemDeItensNaSacola(): void
    {
        $carrinho = [];
        $itemPizza = [
            'produto_id'     => 1,
            'tamanho_id'     => 3,
            'borda_id'       => 2,
            'sabor_2_id'     => 4,
            'titulo'         => 'Pizza Grande (1/2 Margherita + 1/2 Calabresa)',
            'detalhes'       => 'Sabores: 1/2 Margherita + 1/2 Calabresa • Borda: Catupiry',
            'preco_unitario' => 80.00,
            'preco'          => 80.00,
        ];

        $carrinho = CarrinhoController::agruparOuAdicionarItem($carrinho, $itemPizza);
        $this->assertCount(1, $carrinho);
        $this->assertSame(1, $carrinho[0]['quantidade']);
        $this->assertSame(80.00, $carrinho[0]['preco']);

        // Adicionar exatamente a mesma pizza deve incrementar a quantidade para 2 e dobrar o subtotal
        $carrinho = CarrinhoController::agruparOuAdicionarItem($carrinho, $itemPizza);
        $this->assertCount(1, $carrinho);
        $this->assertSame(2, $carrinho[0]['quantidade']);
        $this->assertSame(160.00, $carrinho[0]['preco']);
        $this->assertSame(2, CarrinhoController::contarItensCarrinho($carrinho));
    }

    /**
     * Testa a proteção contra Open Redirect em AuthController::validarRedirect()
     */
    public function testProtecaoOpenRedirect(): void
    {
        $this->assertSame('/cliente/perfil', AuthController::validarRedirect('/cliente/perfil'));
        $this->assertSame('/', AuthController::validarRedirect('/'));
        $this->assertNull(AuthController::validarRedirect('https://evil.com'));
        $this->assertNull(AuthController::validarRedirect('//evil.com'));
        $this->assertNull(AuthController::validarRedirect('/\\evil.com'));
        $this->assertNull(AuthController::validarRedirect('javascript:alert(1)'));
    }
}
