<?php
$precosPizzas = [];
foreach ($todasPizzas as $p) {
    $precosPizzas[(int) $p->id] = (float) $p->preco_base;
}
$precosBordas = [0 => 0.0];
foreach ($bordas as $b) {
    $precosBordas[(int) $b->id] = (float) $b->preco;
}
?>
<div x-data="pizzaModal({
        preco1: <?= (float) $produto->preco_base ?>,
        precosPizzas: <?= htmlspecialchars(json_encode($precosPizzas), ENT_QUOTES, 'UTF-8') ?>,
        precosBordas: <?= htmlspecialchars(json_encode($precosBordas), ENT_QUOTES, 'UTF-8') ?>,
        tamanho: 'Grande',
        sabor2Id: '',
        bordaId: 0
     })" 
     class="bg-white w-full sm:max-w-lg rounded-t-3xl sm:rounded-2xl max-h-[92vh] flex flex-col overflow-hidden shadow-2xl">
    
    <div class="px-5 py-4 border-b border-stone-200 flex items-center justify-between">
        <div>
            <h3 class="font-serif font-bold text-lg text-stone-900"><?= esc($produto->nome) ?></h3>
            <p class="text-xs text-stone-500">
                A partir de R$ <?= number_format((float) $produto->preco_base, 2, ',', '.') ?>
            </p>
        </div>
        <button type="button" @click="fecharModal()" onclick="fecharModal()" class="w-8 h-8 rounded-full bg-stone-100 hover:bg-stone-200 text-stone-600 flex items-center justify-center transition">✕</button>
    </div>

    <!-- Formulário gerenciado pelo HTMX com reatividade Alpine.js -->
    <form hx-post="<?= base_url('carrinho/adicionar') ?>" 
          hx-target="#drawer-conteudo" 
          hx-swap="innerHTML"
          hx-on::after-request="if (window.fecharModal) fecharModal(); if (window.toggleSacola) toggleSacola();"
          class="flex flex-col flex-1 overflow-hidden">
        
        <input type="hidden" name="produto_id" value="<?= $produto->id ?>" />
        <input type="hidden" name="tipo" value="pizza" />

        <div class="p-5 overflow-y-auto space-y-4 text-xs sm:text-sm flex-1">
            
            <!-- 1. Tamanho da Pizza -->
            <div>
                <label class="block font-semibold text-stone-900 mb-2">1. Escolha o Tamanho</label>
                <div class="grid grid-cols-3 gap-2">
                    <label :class="tamanho === 'Broto' ? 'border-brand-800 bg-brand-50 shadow-xs' : 'border-stone-200 hover:border-stone-300'"
                           class="cursor-pointer border p-2.5 rounded-xl text-center transition block">
                        <input type="radio" name="tamanho" value="Broto" x-model="tamanho" class="hidden" />
                        <span class="font-bold text-stone-800 block text-xs">Broto</span>
                        <span class="text-[10px] text-stone-500">4 fatias (0.75x)</span>
                    </label>
                    <label :class="tamanho === 'Média' ? 'border-brand-800 bg-brand-50 shadow-xs' : 'border-stone-200 hover:border-stone-300'"
                           class="cursor-pointer border p-2.5 rounded-xl text-center transition block">
                        <input type="radio" name="tamanho" value="Média" x-model="tamanho" class="hidden" />
                        <span class="font-bold text-stone-800 block text-xs">Média</span>
                        <span class="text-[10px] text-stone-500">6 fatias (0.90x)</span>
                    </label>
                    <label :class="tamanho === 'Grande' ? 'border-brand-800 bg-brand-50 shadow-xs' : 'border-stone-200 hover:border-stone-300'"
                           class="cursor-pointer border p-2.5 rounded-xl text-center transition block">
                        <input type="radio" name="tamanho" value="Grande" x-model="tamanho" class="hidden" />
                        <span class="font-bold text-stone-800 block text-xs">Grande</span>
                        <span class="text-[10px] text-stone-500">8 fatias (1.0x)</span>
                    </label>
                </div>
            </div>

            <!-- 2. Sabores (1 ou 2) -->
            <div>
                <label class="block font-semibold text-stone-900 mb-2">2. Sabores (Inteira ou Meio a Meio)</label>
                <div class="space-y-2">
                    <div class="p-2.5 rounded-xl bg-stone-50 border border-stone-200 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-semibold text-stone-400 uppercase block">1º Sabor (Principal)</span>
                            <span class="font-semibold text-stone-900 text-xs sm:text-sm"><?= esc($produto->nome) ?></span>
                        </div>
                        <span class="text-xs text-stone-500 font-medium">R$ <?= number_format((float) $produto->preco_base, 2, ',', '.') ?></span>
                    </div>

                    <div x-show="tamanho !== 'Broto'" x-transition>
                        <span class="text-[10px] font-semibold text-stone-500 uppercase block mb-1">2º Sabor Opcional (50% / 50%)</span>
                        <select name="sabor_2_id" 
                                x-model="sabor2Id"
                                class="w-full bg-white border border-stone-200 rounded-xl p-2.5 text-xs text-stone-900 focus:outline-none focus:border-brand-800">
                            <option value="">Sem segundo sabor (100% <?= esc($produto->nome) ?>)</option>
                            <?php foreach ($todasPizzas as $p): ?>
                                <?php if ($p->id !== $produto->id): ?>
                                    <option value="<?= $p->id ?>">
                                        1/2 <?= esc($p->nome) ?> (R$ <?= number_format((float)$p->preco_base, 2, ',', '.') ?>)
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-[10px] text-stone-400 mt-1 italic">
                            * Pizzas meio a meio são cobradas pelo maior valor entre os dois sabores.
                        </p>
                    </div>

                    <div x-show="tamanho === 'Broto'" x-transition class="p-2.5 rounded-xl bg-amber-50/70 border border-amber-200/60 text-amber-900 text-[11px] flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-amber-700">info</span>
                        <span>O tamanho Broto é servido exclusivamente com sabor único.</span>
                    </div>
                </div>
            </div>

            <!-- 3. Borda Recheada -->
            <div>
                <label class="block font-semibold text-stone-900 mb-2">3. Borda Artesanal</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <label :class="bordaId === 0 ? 'border-brand-800 bg-brand-50 shadow-xs' : 'border-stone-200 hover:border-stone-300'"
                           class="cursor-pointer border p-2.5 rounded-xl flex items-center justify-between text-xs transition">
                        <input type="radio" name="borda_id" value="0" x-model.number="bordaId" class="hidden" />
                        <span class="font-medium text-stone-800 truncate pr-1">Tradicional</span>
                        <span class="text-stone-500 font-semibold shrink-0">Grátis</span>
                    </label>
                    <?php foreach ($bordas as $b): ?>
                        <?php if ((float)$b->preco > 0): ?>
                            <label :class="bordaId === <?= (int) $b->id ?> ? 'border-brand-800 bg-brand-50 shadow-xs' : 'border-stone-200 hover:border-stone-300'"
                                   class="cursor-pointer border p-2.5 rounded-xl flex items-center justify-between text-xs transition">
                                <input type="radio" name="borda_id" value="<?= $b->id ?>" x-model.number="bordaId" class="hidden" />
                                <span class="font-medium text-stone-800 truncate pr-1"><?= esc($b->nome) ?></span>
                                <span class="text-stone-700 font-semibold shrink-0">+ R$ <?= number_format((float)$b->preco, 2, ',', '.') ?></span>
                            </label>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- 4. Observações -->
            <div>
                <label class="block font-semibold text-stone-900 mb-1">Instruções para o Pizzaiolo</label>
                <input type="text" name="obs" placeholder="Ex: tirar a cebola, massa bem crocante..." 
                       class="w-full bg-stone-50 border border-stone-200 rounded-xl p-2.5 text-xs text-stone-900 placeholder:text-stone-400 focus:outline-none focus:border-brand-800" />
            </div>

        </div>

        <!-- Footer do Modal com Preço Reativo em Tempo Real -->
        <div class="p-4 bg-stone-50 border-t border-stone-200 flex items-center justify-between gap-3">
            <button type="submit" class="w-full bg-brand-800 hover:bg-brand-900 active:scale-[0.98] text-white font-semibold py-3 px-4 rounded-xl text-xs sm:text-sm transition shadow-sm flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                <span>Adicionar à Sacola • R$ <span x-text="precoTotalFormatado"></span></span>
            </button>
        </div>

    </form>
</div>
