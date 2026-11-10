<div class="p-6 text-center space-y-4 my-auto">
    <div class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center text-2xl mx-auto shadow-xs font-bold">
        ✓
    </div>
    <h3 class="font-serif font-bold text-2xl text-stone-900">Pedido Confirmado!</h3>
    <p class="text-xs text-stone-500 leading-relaxed max-w-xs mx-auto">
        Sua comanda já foi impressa na cozinha e o forno a lenha está aquecido.
    </p>

    <div class="p-4 bg-stone-50 rounded-2xl border border-stone-200 text-center space-y-1">
        <span class="text-[11px] uppercase tracking-wider text-stone-400 font-semibold block">Código do Pedido</span>
        <span class="font-serif font-extrabold text-2xl text-brand-900 block"><?= esc($codigo) ?></span>
        <span class="text-xs text-stone-600 block">Total: R$ <?= number_format((float) $totalGeral, 2, ',', '.') ?> (<?= strtoupper(esc($pagamento)) ?>)</span>
    </div>

    <button onclick="toggleSacola(); location.reload();" class="w-full bg-stone-900 hover:bg-black text-white font-medium py-3 rounded-xl text-xs sm:text-sm transition shadow-sm">
        Fazer Outro Pedido
    </button>
</div>
