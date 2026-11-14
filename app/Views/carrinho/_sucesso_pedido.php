<?php
$pixPayload = '00020126580014BR.GOV.BCB.PIX0136fornodoro@pizzaria.com.br520400005303986540'
    . number_format((float) $totalGeral, 2, '.', '')
    . '5802BR5918FORNO D ORO PIZZAS6009SAO PAULO62070503***6304ABCD';

$resumoLinhas = [
    "🍕 *Pedido " . $codigo . " — Forno d'Oro*",
    "👤 Cliente: " . ($nome ?? 'Cliente'),
    "📍 Modalidade: " . ucfirst($entrega ?? 'delivery') . (! empty($endereco) ? " ({$endereco})" : ''),
];
if (! empty($itens)) {
    $resumoLinhas[] = "\n*Itens do Pedido:*";
    foreach ($itens as $it) {
        $q = max(1, (int) ($it['quantidade'] ?? 1));
        $resumoLinhas[] = "• {$q}x " . ($it['titulo'] ?? 'Item') . " — R$ " . number_format((float) ($it['preco'] ?? 0), 2, ',', '.');
    }
}
if (! empty($descontoPix) && $descontoPix > 0) {
    $resumoLinhas[] = "⚡ Desconto PIX (10%): -R$ " . number_format((float) $descontoPix, 2, ',', '.');
}
$resumoLinhas[] = "💰 *Total Final: R$ " . number_format((float) $totalGeral, 2, ',', '.') . "* (" . strtoupper(str_replace('_', ' ', (string) $pagamento)) . ")";
$whatsappTexto = rawurlencode(implode("\n", $resumoLinhas));
?>

<div x-data="{
        copiado: false,
        statusAtual: 'pendente',
        statusLabel: 'Recebido — Aguardando confirmação',
        horaAtualizacao: '<?= date('H:i') ?>',
        consultando: false,
        copiarPix(texto) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(texto);
            }
            this.copiado = true;
            setTimeout(() => { this.copiado = false; }, 3000);
        },
        async consultarStatus() {
            this.consultando = true;
            try {
                const r = await fetch('<?= base_url('carrinho/status-pedido/' . rawurlencode((string) $codigo)) ?>');
                const d = await r.json();
                if (d.encontrado) {
                    this.statusAtual = d.status;
                    this.statusLabel = d.status_label;
                    this.horaAtualizacao = d.updated_at;
                }
            } catch (e) {}
            this.consultando = false;
        }
     }"
     class="p-5 overflow-y-auto flex-1 space-y-4 text-left">

    <div class="text-center space-y-2">
        <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center text-xl mx-auto shadow-sm font-bold">
            ✓
        </div>
        <h3 class="font-serif font-bold text-xl text-stone-900">Pedido Confirmado!</h3>
        <p class="text-xs text-stone-500 leading-relaxed">
            Sua comanda já foi enviada à cozinha e o forno a lenha está aquecido.
        </p>
    </div>

    <!-- Card de Código e Status em Tempo Real -->
    <div class="p-4 bg-stone-50 rounded-2xl border border-stone-200 space-y-2.5">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-[10px] uppercase tracking-wider text-stone-400 font-semibold block">Código do Pedido</span>
                <span class="font-serif font-extrabold text-xl text-brand-900"><?= esc($codigo) ?></span>
            </div>
            <button type="button"
                    @click="consultarStatus()"
                    class="px-2.5 py-1 rounded-lg bg-white border border-stone-200 hover:border-stone-300 text-[11px] font-semibold text-stone-700 flex items-center gap-1 transition shadow-sm">
                <span class="material-symbols-outlined text-[14px]" :class="consultando ? 'animate-spin' : ''">sync</span>
                <span>Atualizar</span>
            </button>
        </div>

        <div class="p-2.5 rounded-xl bg-white border border-stone-200/80 flex items-center justify-between text-xs">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="font-semibold text-stone-800" x-text="statusLabel"></span>
            </div>
            <span class="text-[10px] text-stone-400" x-text="horaAtualizacao"></span>
        </div>

        <div class="pt-1 border-t border-stone-200/70 flex items-center justify-between text-xs">
            <span class="text-stone-500">Total (<?= strtoupper(str_replace('_', ' ', esc($pagamento))) ?>)</span>
            <span class="font-bold text-stone-900 text-sm">R$ <?= number_format((float) $totalGeral, 2, ',', '.') ?></span>
        </div>
        <?php if (! empty($descontoPix) && $descontoPix > 0): ?>
            <div class="flex items-center justify-between text-[11px] text-emerald-700 font-semibold">
                <span>Economia PIX (10% OFF)</span>
                <span>- R$ <?= number_format((float) $descontoPix, 2, ',', '.') ?></span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bloco PIX Copia e Cola quando pagamento === 'pix' -->
    <?php if ($pagamento === 'pix'): ?>
        <div class="p-3.5 rounded-2xl bg-emerald-50/80 border border-emerald-200 space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-900 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">qr_code_2</span>
                    Pagamento via PIX (10% OFF aplicado)
                </span>
                <span class="text-[10px] font-semibold uppercase px-2 py-0.5 rounded bg-emerald-200/70 text-emerald-900">Chave Copia e Cola</span>
            </div>
            <input type="text" readonly value="<?= esc($pixPayload) ?>"
                   class="w-full bg-white border border-emerald-200 rounded-lg p-2 text-[10px] font-mono text-stone-600 focus:outline-none" />
            <button type="button"
                    @click="copiarPix('<?= esc($pixPayload) ?>')"
                    class="w-full py-2 px-3 rounded-xl font-semibold text-xs transition flex items-center justify-center gap-1.5"
                    :class="copiado ? 'bg-emerald-700 text-white' : 'bg-emerald-600 hover:bg-emerald-700 text-white'">
                <span class="material-symbols-outlined text-[16px]" x-text="copiado ? 'check' : 'content_copy'"></span>
                <span x-text="copiado ? 'Código PIX Copiado!' : 'Copiar Código PIX'"></span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Resumo dos Itens -->
    <?php if (! empty($itens)): ?>
        <div class="space-y-1.5">
            <span class="text-[11px] uppercase tracking-wider text-stone-400 font-semibold block">Resumo do Pedido</span>
            <?php foreach ($itens as $it): ?>
                <div class="text-xs flex justify-between text-stone-700 bg-stone-50 px-3 py-2 rounded-lg border border-stone-200/60">
                    <span class="truncate pr-2"><?= max(1, (int) ($it['quantidade'] ?? 1)) ?>x <?= esc($it['titulo']) ?></span>
                    <span class="font-semibold shrink-0">R$ <?= number_format((float) $it['preco'], 2, ',', '.') ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Ações Pós-Pedido: WhatsApp e Novo Pedido -->
    <div class="space-y-2 pt-2">
        <a href="https://wa.me/?text=<?= $whatsappTexto ?>"
           target="_blank"
           rel="noopener noreferrer"
           class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold py-2.5 px-4 rounded-xl text-xs sm:text-sm transition shadow-sm flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-[18px]">chat</span>
            <span>Enviar Resumo no WhatsApp</span>
        </a>

        <button type="button" onclick="toggleSacola(); location.reload();" class="w-full bg-stone-900 hover:bg-black text-white font-medium py-2.5 rounded-xl text-xs sm:text-sm transition shadow-sm">
            Fazer Outro Pedido
        </button>
    </div>
</div>
