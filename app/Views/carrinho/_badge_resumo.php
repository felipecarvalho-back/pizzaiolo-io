<!-- Atualiza o Badge do Topo -->
<span id="badge-sacola" hx-swap-oob="true" class="px-2 py-0.2 rounded-full bg-black/25 text-xs font-bold">
    <?= $qtd ?>
</span>

<!-- Atualiza a Barra Flutuante -->
<div id="barra-flutuante" hx-swap-oob="true" class="fixed bottom-4 left-1/2 -translate-x-1/2 z-20 w-11/12 max-w-lg <?= $qtd === 0 ? 'hidden' : '' ?>">
    <button onclick="toggleSacola()" class="w-full bg-stone-900 hover:bg-black active:scale-[0.99] text-white p-3.5 rounded-2xl shadow-xl flex items-center justify-between transition">
        <div class="flex items-center gap-2.5">
            <span class="px-2 py-0.5 rounded-lg bg-brand-800 text-white text-xs font-bold">
                <?= $qtd ?> <?= $qtd === 1 ? 'item' : 'itens' ?>
            </span>
            <span class="text-xs sm:text-sm font-medium">Ver Sacola de Pedidos</span>
        </div>
        <span class="font-serif font-bold text-sm sm:text-base text-amber-300">
            R$ <?= number_format($total + ($qtd > 0 ? 5.00 : 0.00), 2, ',', '.') ?>
        </span>
    </button>
</div>
