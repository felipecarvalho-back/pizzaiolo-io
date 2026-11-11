<?php
$qtd = count($carrinho);
$subtotal = array_sum(array_column($carrinho, 'preco'));
?>

<div x-data="drawerCheckout({ subtotal: <?= (float) $subtotal ?> })" 
     class="p-4 overflow-y-auto flex-1 space-y-4">
    <!-- Lista de Itens -->
    <div class="space-y-2">
        <?php if ($qtd === 0): ?>
            <div class="text-center py-12 text-stone-400 text-xs flex flex-col items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[36px] text-stone-300">shopping_basket</span>
                <span>Sua sacola está vazia. Adicione uma pizza artesanal!</span>
            </div>
        <?php else: ?>
            <?php foreach ($carrinho as $idx => $item): ?>
                <div class="p-3 bg-stone-50 rounded-xl border border-stone-200/80 flex items-center justify-between gap-2 shadow-2xs">
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

    <!-- Formulário de Checkout com HTMX e Reatividade Alpine -->
    <?php if ($qtd > 0): ?>
        <form id="form-finalizar-pedido" 
              hx-post="<?= base_url('carrinho/finalizar') ?>" 
              hx-target="#drawer-conteudo" 
              hx-swap="innerHTML"
              class="pt-4 border-t border-stone-200 space-y-3.5">
            
            <div class="flex items-center justify-between">
                <h4 class="font-serif font-bold text-sm text-stone-900">Modalidade & Destino</h4>
                <!-- Pílulas de seleção de modalidade dentro da sacola -->
                <div class="inline-flex p-0.5 bg-stone-100 rounded-lg text-[11px] font-semibold">
                    <button type="button" 
                            @click="mudarModo('delivery')" 
                            :class="tipoEntrega === 'delivery' ? 'bg-stone-900 text-white shadow-xs' : 'text-stone-600 hover:text-stone-900'"
                            class="px-2.5 py-1 rounded-md transition">
                        Entrega
                    </button>
                    <button type="button" 
                            @click="mudarModo('retirada')" 
                            :class="tipoEntrega === 'retirada' ? 'bg-stone-900 text-white shadow-xs' : 'text-stone-600 hover:text-stone-900'"
                            class="px-2.5 py-1 rounded-md transition">
                        Retirada
                    </button>
                    <button type="button" 
                            @click="mudarModo('mesa')" 
                            :class="tipoEntrega === 'mesa' ? 'bg-stone-900 text-white shadow-xs' : 'text-stone-600 hover:text-stone-900'"
                            class="px-2.5 py-1 rounded-md transition">
                        Na Mesa
                    </button>
                </div>
            </div>

            <div class="space-y-2.5 text-xs">
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

                <!-- Campo de Endereço: visível apenas se tipoEntrega === 'delivery' -->
                <div x-show="tipoEntrega === 'delivery'" x-transition>
                    <label class="block text-[11px] font-medium text-stone-600 mb-0.5">Endereço de Entrega *</label>
                    <input type="text" name="endereco" :required="tipoEntrega === 'delivery'" value="<?= esc($cliente ? $cliente->getEnderecoCompleto() : '') ?>" placeholder="Rua, número e bairro"
                           class="w-full bg-stone-50 border border-stone-200 rounded-xl p-2.5 text-stone-900 focus:outline-none focus:border-brand-800" />
                </div>

                <!-- Campo para Número da Mesa se tipoEntrega === 'mesa' -->
                <div x-show="tipoEntrega === 'mesa'" x-transition>
                    <label class="block text-[11px] font-medium text-stone-600 mb-0.5">Número da Mesa *</label>
                    <input type="text" name="mesa" :required="tipoEntrega === 'mesa'" placeholder="Ex: Mesa 04 (Salão Principal)"
                           class="w-full bg-stone-50 border border-stone-200 rounded-xl p-2.5 text-stone-900 focus:outline-none focus:border-brand-800" />
                </div>

                <!-- Aviso para Retirada no Balcão -->
                <div x-show="tipoEntrega === 'retirada'" x-transition class="p-2.5 rounded-xl bg-stone-100 text-stone-700 text-[11px] flex items-center gap-2">
                    <span class="material-symbols-outlined text-[16px] text-stone-500">storefront</span>
                    <span>Retirada no balcão da pizzaria (Sem cobrança de taxa).</span>
                </div>

                <input type="hidden" name="tipo_entrega" id="input-tipo-entrega" :value="tipoEntrega" />
            </div>

            <div>
                <span class="text-xs font-semibold text-stone-700 block mb-1.5">Forma de Pagamento</span>
                <div class="grid grid-cols-3 gap-1.5">
                    <label :class="formaPagamento === 'pix' ? 'border-brand-800 bg-brand-50 shadow-xs' : 'border-stone-200'"
                           class="cursor-pointer border p-2 rounded-xl text-center transition">
                        <input type="radio" name="forma_pagamento" value="pix" x-model="formaPagamento" class="hidden" />
                        <span class="font-bold text-xs text-stone-900 block">PIX</span>
                        <span class="text-[10px] text-emerald-700 font-semibold block">-10% OFF</span>
                    </label>
                    <label :class="formaPagamento === 'cartao_credito' ? 'border-brand-800 bg-brand-50 shadow-xs' : 'border-stone-200'"
                           class="cursor-pointer border p-2 rounded-xl text-center transition">
                        <input type="radio" name="forma_pagamento" value="cartao_credito" x-model="formaPagamento" class="hidden" />
                        <span class="font-bold text-xs text-stone-900 block">Cartão</span>
                        <span class="text-[10px] text-stone-500 block">Na entrega</span>
                    </label>
                    <label :class="formaPagamento === 'dinheiro' ? 'border-brand-800 bg-brand-50 shadow-xs' : 'border-stone-200'"
                           class="cursor-pointer border p-2 rounded-xl text-center transition">
                        <input type="radio" name="forma_pagamento" value="dinheiro" x-model="formaPagamento" class="hidden" />
                        <span class="font-bold text-xs text-stone-900 block">Dinheiro</span>
                        <span class="text-[10px] text-stone-500 block">Com troco</span>
                    </label>
                </div>

                <!-- Campo dinâmico: Revela troco apenas quando selecionado 'Dinheiro' -->
                <div x-show="formaPagamento === 'dinheiro'" x-transition class="mt-2.5">
                    <label class="block text-[11px] font-medium text-stone-600 mb-0.5">Troco para quanto?</label>
                    <input type="text" name="troco_para" x-model="trocoPara" placeholder="Ex: R$ 50,00 ou Não preciso"
                           class="w-full bg-stone-50 border border-stone-200 rounded-xl p-2.5 text-xs text-stone-900 placeholder:text-stone-400 focus:outline-none focus:border-brand-800" />
                </div>
            </div>

            <!-- Resumo Financeiro Dinâmico -->
            <div class="pt-3 border-t border-stone-200 space-y-1 text-xs">
                <div class="flex justify-between text-stone-500">
                    <span>Subtotal dos Itens</span>
                    <span>R$ <?= number_format($subtotal, 2, ',', '.') ?></span>
                </div>
                <div class="flex justify-between text-stone-500">
                    <span>Taxa de Entrega</span>
                    <span x-text="taxaEntregaFormatada">R$ 5,00</span>
                </div>
                <div class="flex justify-between text-sm font-bold text-stone-900 pt-1.5 border-t border-stone-200">
                    <span>Total Final</span>
                    <span class="text-brand-900">R$ <span x-text="totalFinalFormatado"></span></span>
                </div>
            </div>

            <button type="submit" class="w-full bg-brand-800 hover:bg-brand-900 active:scale-[0.98] text-white font-semibold py-3 rounded-xl text-sm transition shadow-sm mt-3 flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                <span>Confirmar e Enviar Pedido</span>
            </button>
        </form>
    <?php endif; ?>
</div>
