<?php
$qtdTotal = \App\Controllers\CarrinhoController::contarItensCarrinho($carrinho);
$subtotal = (float) array_sum(array_column($carrinho, 'preco'));

$inputNome      = $formInput['nome'] ?? ($cliente->nome ?? '');
$inputTelefone  = $formInput['telefone'] ?? ($cliente->telefone ?? '');
$inputEndereco  = $formInput['endereco'] ?? ($cliente ? $cliente->getEnderecoCompleto() : '');
$inputMesa      = $formInput['mesa'] ?? '';
$inputPagamento = $formInput['forma_pagamento'] ?? '';
$inputEntrega   = $formInput['tipo_entrega'] ?? '';
$inputTroco     = $formInput['troco_para'] ?? '';
?>

<div x-data="drawerCheckout({
        subtotal: <?= $subtotal ?>,
        tipoEntrega: '<?= esc($inputEntrega) ?>',
        formaPagamento: '<?= esc($inputPagamento) ?>',
        trocoPara: '<?= esc($inputTroco) ?>'
     })" 
     class="p-4 overflow-y-auto flex-1 space-y-4">

    <?php if (! empty($erroCheckout)): ?>
        <div class="p-3 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-xl flex items-center gap-2">
            <span class="material-symbols-outlined text-[16px] shrink-0">error</span>
            <span><?= esc($erroCheckout) ?></span>
        </div>
    <?php endif; ?>

    <!-- Lista de Itens com Controles de Quantidade (- / +) -->
    <div class="space-y-2">
        <?php if (empty($carrinho)): ?>
            <div class="text-center py-12 text-stone-400 text-xs flex flex-col items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[36px] text-stone-300">shopping_basket</span>
                <span>Sua sacola está vazia. Adicione uma pizza artesanal!</span>
            </div>
        <?php else: ?>
            <?php foreach ($carrinho as $idx => $item): ?>
                <?php
                $qtdItem       = max(1, (int) ($item['quantidade'] ?? 1));
                $precoUnitario = (float) ($item['preco_unitario'] ?? ($item['preco'] / $qtdItem));
                $subtotalItem  = (float) $item['preco'];
                ?>
                <div class="p-3 bg-stone-50 rounded-xl border border-stone-200/80 flex items-center justify-between gap-2 shadow-sm">
                    <div class="min-w-0 flex-1">
                        <span class="font-semibold text-stone-900 text-xs block truncate"><?= esc($item['titulo']) ?></span>
                        <span class="text-[11px] text-stone-500 block line-clamp-2"><?= esc($item['detalhes']) ?></span>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs font-bold text-stone-900">R$ <?= number_format($subtotalItem, 2, ',', '.') ?></span>
                            <?php if ($qtdItem > 1): ?>
                                <span class="text-[10px] text-stone-400">(<?= $qtdItem ?>x R$ <?= number_format($precoUnitario, 2, ',', '.') ?>)</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Controles de Quantidade e Remoção via HTMX -->
                    <div class="flex items-center gap-1 shrink-0">
                        <div class="inline-flex items-center bg-white border border-stone-200 rounded-lg overflow-hidden">
                            <button type="button"
                                    hx-post="<?= base_url('carrinho/quantidade/' . $idx) ?>"
                                    hx-vals='{"acao": "menos"}'
                                    hx-target="#drawer-conteudo"
                                    hx-swap="innerHTML"
                                    title="Diminuir quantidade"
                                    class="w-6 h-7 hover:bg-stone-100 text-stone-700 font-bold text-xs flex items-center justify-center transition">
                                −
                            </button>
                            <span class="px-2 text-xs font-semibold text-stone-900 select-none"><?= $qtdItem ?></span>
                            <button type="button"
                                    hx-post="<?= base_url('carrinho/quantidade/' . $idx) ?>"
                                    hx-vals='{"acao": "mais"}'
                                    hx-target="#drawer-conteudo"
                                    hx-swap="innerHTML"
                                    title="Aumentar quantidade"
                                    class="w-6 h-7 hover:bg-stone-100 text-stone-700 font-bold text-xs flex items-center justify-center transition">
                                +
                            </button>
                        </div>

                        <button type="button"
                                hx-post="<?= base_url('carrinho/remover/' . $idx) ?>" 
                                hx-target="#drawer-conteudo" 
                                hx-swap="innerHTML"
                                title="Remover item"
                                class="w-7 h-7 rounded-lg bg-stone-200/80 hover:bg-red-100 hover:text-red-700 text-stone-500 flex items-center justify-center text-xs transition">
                            ✕
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Formulário de Checkout com HTMX e Reatividade Alpine -->
    <?php if (! empty($carrinho)): ?>
        <form id="form-finalizar-pedido" 
              hx-post="<?= base_url('carrinho/finalizar') ?>" 
              hx-target="#drawer-conteudo" 
              hx-swap="innerHTML"
              class="pt-4 border-t border-stone-200 space-y-3.5">
            <?= csrf_field() ?>
            
            <div class="flex items-center justify-between">
                <h4 class="font-serif font-bold text-sm text-stone-900">Modalidade & Destino</h4>
                <!-- Pílulas de seleção de modalidade dentro da sacola -->
                <div class="inline-flex p-0.5 bg-stone-100 rounded-lg text-[11px] font-semibold">
                    <button type="button" 
                            @click="mudarModo('delivery')" 
                            :class="tipoEntrega === 'delivery' ? 'bg-stone-900 text-white shadow-sm' : 'text-stone-600 hover:text-stone-900'"
                            class="px-2.5 py-1 rounded-md transition">
                        Entrega
                    </button>
                    <button type="button" 
                            @click="mudarModo('retirada')" 
                            :class="tipoEntrega === 'retirada' ? 'bg-stone-900 text-white shadow-sm' : 'text-stone-600 hover:text-stone-900'"
                            class="px-2.5 py-1 rounded-md transition">
                        Retirada
                    </button>
                    <button type="button" 
                            @click="mudarModo('mesa')" 
                            :class="tipoEntrega === 'mesa' ? 'bg-stone-900 text-white shadow-sm' : 'text-stone-600 hover:text-stone-900'"
                            class="px-2.5 py-1 rounded-md transition">
                        Na Mesa
                    </button>
                </div>
            </div>

            <div class="space-y-2.5 text-xs">
                <div>
                    <label class="block text-[11px] font-medium text-stone-600 mb-0.5">Nome Completo *</label>
                    <input type="text" name="nome" value="<?= esc($inputNome) ?>" required placeholder="Seu nome"
                           class="w-full bg-stone-50 border border-stone-200 rounded-xl p-2.5 text-stone-900 focus:outline-none focus:border-brand-800" />
                </div>

                <div>
                    <label class="block text-[11px] font-medium text-stone-600 mb-0.5">WhatsApp para aviso *</label>
                    <input type="tel" name="telefone" value="<?= esc($inputTelefone) ?>" required placeholder="(11) 99999-9999"
                           class="w-full bg-stone-50 border border-stone-200 rounded-xl p-2.5 text-stone-900 focus:outline-none focus:border-brand-800" />
                </div>

                <!-- Campo de Endereço: visível apenas se tipoEntrega === 'delivery' -->
                <div x-show="tipoEntrega === 'delivery'" x-transition>
                    <label class="block text-[11px] font-medium text-stone-600 mb-0.5">Endereço de Entrega *</label>
                    <input type="text" name="endereco" :required="tipoEntrega === 'delivery'" value="<?= esc($inputEndereco) ?>" placeholder="Rua, número e bairro"
                           class="w-full bg-stone-50 border border-stone-200 rounded-xl p-2.5 text-stone-900 focus:outline-none focus:border-brand-800" />
                </div>

                <!-- Campo para Número da Mesa se tipoEntrega === 'mesa' -->
                <div x-show="tipoEntrega === 'mesa'" x-transition>
                    <label class="block text-[11px] font-medium text-stone-600 mb-0.5">Número da Mesa *</label>
                    <input type="text" name="mesa" :required="tipoEntrega === 'mesa'" value="<?= esc($inputMesa) ?>" placeholder="Ex: 04 (Salão Principal)"
                           class="w-full bg-stone-50 border border-stone-200 rounded-xl p-2.5 text-stone-900 focus:outline-none focus:border-brand-800" />
                </div>

                <!-- Aviso para Retirada no Balcão -->
                <div x-show="tipoEntrega === 'retirada'" x-transition class="p-2.5 rounded-xl bg-stone-100 text-stone-700 text-[11px] flex items-center gap-2">
                    <span class="material-symbols-outlined text-[16px] text-stone-500">storefront</span>
                    <span>Retirada no balcão da pizzaria (Sem cobrança de taxa).</span>
                </div>

                <input type="hidden" name="tipo_entrega" id="input-tipo-entrega" x-model="tipoEntrega" :value="tipoEntrega" />
            </div>

            <div>
                <span class="text-xs font-semibold text-stone-700 block mb-1.5">Forma de Pagamento</span>
                <div class="grid grid-cols-3 gap-1.5">
                    <label :class="formaPagamento === 'pix' ? 'border-brand-800 bg-brand-50 shadow-sm' : 'border-stone-200'"
                           class="cursor-pointer border p-2 rounded-xl text-center transition">
                        <input type="radio" name="forma_pagamento" value="pix" x-model="formaPagamento" class="hidden" />
                        <span class="font-bold text-xs text-stone-900 block">PIX</span>
                        <span class="text-[10px] text-emerald-700 font-semibold block">-10% OFF</span>
                    </label>
                    <label :class="formaPagamento === 'cartao_credito' ? 'border-brand-800 bg-brand-50 shadow-sm' : 'border-stone-200'"
                           class="cursor-pointer border p-2 rounded-xl text-center transition">
                        <input type="radio" name="forma_pagamento" value="cartao_credito" x-model="formaPagamento" class="hidden" />
                        <span class="font-bold text-xs text-stone-900 block">Cartão</span>
                        <span class="text-[10px] text-stone-500 block">Na entrega</span>
                    </label>
                    <label :class="formaPagamento === 'dinheiro' ? 'border-brand-800 bg-brand-50 shadow-sm' : 'border-stone-200'"
                           class="cursor-pointer border p-2 rounded-xl text-center transition">
                        <input type="radio" name="forma_pagamento" value="dinheiro" x-model="formaPagamento" class="hidden" />
                        <span class="font-bold text-xs text-stone-900 block">Dinheiro</span>
                        <span class="text-[10px] text-stone-500 block">Com troco</span>
                    </label>
                </div>

                <!-- Campo dinâmico: Revela troco apenas quando selecionado 'Dinheiro' -->
                <div x-show="formaPagamento === 'dinheiro'" x-transition class="mt-2.5">
                    <label class="block text-[11px] font-medium text-stone-600 mb-0.5">Troco para quanto?</label>
                    <input type="text" name="troco_para" x-model="trocoPara" placeholder="Ex: R$ 100,00 ou Não preciso"
                           class="w-full bg-stone-50 border border-stone-200 rounded-xl p-2.5 text-xs text-stone-900 placeholder:text-stone-400 focus:outline-none focus:border-brand-800" />
                </div>
            </div>

            <!-- Resumo Financeiro Dinâmico com Desconto PIX 10% -->
            <div class="pt-3 border-t border-stone-200 space-y-1 text-xs">
                <div class="flex justify-between text-stone-500">
                    <span>Subtotal (<?= $qtdTotal ?> <?= $qtdTotal === 1 ? 'item' : 'itens' ?>)</span>
                    <span>R$ <?= number_format($subtotal, 2, ',', '.') ?></span>
                </div>
                <div x-show="formaPagamento === 'pix'" x-transition class="flex justify-between text-emerald-700 font-semibold">
                    <span>Desconto PIX (10% OFF)</span>
                    <span x-text="descontoPixFormatado"></span>
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
