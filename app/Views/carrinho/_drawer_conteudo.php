<?php
$qtd = count($carrinho);
$subtotal = array_sum(array_column($carrinho, 'preco'));
$taxa = ($qtd > 0) ? 5.00 : 0.00;
$total = $subtotal + $taxa;
?>

<div class="p-4 overflow-y-auto flex-1 space-y-4">
    <!-- Lista de Itens -->
    <div class="space-y-2">
        <?php if ($qtd === 0): ?>
            <div class="text-center py-10 text-stone-400 text-xs">
                Sua sacola está vazia. Adicione uma pizza artesanal!
            </div>
        <?php else: ?>
            <?php foreach ($carrinho as $idx => $item): ?>
                <div class="p-3 bg-stone-50 rounded-xl border border-stone-200/80 flex items-center justify-between gap-2">
                    <div class="min-w-0 flex-1">
                        <span class="font-medium text-stone-900 text-xs block truncate"><?= esc($item['titulo']) ?></span>
                        <span class="text-[11px] text-stone-500 block truncate"><?= esc($item['detalhes']) ?></span>
                        <span class="text-xs font-bold text-stone-900 block mt-0.5">R$ <?= number_format((float) $item['preco'], 2, ',', '.') ?></span>
                    </div>
                    <!-- Botão de Remover com HTMX puro -->
                    <button hx-post="<?= base_url('carrinho/remover/' . $idx) ?>" 
                            hx-target="#drawer-conteudo" 
                            hx-swap="innerHTML"
                            class="w-7 h-7 rounded-lg bg-stone-200 hover:bg-red-100 hover:text-red-700 text-stone-600 flex items-center justify-center text-xs transition">
                        ✕
                    </button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Formulário de Checkout com HTMX -->
    <?php if ($qtd > 0): ?>
        <form id="form-finalizar-pedido" 
              hx-post="<?= base_url('carrinho/finalizar') ?>" 
              hx-target="#drawer-conteudo" 
              hx-swap="innerHTML"
              class="pt-4 border-t border-stone-200 space-y-3">
            
            <h4 class="font-serif font-bold text-sm text-stone-900">Dados para Entrega</h4>

            <div class="space-y-2 text-xs">
                <div>
                    <label class="block text-[11px] font-medium text-stone-600 mb-0.5">Nome Completo *</label>
                    <input type="text" name="nome" value="<?= esc($cliente->nome ?? '') ?>" required placeholder="Seu nome"
                           class="w-full bg-stone-50 border border-stone-200 rounded-xl p-2.5 text-stone-900 focus:outline-none focus:border-brand-800" />
                </div>

                <div>
                    <label class="block text-[11px] font-medium text-stone-600 mb-0.5">WhatsApp para aviso *</label>
                    <input type="tel" name="telefone" value="<?= esc($cliente->telefone ?? '') ?>" required placeholder="(11) 99999-9999"
                           class="w-full bg-stone-50 border border-stone-200 rounded-xl p-2.5 text-stone-900 focus:outline-none focus:border-brand-800" />
                </div>

                <div>
                    <label class="block text-[11px] font-medium text-stone-600 mb-0.5">Endereço de Entrega</label>
                    <input type="text" name="endereco" value="<?= esc($cliente ? $cliente->getEnderecoCompleto() : '') ?>" placeholder="Rua, número e bairro"
                           class="w-full bg-stone-50 border border-stone-200 rounded-xl p-2.5 text-stone-900 focus:outline-none focus:border-brand-800" />
                </div>

                <input type="hidden" name="tipo_entrega" id="input-tipo-entrega" value="delivery" />
            </div>

            <div>
                <span class="text-xs font-semibold text-stone-700 block mb-1.5">Forma de Pagamento</span>
                <div class="grid grid-cols-3 gap-1.5">
                    <label class="cursor-pointer border border-stone-200 p-2 rounded-xl text-center has-[:checked]:border-brand-800 has-[:checked]:bg-brand-50 transition">
                        <input type="radio" name="forma_pagamento" value="pix" checked class="hidden" />
                        <span class="font-bold text-xs text-stone-900 block">PIX</span>
                        <span class="text-[10px] text-emerald-700 font-semibold block">-10% OFF</span>
                    </label>
                    <label class="cursor-pointer border border-stone-200 p-2 rounded-xl text-center has-[:checked]:border-brand-800 has-[:checked]:bg-brand-50 transition">
                        <input type="radio" name="forma_pagamento" value="cartao_credito" class="hidden" />
                        <span class="font-bold text-xs text-stone-900 block">Cartão</span>
                        <span class="text-[10px] text-stone-500 block">Na entrega</span>
                    </label>
                    <label class="cursor-pointer border border-stone-200 p-2 rounded-xl text-center has-[:checked]:border-brand-800 has-[:checked]:bg-brand-50 transition">
                        <input type="radio" name="forma_pagamento" value="dinheiro" class="hidden" />
                        <span class="font-bold text-xs text-stone-900 block">Dinheiro</span>
                        <span class="text-[10px] text-stone-500 block">Com troco</span>
                    </label>
                </div>
            </div>

            <!-- Resumo Financeiro -->
            <div class="pt-3 border-t border-stone-200 space-y-1 text-xs">
                <div class="flex justify-between text-stone-500">
                    <span>Subtotal</span>
                    <span>R$ <?= number_format($subtotal, 2, ',', '.') ?></span>
                </div>
                <div class="flex justify-between text-stone-500">
                    <span>Taxa de Entrega</span>
                    <span>R$ <?= number_format($taxa, 2, ',', '.') ?></span>
                </div>
                <div class="flex justify-between text-sm font-bold text-stone-900 pt-1 border-t border-stone-200">
                    <span>Total Final</span>
                    <span class="text-brand-900">R$ <?= number_format($total, 2, ',', '.') ?></span>
                </div>
            </div>

            <button type="submit" class="w-full bg-brand-800 hover:bg-brand-900 text-white font-semibold py-3 rounded-xl text-sm transition shadow-sm mt-3">
                Confirmar e Enviar Pedido
            </button>
        </form>
    <?php endif; ?>
</div>
