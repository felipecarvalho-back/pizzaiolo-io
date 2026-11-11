<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class CarrinhoSanitizationTest extends CIUnitTestCase
{
    /**
     * Testa o algoritmo de sanitização de troco decimal implementado no CarrinhoController
     */
    public function testTrocoDecimalParsing(): void
    {
        $parseTroco = function (?string $pagamento, ?string $trocoParaRaw): ?float {
            $trocoPara = null;
            if ($pagamento === 'dinheiro' && !empty($trocoParaRaw)) {
                $raw = trim((string) $trocoParaRaw);
                $raw = preg_replace('/[^\d.,]/', '', $raw);
                if (str_contains($raw, ',')) {
                    $raw = str_replace('.', '', $raw);
                    $raw = str_replace(',', '.', $raw);
                }
                if (is_numeric($raw)) {
                    $trocoPara = round((float) $raw, 2);
                }
            }
            return $trocoPara;
        };

        // Pagamento não-dinheiro deve sempre resultar em null
        $this->assertNull($parseTroco('pix', '100,00'));
        $this->assertNull($parseTroco('cartao_credito', '150.00'));
        $this->assertNull($parseTroco('cartao_debito', '50,00'));

        // Dinheiro com formatação brasileira (vírgula)
        $this->assertSame(100.50, $parseTroco('dinheiro', 'R$ 100,50'));
        $this->assertSame(1000.50, $parseTroco('dinheiro', '1.000,50'));

        // Dinheiro com formatação decimal pura (ponto)
        $this->assertSame(75.25, $parseTroco('dinheiro', '75.25'));

        // Dinheiro número inteiro
        $this->assertSame(50.0, $parseTroco('dinheiro', '50'));

        // Inválido / vazio
        $this->assertNull($parseTroco('dinheiro', ''));
        $this->assertNull($parseTroco('dinheiro', 'abc'));
    }

    /**
     * Testa a formatação de endereço para entrega na mesa
     */
    public function testEntregaMesaFormatting(): void
    {
        $formatEndereco = function (string $entrega, ?string $mesaInput, string $enderecoInput): string {
            $endereco = trim($enderecoInput);
            $mesa = trim((string) $mesaInput);
            if ($entrega === 'mesa' && !empty($mesa)) {
                $endereco = 'Mesa ' . $mesa . (!empty($endereco) ? ' — ' . $endereco : '');
            }
            return $endereco;
        };

        // Entrega na mesa sem endereço adicional
        $this->assertSame('Mesa 04', $formatEndereco('mesa', '04', ''));

        // Entrega na mesa com detalhe de localização
        $this->assertSame('Mesa 12 — Varanda externa', $formatEndereco('mesa', '12', 'Varanda externa'));

        // Delivery normal não deve adicionar prefixo de mesa
        $this->assertSame('Rua das Flores, 123', $formatEndereco('delivery', '12', 'Rua das Flores, 123'));
    }

    /**
     * Testa a regra de que Broto nunca pode receber meio-a-meio
     */
    public function testBrotoNeverAllowsHalfAndHalf(): void
    {
        $canSplitFlavors = function (string $tamanhoNome, int $sabor2Id, int $produtoId): bool {
            return ($tamanhoNome !== 'Broto' && $sabor2Id > 0 && $sabor2Id !== $produtoId);
        };

        $this->assertFalse($canSplitFlavors('Broto', 2, 1), 'Broto não pode aceitar segundo sabor');
        $this->assertTrue($canSplitFlavors('Média', 2, 1), 'Média pode aceitar segundo sabor');
        $this->assertTrue($canSplitFlavors('Grande', 2, 1), 'Grande pode aceitar segundo sabor');
        $this->assertFalse($canSplitFlavors('Grande', 1, 1), 'Mesmo sabor não deve acionar meio-a-meio');
        $this->assertFalse($canSplitFlavors('Grande', 0, 1), 'Sem segundo sabor não deve acionar meio-a-meio');
    }
}
