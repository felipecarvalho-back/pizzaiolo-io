<?php
$cargoUsuario = (string) (session()->get('usuario_cargo') ?? '');
$isAdmin      = ($cargoUsuario === 'admin');

$pedidosFila     = (int) ($metricas['pedidos_fila'] ?? 0);
$pedidosHoje     = (int) ($metricas['pedidos_hoje'] ?? 0);
$faturamentoHoje = (float) ($metricas['faturamento_hoje'] ?? 0.0);
$produtosAtivos  = (int) ($metricas['produtos_ativos'] ?? count($produtos));

$pedidosIndice = [];
foreach ($pedidos as $p) {
    $pedidosIndice[] = [
        'codigo'   => mb_strtolower((string) $p->codigo, 'UTF-8'),
        'cliente'  => mb_strtolower((string) $p->cliente_nome, 'UTF-8'),
        'telefone' => mb_strtolower((string) $p->cliente_telefone, 'UTF-8'),
        'status'   => (string) $p->status,
    ];
}

$produtosIndice = [];
foreach ($produtos as $p) {
    $produtosIndice[] = [
        'nome' => mb_strtolower((string) $p->nome, 'UTF-8'),
        'desc' => mb_strtolower((string) ($p->descricao ?? ''), 'UTF-8'),
        'cat'  => (string) $p->categoria_id,
        'tipo' => (string) $p->tipo,
    ];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="<?= csrf_hash() ?>" />
    <meta name="csrf-header" content="<?= csrf_header() ?>" />
    <title>Painel Administrativo — Forno d'Oro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
                        },
                    },
                    fontFamily: {
                        serif: ['"Playfair Display"', 'Georgia', 'serif'],
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        };
    </script>

    <!-- Alpine.js Core (defer) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body x-data="painelApp()" class="bg-[#faf8f5] text-stone-900 font-sans antialiased min-h-screen flex flex-col selection:bg-brand-100 selection:text-brand-900">

    <!-- Formulário oculto reutilizável para ações POST seguras com CSRF -->
    <form id="form-acao-post" method="post" class="hidden">
        <?= csrf_field() ?>
    </form>

    <!-- TOPBAR DO PAINEL -->
    <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-stone-200/90 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="<?= base_url('painel') ?>" class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-brand-800 text-white flex items-center justify-center font-serif font-bold text-lg shadow-sm">
                        F
                    </div>
                    <div>
                        <span class="font-serif font-bold text-lg text-stone-900 leading-none block">Forno d'Oro</span>
                        <span class="text-[11px] text-stone-500 font-medium block mt-0.5">Painel de Gestão</span>
                    </div>
                </a>
            </div>

            <div class="flex items-center gap-3">
                <a href="<?= base_url('/') ?>" class="px-3.5 py-1.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-xs font-semibold text-stone-800 flex items-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-[16px] text-brand-800">restaurant_menu</span>
                    <span class="hidden sm:inline">Ver Cardápio</span>
                </a>
                <div class="hidden sm:flex items-center gap-2 text-xs text-stone-600 border-l border-stone-200 pl-3">
                    <span class="material-symbols-outlined text-[18px] text-emerald-700">verified_user</span>
                    <span class="font-medium"><?= esc(session()->get('usuario_nome')) ?></span>
                    <span class="px-1.5 py-0.5 rounded text-[10px] uppercase font-bold bg-stone-100 text-stone-600 border border-stone-200">
                        <?= esc($cargoUsuario) ?>
                    </span>
                </div>
                <a href="<?= base_url('logout') ?>" class="px-3 py-1.5 rounded-xl text-stone-500 hover:text-red-700 hover:bg-red-50 text-xs font-semibold flex items-center gap-1 transition">
                    <span class="material-symbols-outlined text-[16px]">logout</span>
                    <span class="hidden sm:inline">Sair</span>
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto w-full px-4 sm:px-6 py-8 flex-1">

        <!-- Alertas -->
        <?php if (session()->getFlashdata('sucesso')): ?>
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-medium flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                <span><?= esc(session()->getFlashdata('sucesso')) ?></span>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('erro')): ?>
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs sm:text-sm font-medium flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">error</span>
                <span><?= esc(session()->getFlashdata('erro')) ?></span>
            </div>
        <?php endif; ?>

        <!-- Resumo de Métricas Operacionais Reais -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
            <div @click="mudarAba('pedidos'); filtroStatusPedido = 'pendente'" class="cursor-pointer bg-white border border-stone-200/90 hover:border-brand-800/40 p-4 rounded-2xl shadow-sm transition">
                <div class="flex items-center justify-between text-stone-500">
                    <span class="text-[11px] uppercase tracking-wider font-medium">Pedidos na Fila</span>
                    <span class="material-symbols-outlined text-[20px] text-amber-600">local_fire_department</span>
                </div>
                <span class="font-serif font-bold text-2xl text-stone-900 mt-1.5 block"><?= $pedidosFila ?> em andamento</span>
                <span class="text-[11px] text-stone-400 block mt-0.5">Total listado: <?= count($pedidos) ?></span>
            </div>
            <div @click="mudarAba('pedidos')" class="cursor-pointer bg-white border border-stone-200/90 hover:border-brand-800/40 p-4 rounded-2xl shadow-sm transition">
                <div class="flex items-center justify-between text-stone-500">
                    <span class="text-[11px] uppercase tracking-wider font-medium">Faturamento Hoje</span>
                    <span class="material-symbols-outlined text-[20px] text-emerald-600">payments</span>
                </div>
                <span class="font-serif font-bold text-2xl text-emerald-800 mt-1.5 block">R$ <?= number_format($faturamentoHoje, 2, ',', '.') ?></span>
                <span class="text-[11px] text-stone-400 block mt-0.5"><?= $pedidosHoje ?> <?= $pedidosHoje === 1 ? 'pedido hoje' : 'pedidos hoje' ?></span>
            </div>
            <div <?= $isAdmin ? '@click="mudarAba(\'produtos\')"' : '' ?> class="<?= $isAdmin ? 'cursor-pointer hover:border-brand-800/40' : '' ?> bg-white border border-stone-200/90 p-4 rounded-2xl shadow-sm transition">
                <div class="flex items-center justify-between text-stone-500">
                    <span class="text-[11px] uppercase tracking-wider font-medium">Cardápio Ativo</span>
                    <span class="material-symbols-outlined text-[20px] text-brand-800">local_pizza</span>
                </div>
                <span class="font-serif font-bold text-2xl text-stone-900 mt-1.5 block"><?= $produtosAtivos ?> ativos</span>
                <span class="text-[11px] text-stone-400 block mt-0.5"><?= count($produtos) ?> cadastrados</span>
            </div>
            <div <?= $isAdmin ? '@click="mudarAba(\'usuarios\')"' : '' ?> class="<?= $isAdmin ? 'cursor-pointer hover:border-brand-800/40' : '' ?> bg-white border border-stone-200/90 p-4 rounded-2xl shadow-sm transition">
                <div class="flex items-center justify-between text-stone-500">
                    <span class="text-[11px] uppercase tracking-wider font-medium">Categorias & Equipe</span>
                    <span class="material-symbols-outlined text-[20px] text-blue-600">group</span>
                </div>
                <span class="font-serif font-bold text-2xl text-stone-900 mt-1.5 block"><?= count($categorias) ?> categorias</span>
                <span class="text-[11px] text-stone-400 block mt-0.5"><?= count($usuarios) ?> membros na equipe</span>
            </div>
        </div>

        <!-- NAVEGAÇÃO DE ABAS REATIVA VIA ALPINE.JS COM SINCRONIZAÇÃO DE URL -->
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-2 mb-6 border-b border-stone-200">
            <button type="button"
                    @click="mudarAba('pedidos')" 
                    :class="abaAtiva === 'pedidos' ? 'bg-stone-900 text-white shadow-sm' : 'bg-white border border-stone-200 text-stone-600 hover:text-stone-900'"
                    class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold flex items-center gap-1.5 transition shrink-0">
                <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                <span>Ordens & Pedidos</span>
                <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] font-bold" 
                      :class="abaAtiva === 'pedidos' ? 'bg-white/20 text-white' : 'bg-stone-200 text-stone-700'">
                    <?= count($pedidos) ?>
                </span>
            </button>
            <?php if ($isAdmin): ?>
                <button type="button"
                        @click="mudarAba('produtos')" 
                        :class="abaAtiva === 'produtos' ? 'bg-stone-900 text-white shadow-sm' : 'bg-white border border-stone-200 text-stone-600 hover:text-stone-900'"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold flex items-center gap-1.5 transition shrink-0">
                    <span class="material-symbols-outlined text-[18px]">local_pizza</span>
                    <span>Produtos & Pizzas</span>
                </button>
                <button type="button"
                        @click="mudarAba('categorias')" 
                        :class="abaAtiva === 'categorias' ? 'bg-stone-900 text-white shadow-sm' : 'bg-white border border-stone-200 text-stone-600 hover:text-stone-900'"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold flex items-center gap-1.5 transition shrink-0">
                    <span class="material-symbols-outlined text-[18px]">category</span>
                    <span>Categorias</span>
                </button>
                <button type="button"
                        @click="mudarAba('bordas')" 
                        :class="abaAtiva === 'bordas' ? 'bg-stone-900 text-white shadow-sm' : 'bg-white border border-stone-200 text-stone-600 hover:text-stone-900'"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold flex items-center gap-1.5 transition shrink-0">
                    <span class="material-symbols-outlined text-[18px]">radio_button_checked</span>
                    <span>Bordas</span>
                </button>
                <button type="button"
                        @click="mudarAba('usuarios')" 
                        :class="abaAtiva === 'usuarios' ? 'bg-stone-900 text-white shadow-sm' : 'bg-white border border-stone-200 text-stone-600 hover:text-stone-900'"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold flex items-center gap-1.5 transition shrink-0">
                    <span class="material-symbols-outlined text-[18px]">group</span>
                    <span>Usuários</span>
                </button>
            <?php endif; ?>
        </div>

        <!-- ABA 1: ORDENS & PEDIDOS (GESTÃO COMPLETA DE PEDIDOS) -->
        <section x-show="abaAtiva === 'pedidos'" x-cloak class="space-y-4">
            
            <!-- Barra de Filtros e Busca de Pedidos -->
            <div class="bg-white border border-stone-200/90 rounded-2xl p-4 shadow-sm flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar pb-1 sm:pb-0">
                    <button type="button" 
                            @click="filtroStatusPedido = 'todos'"
                            :class="filtroStatusPedido === 'todos' ? 'bg-stone-900 text-white' : 'bg-stone-100 text-stone-600 hover:bg-stone-200'"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition shrink-0">
                        Todos
                    </button>
                    <button type="button" 
                            @click="filtroStatusPedido = 'pendente'"
                            :class="filtroStatusPedido === 'pendente' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-800 hover:bg-amber-100'"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition shrink-0">
                        Pendentes
                    </button>
                    <button type="button" 
                            @click="filtroStatusPedido = 'confirmado'"
                            :class="filtroStatusPedido === 'confirmado' ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-800 hover:bg-blue-100'"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition shrink-0">
                        Confirmados
                    </button>
                    <button type="button" 
                            @click="filtroStatusPedido = 'em_preparo'"
                            :class="filtroStatusPedido === 'em_preparo' ? 'bg-purple-600 text-white' : 'bg-purple-50 text-purple-800 hover:bg-purple-100'"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition shrink-0">
                        Em Preparo
                    </button>
                    <button type="button" 
                            @click="filtroStatusPedido = 'saiu_entrega'"
                            :class="filtroStatusPedido === 'saiu_entrega' ? 'bg-cyan-600 text-white' : 'bg-cyan-50 text-cyan-800 hover:bg-cyan-100'"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition shrink-0">
                        Em Entrega
                    </button>
                    <button type="button" 
                            @click="filtroStatusPedido = 'entregue'"
                            :class="filtroStatusPedido === 'entregue' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100'"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition shrink-0">
                        Entregues
                    </button>
                    <button type="button" 
                            @click="filtroStatusPedido = 'cancelado'"
                            :class="filtroStatusPedido === 'cancelado' ? 'bg-rose-600 text-white' : 'bg-rose-50 text-rose-800 hover:bg-rose-100'"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition shrink-0">
                        Cancelados
                    </button>
                </div>

                <!-- Busca por Código ou Cliente -->
                <div class="relative w-full sm:w-64">
                    <span class="material-symbols-outlined text-stone-400 text-[18px] absolute left-3 top-1/2 -translate-y-1/2">search</span>
                    <input type="text" 
                           x-model="buscaPedido" 
                           placeholder="Buscar pedido, cliente ou tel..." 
                           class="w-full bg-stone-50 border border-stone-200 rounded-xl py-2 pl-9 pr-3 text-xs text-stone-900 placeholder:text-stone-400 focus:outline-none focus:border-brand-800 focus:bg-white transition" />
                </div>
            </div>

            <!-- Listagem de Pedidos -->
            <?php if (empty($pedidos)): ?>
                <div class="bg-white border border-stone-200/90 rounded-2xl p-12 text-center">
                    <span class="material-symbols-outlined text-4xl text-stone-300 mb-2 block">receipt_long</span>
                    <h4 class="font-medium text-stone-700 text-sm">Nenhum pedido registrado no momento</h4>
                    <p class="text-xs text-stone-400 mt-1">Os pedidos realizados pelo cardápio aparecerão aqui em tempo real.</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($pedidos as $p): ?>
                        <?php
                        $badgeBg = match($p->status) {
                            'entregue'     => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                            'confirmado'   => 'bg-blue-50 text-blue-800 border-blue-200',
                            'em_preparo'   => 'bg-purple-50 text-purple-800 border-purple-200',
                            'saiu_entrega' => 'bg-cyan-50 text-cyan-800 border-cyan-200',
                            'cancelado'    => 'bg-rose-50 text-rose-800 border-rose-200',
                            default        => 'bg-amber-50 text-amber-800 border-amber-200',
                        };
                        $telLimpo    = preg_replace('/\D/', '', (string) $p->cliente_telefone);
                        $whatsappMsg = rawurlencode("Olá " . $p->cliente_nome . "! Seu pedido " . $p->codigo . " na Pizzaria Forno d'Oro está em andamento.");
                        $codMin      = mb_strtolower((string) $p->codigo, 'UTF-8');
                        $cliMin      = mb_strtolower((string) $p->cliente_nome, 'UTF-8');
                        $telMin      = mb_strtolower((string) $p->cliente_telefone, 'UTF-8');
                        ?>
                        <div x-show="pedidoVisivel($el)"
                             x-transition
                             data-codigo="<?= esc($codMin) ?>"
                             data-cliente="<?= esc($cliMin) ?>"
                             data-telefone="<?= esc($telMin) ?>"
                             data-status="<?= esc($p->status) ?>"
                             class="bg-white border border-stone-200/90 hover:border-stone-300 rounded-2xl p-5 shadow-sm transition">
                            
                            <!-- Cabeçalho do Card de Pedido -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-stone-100">
                                <div class="flex items-center gap-3">
                                    <span class="font-mono font-bold text-base text-stone-900"><?= esc($p->codigo) ?></span>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border <?= $badgeBg ?> capitalize">
                                        <?= str_replace('_', ' ', esc($p->status)) ?>
                                    </span>
                                    <span class="text-xs text-stone-400">
                                        <?= date('d/m/Y \à\s H:i', strtotime($p->created_at ?? 'now')) ?>
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-stone-500 uppercase tracking-wider font-medium">Total:</span>
                                    <span class="font-serif font-bold text-lg text-brand-900">
                                        R$ <?= number_format((float) $p->total_geral, 2, ',', '.') ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Dados do Cliente e Entrega -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 py-3.5 border-b border-stone-100 text-xs">
                                <div>
                                    <span class="text-stone-400 block text-[11px] uppercase tracking-wider mb-0.5">Cliente</span>
                                    <span class="font-semibold text-stone-900 block"><?= esc($p->cliente_nome) ?></span>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-stone-500"><?= esc($p->cliente_telefone) ?></span>
                                        <?php if (! empty($telLimpo)): ?>
                                            <a href="https://wa.me/55<?= $telLimpo ?>?text=<?= $whatsappMsg ?>" 
                                               target="_blank" 
                                               rel="noopener noreferrer"
                                               class="text-emerald-700 hover:text-emerald-800 font-semibold inline-flex items-center gap-0.5">
                                                <span class="material-symbols-outlined text-[14px]">chat</span>
                                                <span>WhatsApp</span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div>
                                    <span class="text-stone-400 block text-[11px] uppercase tracking-wider mb-0.5">Modalidade & Destino</span>
                                    <span class="font-semibold text-stone-900 capitalize block">
                                        <?= esc($p->tipo_entrega) ?> 
                                        <?= $p->tipo_entrega === 'delivery' ? '(Taxa R$ 5,00)' : '(Sem taxa)' ?>
                                    </span>
                                    <span class="text-stone-600 block mt-0.5">
                                        <?= ! empty($p->endereco) ? esc($p->endereco) : 'Retirada no balcão / Mesa' ?>
                                    </span>
                                </div>

                                <div>
                                    <span class="text-stone-400 block text-[11px] uppercase tracking-wider mb-0.5">Pagamento</span>
                                    <span class="font-semibold text-stone-900 capitalize block">
                                        <?= str_replace('_', ' ', esc($p->forma_pagamento)) ?>
                                        <?= $p->forma_pagamento === 'pix' ? ' (10% OFF)' : '' ?>
                                    </span>
                                    <?php if (! empty($p->troco_para)): ?>
                                        <span class="text-amber-800 font-medium block mt-0.5">
                                            Troco para R$ <?= number_format((float) $p->troco_para, 2, ',', '.') ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Itens do Pedido com Exibição Completa de Meio a Meio (2 Sabores) -->
                            <div class="py-3">
                                <span class="text-[11px] text-stone-400 uppercase tracking-wider font-semibold block mb-2">Composição do Pedido:</span>
                                <div class="space-y-1.5">
                                    <?php if (! empty($p->itens)): ?>
                                        <?php foreach ($p->itens as $item): ?>
                                            <?php
                                            $sabores = $item->sabores_nomes ?? [];
                                            if (count($sabores) >= 2) {
                                                $tituloItem = 'Pizza Meio a Meio: 1/2 ' . $sabores[0] . ' + 1/2 ' . $sabores[1];
                                            } elseif (count($sabores) === 1) {
                                                $tituloItem = $sabores[0];
                                            } else {
                                                $tituloItem = $item->produto_nome ?? 'Item';
                                            }
                                            $qtdItem = max(1, (int) ($item->quantidade ?? 1));
                                            ?>
                                            <div class="p-2.5 rounded-xl bg-stone-50 flex items-center justify-between text-xs">
                                                <div>
                                                    <span class="inline-block px-1.5 py-0.5 rounded bg-stone-200 text-stone-800 font-bold text-[11px] mr-1"><?= $qtdItem ?>x</span>
                                                    <span class="font-semibold text-stone-900"><?= esc($tituloItem) ?></span>
                                                    <?php if (! empty($item->tamanho_nome)): ?>
                                                        <span class="text-stone-600 font-medium">(<?= esc($item->tamanho_nome) ?>)</span>
                                                    <?php endif; ?>
                                                    <?php if (! empty($item->borda_nome) && stripos($item->borda_nome, 'Tradicional') === false): ?>
                                                        <span class="text-brand-800 font-medium">• Borda: <?= esc($item->borda_nome) ?></span>
                                                    <?php endif; ?>
                                                    <?php if (! empty($item->observacao)): ?>
                                                        <span class="text-stone-500 italic block mt-0.5"><?= esc($item->observacao) ?></span>
                                                    <?php endif; ?>
                                                </div>
                                                <span class="font-semibold text-stone-800 shrink-0">
                                                    R$ <?= number_format((float) $item->subtotal, 2, ',', '.') ?>
                                                </span>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-stone-400 italic text-xs">Detalhes dos itens salvos na finalização.</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Barra de Ações: Transição de Status -->
                            <div class="pt-3 border-t border-stone-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <span class="text-xs text-stone-500">Mudar status operacional:</span>
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <?php if ($p->status !== 'confirmado'): ?>
                                        <button type="button" 
                                                @click="atualizarStatus(<?= $p->id ?>, 'confirmado')" 
                                                class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-800 hover:bg-blue-100 transition">
                                            Confirmar
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($p->status !== 'em_preparo'): ?>
                                        <button type="button" 
                                                @click="atualizarStatus(<?= $p->id ?>, 'em_preparo')" 
                                                class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-purple-50 text-purple-800 hover:bg-purple-100 transition">
                                            No Forno
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($p->status !== 'saiu_entrega'): ?>
                                        <button type="button" 
                                                @click="atualizarStatus(<?= $p->id ?>, 'saiu_entrega')" 
                                                class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-cyan-50 text-cyan-800 hover:bg-cyan-100 transition">
                                            Despachar
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($p->status !== 'entregue'): ?>
                                        <button type="button" 
                                                @click="atualizarStatus(<?= $p->id ?>, 'entregue')" 
                                                class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-800 hover:bg-emerald-100 transition">
                                            Concluir
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($p->status !== 'cancelado'): ?>
                                        <button type="button" 
                                                @click="atualizarStatus(<?= $p->id ?>, 'cancelado')" 
                                                class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-rose-50 text-rose-800 hover:bg-rose-100 transition">
                                            Cancelar
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Empty State quando nenhum pedido corresponder ao filtro/busca -->
                <div x-show="!temResultadoPedido" x-cloak class="bg-white border border-stone-200/90 rounded-2xl p-10 text-center shadow-sm">
                    <span class="material-symbols-outlined text-4xl text-stone-300 mb-2 block">search_off</span>
                    <h4 class="font-serif font-bold text-base text-stone-800">Nenhum pedido encontrado</h4>
                    <p class="text-xs text-stone-500 mt-1 mb-4">Não há pedidos correspondentes ao filtro ou busca informada.</p>
                    <button type="button"
                            @click="limparFiltrosPedido()"
                            class="px-4 py-2 rounded-xl bg-stone-900 hover:bg-black text-white text-xs font-semibold transition">
                        Limpar filtros de pedidos
                    </button>
                </div>
            <?php endif; ?>

        </section>

        <?php if ($isAdmin): ?>
        <!-- ABA 2: PRODUTOS & PIZZAS COM EDIÇÃO, PAUSA RÁPIDA E FILTROS -->
        <section x-show="abaAtiva === 'produtos'" x-cloak class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- Formulário de Cadastro / Edição de Produto -->
            <div class="lg:col-span-5 bg-white border border-stone-200/90 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-1">
                    <h2 class="font-serif font-bold text-lg text-stone-900" x-text="formProduto.id ? 'Editar Item #' + formProduto.id : 'Cadastrar Item'">Cadastrar Item</h2>
                    <button type="button" x-show="formProduto.id" x-cloak @click="limparFormProduto()" class="text-xs font-semibold text-stone-500 hover:text-stone-800">
                        Cancelar edição
                    </button>
                </div>
                <p class="text-xs text-stone-500 mb-5">Adicione ou atualize pizzas e bebidas do cardápio digital.</p>

                <form action="<?= base_url('painel/produto/salvar') ?>" method="post" class="space-y-3.5 text-xs sm:text-sm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" x-model="formProduto.id" />

                    <div>
                        <label class="block font-medium text-stone-700 mb-1">Nome do Produto *</label>
                        <input type="text" name="nome" x-model="formProduto.nome" required placeholder="Ex: Pepperoni Especial D.O.P"
                               class="w-full bg-stone-50/60 border border-stone-300 focus:border-brand-800 rounded-xl p-2.5 text-stone-900 focus:outline-none transition" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-medium text-stone-700 mb-1">Categoria *</label>
                            <select name="categoria_id" x-model="formProduto.categoria_id" required class="w-full bg-stone-50/60 border border-stone-300 focus:border-brand-800 rounded-xl p-2.5 text-stone-900 focus:outline-none transition">
                                <?php foreach ($categorias as $c): ?>
                                    <option value="<?= $c->id ?>"><?= esc($c->nome) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium text-stone-700 mb-1">Tipo *</label>
                            <select name="tipo" x-model="formProduto.tipo" class="w-full bg-stone-50/60 border border-stone-300 focus:border-brand-800 rounded-xl p-2.5 text-stone-900 focus:outline-none transition">
                                <option value="pizza">Pizza (Montável)</option>
                                <option value="simples">Item Simples / Bebida</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 items-center">
                        <div>
                            <label class="block font-medium text-stone-700 mb-1">Preço Base (R$) *</label>
                            <input type="text" name="preco_base" x-model="formProduto.preco_base" required placeholder="69,90"
                                   class="w-full bg-stone-50/60 border border-stone-300 focus:border-brand-800 rounded-xl p-2.5 text-stone-900 focus:outline-none transition" />
                        </div>
                        <div class="pt-5">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="destaque" value="1" x-model="formProduto.destaque" class="accent-brand-800 w-4 h-4 rounded" />
                                <span class="text-xs font-medium text-stone-700">Destaque do Chef</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium text-stone-700 mb-1">Ingredientes / Descrição</label>
                        <textarea name="descricao" x-model="formProduto.descricao" rows="2" placeholder="Descreva os ingredientes..."
                                  class="w-full bg-stone-50/60 border border-stone-300 focus:border-brand-800 rounded-xl p-2.5 text-stone-900 focus:outline-none transition"></textarea>
                    </div>

                    <div>
                        <label class="block font-medium text-stone-700 mb-1">URL da Imagem (Opcional)</label>
                        <input type="url" name="imagem" x-model="formProduto.imagem" placeholder="https://..."
                               class="w-full bg-stone-50/60 border border-stone-300 focus:border-brand-800 rounded-xl p-2.5 text-stone-900 focus:outline-none transition" />
                    </div>

                    <button type="submit" class="w-full bg-brand-800 hover:bg-brand-900 text-white font-semibold py-2.5 rounded-xl shadow-sm transition"
                            x-text="formProduto.id ? 'Salvar Alterações do Produto' : 'Salvar Item'">
                        Salvar Item
                    </button>
                </form>
            </div>

            <!-- Listagem de Produtos com Busca, Pausa Rápida e Edição -->
            <div class="lg:col-span-7 bg-white border border-stone-200/90 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <h3 class="font-serif font-bold text-lg text-stone-900">Itens Cadastrados (<?= count($produtos) ?>)</h3>
                </div>

                <!-- Filtros Reativos de Produtos -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <div class="relative">
                        <span class="material-symbols-outlined text-stone-400 text-[18px] absolute left-3 top-1/2 -translate-y-1/2">search</span>
                        <input type="text" 
                               x-model="buscaProduto" 
                               placeholder="Filtrar por nome ou ingrediente..." 
                               class="w-full bg-stone-50 border border-stone-200 rounded-xl py-2 pl-9 pr-3 text-xs text-stone-900 placeholder:text-stone-400 focus:outline-none focus:border-brand-800 focus:bg-white transition" />
                    </div>
                    <div>
                        <select x-model="filtroCategoriaProduto" 
                                class="w-full bg-stone-50 border border-stone-200 rounded-xl py-2 px-3 text-xs text-stone-900 focus:outline-none focus:border-brand-800 focus:bg-white transition">
                            <option value="todas">Todas as categorias</option>
                            <?php foreach ($categorias as $c): ?>
                                <option value="<?= $c->id ?>"><?= esc($c->nome) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="space-y-3 max-h-[560px] overflow-y-auto pr-1">
                    <?php foreach ($produtos as $p): ?>
                        <?php
                        $pNomeMin = mb_strtolower((string) $p->nome, 'UTF-8');
                        $pDescMin = mb_strtolower((string) ($p->descricao ?? ''), 'UTF-8');
                        $pJson    = htmlspecialchars(json_encode([
                            'id'           => (int) $p->id,
                            'nome'         => (string) $p->nome,
                            'categoria_id' => (int) $p->categoria_id,
                            'tipo'         => (string) $p->tipo,
                            'preco_base'   => number_format((float) $p->preco_base, 2, ',', ''),
                            'destaque'     => (bool) $p->destaque,
                            'descricao'    => (string) ($p->descricao ?? ''),
                            'imagem'       => (string) ($p->imagem ?? ''),
                        ]), ENT_QUOTES, 'UTF-8');
                        ?>
                        <div x-show="produtoVisivel($el)"
                             x-transition
                             data-nome="<?= esc($pNomeMin) ?>"
                             data-desc="<?= esc($pDescMin) ?>"
                             data-cat="<?= (int) $p->categoria_id ?>"
                             data-tipo="<?= esc($p->tipo) ?>"
                             class="bg-stone-50 border border-stone-200/70 p-3 rounded-xl flex items-center justify-between gap-3 <?= ! $p->ativo ? 'opacity-60' : '' ?>">
                            <div class="flex items-center gap-3 min-w-0">
                                <img src="<?= esc($p->imagem) ?>" alt="<?= esc($p->nome) ?>" class="w-12 h-12 rounded-lg object-cover bg-stone-200 shrink-0" />
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="font-semibold text-stone-900 text-sm truncate"><?= esc($p->nome) ?></h4>
                                        <?php if ($p->destaque): ?>
                                            <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-900 text-[10px] font-bold">Destaque</span>
                                        <?php endif; ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold <?= $p->ativo ? 'bg-emerald-100 text-emerald-800' : 'bg-stone-200 text-stone-600' ?>">
                                            <?= $p->ativo ? 'Ativo' : 'Pausado' ?>
                                        </span>
                                    </div>
                                    <p class="text-xs text-stone-500 truncate"><?= esc($p->categoria_nome ?? 'Sem categoria') ?></p>
                                    <span class="text-xs font-bold text-stone-900">R$ <?= number_format((float) $p->preco_base, 2, ',', '.') ?></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                <!-- Pausar / Ativar com 1 clique -->
                                <button type="button"
                                        @click="submeterPost('<?= base_url('painel/produto/alternar/' . $p->id) ?>')"
                                        class="w-8 h-8 rounded-lg hover:bg-stone-200 text-stone-500 hover:text-stone-800 flex items-center justify-center transition"
                                        title="<?= $p->ativo ? 'Pausar no cardápio' : 'Ativar no cardápio' ?>">
                                    <span class="material-symbols-outlined text-[18px]"><?= $p->ativo ? 'pause_circle' : 'play_circle' ?></span>
                                </button>
                                <!-- Editar Produto -->
                                <button type="button"
                                        @click="editarProduto(<?= $pJson ?>)"
                                        class="w-8 h-8 rounded-lg hover:bg-blue-50 text-stone-500 hover:text-blue-700 flex items-center justify-center transition"
                                        title="Editar produto">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </button>
                                <!-- Excluir via POST -->
                                <button type="button"
                                        @click="abrirModalConfirmacao('Remover Produto', 'Deseja remover este item do cardápio? Esta ação não pode ser desfeita.', 'Sim, Remover', 'red', () => { submeterPost('<?= base_url('painel/produto/excluir/' . $p->id) ?>'); })"
                                        class="w-8 h-8 rounded-lg hover:bg-red-50 text-stone-400 hover:text-red-700 flex items-center justify-center transition"
                                        title="Remover produto">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Empty State quando nenhum produto corresponder ao filtro/busca -->
                    <div x-show="!temResultadoProduto" x-cloak class="bg-stone-50 border border-dashed border-stone-300 rounded-xl p-8 text-center">
                        <span class="material-symbols-outlined text-3xl text-stone-400 mb-1.5 block">search_off</span>
                        <h4 class="font-serif font-bold text-sm text-stone-800">Nenhum produto encontrado</h4>
                        <p class="text-xs text-stone-500 mt-0.5 mb-3">Tente buscar outro termo ou limpar os filtros ativos.</p>
                        <button type="button"
                                @click="limparFiltrosProduto()"
                                class="px-3.5 py-1.5 rounded-lg bg-stone-900 hover:bg-black text-white text-xs font-semibold transition">
                            Limpar filtros
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- ABA 3: CATEGORIAS -->
        <section x-show="abaAtiva === 'categorias'" x-cloak class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
            <div class="md:col-span-5 bg-white border border-stone-200/90 rounded-2xl p-6 shadow-sm">
                <h2 class="font-serif font-bold text-lg text-stone-900 mb-4">Nova Categoria</h2>
                <form action="<?= base_url('painel/categoria/salvar') ?>" method="post" class="space-y-3.5 text-xs sm:text-sm">
                    <?= csrf_field() ?>
                    <div>
                        <label class="block font-medium text-stone-700 mb-1">Nome da Categoria *</label>
                        <input type="text" name="nome" required placeholder="Ex: Vinhos Selecionados" class="w-full bg-stone-50/60 border border-stone-300 focus:border-brand-800 rounded-xl p-2.5 text-stone-900 focus:outline-none transition" />
                    </div>
                    <div>
                        <label class="block font-medium text-stone-700 mb-1">Descrição</label>
                        <input type="text" name="descricao" placeholder="Rótulos especiais" class="w-full bg-stone-50/60 border border-stone-300 focus:border-brand-800 rounded-xl p-2.5 text-stone-900 focus:outline-none transition" />
                    </div>
                    <div>
                        <label class="block font-medium text-stone-700 mb-1">Ordem de Exibição</label>
                        <input type="number" name="ordem" value="5" class="w-full bg-stone-50/60 border border-stone-300 focus:border-brand-800 rounded-xl p-2.5 text-stone-900 focus:outline-none transition" />
                    </div>
                    <button type="submit" class="w-full bg-brand-800 hover:bg-brand-900 text-white font-semibold py-2.5 rounded-xl transition">
                        Cadastrar Categoria
                    </button>
                </form>
            </div>

            <div class="md:col-span-7 bg-white border border-stone-200/90 rounded-2xl p-6 shadow-sm">
                <h3 class="font-serif font-bold text-lg text-stone-900 mb-4">Categorias Ativas</h3>
                <div class="space-y-2.5">
                    <?php foreach ($categorias as $c): ?>
                        <div class="bg-stone-50 border border-stone-200/70 p-3 rounded-xl flex items-center justify-between gap-2">
                            <div>
                                <span class="font-semibold text-stone-900 text-sm block"><?= esc($c->nome) ?></span>
                                <span class="text-xs text-stone-500">Slug: <?= esc($c->slug) ?> • Ordem <?= $c->ordem ?></span>
                            </div>
                            <button type="button"
                                    @click="abrirModalConfirmacao('Remover Categoria', 'Deseja remover a categoria <?= esc(addslashes($c->nome)) ?>?', 'Sim, Remover', 'red', () => { submeterPost('<?= base_url('painel/categoria/excluir/' . $c->id) ?>'); })"
                                    class="w-8 h-8 rounded-lg hover:bg-red-50 text-stone-400 hover:text-red-700 flex items-center justify-center transition"
                                    title="Remover categoria">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- ABA 4: BORDAS -->
        <section x-show="abaAtiva === 'bordas'" x-cloak class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
            <div class="md:col-span-5 bg-white border border-stone-200/90 rounded-2xl p-6 shadow-sm">
                <h2 class="font-serif font-bold text-lg text-stone-900 mb-4">Nova Borda Recheada</h2>
                <form action="<?= base_url('painel/borda/salvar') ?>" method="post" class="space-y-3.5 text-xs sm:text-sm">
                    <?= csrf_field() ?>
                    <div>
                        <label class="block font-medium text-stone-700 mb-1">Nome da Borda *</label>
                        <input type="text" name="nome" required placeholder="Ex: Borda Vulcão Catupiry" class="w-full bg-stone-50/60 border border-stone-300 focus:border-brand-800 rounded-xl p-2.5 text-stone-900 focus:outline-none transition" />
                    </div>
                    <div>
                        <label class="block font-medium text-stone-700 mb-1">Valor Adicional (R$) *</label>
                        <input type="text" name="preco" required placeholder="14,00 (ou 0,00 para grátis)" class="w-full bg-stone-50/60 border border-stone-300 focus:border-brand-800 rounded-xl p-2.5 text-stone-900 focus:outline-none transition" />
                    </div>
                    <button type="submit" class="w-full bg-brand-800 hover:bg-brand-900 text-white font-semibold py-2.5 rounded-xl transition">
                        Salvar Borda
                    </button>
                </form>
            </div>

            <div class="md:col-span-7 bg-white border border-stone-200/90 rounded-2xl p-6 shadow-sm">
                <h3 class="font-serif font-bold text-lg text-stone-900 mb-4">Bordas Disponíveis</h3>
                <div class="space-y-2.5">
                    <?php foreach ($bordas as $b): ?>
                        <div class="bg-stone-50 border border-stone-200/70 p-3 rounded-xl flex items-center justify-between gap-2">
                            <span class="font-semibold text-stone-900 text-sm"><?= esc($b->nome) ?></span>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-sm <?= (float) $b->preco <= 0 ? 'text-emerald-700' : 'text-stone-900' ?>">
                                    <?= (float) $b->preco <= 0 ? 'Grátis' : '+ R$ ' . number_format((float) $b->preco, 2, ',', '.') ?>
                                </span>
                                <button type="button"
                                        @click="abrirModalConfirmacao('Remover Borda', 'Deseja remover a borda <?= esc(addslashes($b->nome)) ?>?', 'Sim, Remover', 'red', () => { submeterPost('<?= base_url('painel/borda/excluir/' . $b->id) ?>'); })"
                                        class="w-8 h-8 rounded-lg hover:bg-red-50 text-stone-400 hover:text-red-700 flex items-center justify-center transition"
                                        title="Remover borda">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- ABA 5: USUÁRIOS -->
        <section x-show="abaAtiva === 'usuarios'" x-cloak class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
            <div class="md:col-span-5 bg-white border border-stone-200/90 rounded-2xl p-6 shadow-sm">
                <h2 class="font-serif font-bold text-lg text-stone-900 mb-1">Novo Usuário</h2>
                <p class="text-xs text-stone-500 mb-4">Membros da equipe da pizzaria</p>

                <form action="<?= base_url('painel/usuario/salvar') ?>" method="post" class="space-y-3.5 text-xs sm:text-sm">
                    <?= csrf_field() ?>
                    <div>
                        <label class="block font-medium text-stone-700 mb-1">Nome Completo *</label>
                        <input type="text" name="nome" required placeholder="Ex: Marco Rossi" class="w-full bg-stone-50/60 border border-stone-300 focus:border-brand-800 rounded-xl p-2.5 text-stone-900 focus:outline-none transition" />
                    </div>
                    <div>
                        <label class="block font-medium text-stone-700 mb-1">E-mail *</label>
                        <input type="email" name="email" required placeholder="marco@fornodoro.com" class="w-full bg-stone-50/60 border border-stone-300 focus:border-brand-800 rounded-xl p-2.5 text-stone-900 focus:outline-none transition" />
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-medium text-stone-700 mb-1">Senha *</label>
                            <input type="password" name="senha" required placeholder="••••••" class="w-full bg-stone-50/60 border border-stone-300 focus:border-brand-800 rounded-xl p-2.5 text-stone-900 focus:outline-none transition" />
                        </div>
                        <div>
                            <label class="block font-medium text-stone-700 mb-1">Função</label>
                            <select name="cargo" class="w-full bg-stone-50/60 border border-stone-300 focus:border-brand-800 rounded-xl p-2.5 text-stone-900 focus:outline-none transition">
                                <option value="admin">Administrador</option>
                                <option value="atendente">Atendente</option>
                                <option value="cozinha">Cozinha / Forno</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-stone-900 hover:bg-black text-white font-medium py-2.5 rounded-xl transition">
                        Criar Usuário
                    </button>
                </form>
            </div>

            <div class="md:col-span-7 bg-white border border-stone-200/90 rounded-2xl p-6 shadow-sm">
                <h3 class="font-serif font-bold text-lg text-stone-900 mb-4">Equipe Cadastrada (<?= count($usuarios) ?>)</h3>
                <div class="space-y-2.5">
                    <?php foreach ($usuarios as $u): ?>
                        <div class="bg-stone-50 border border-stone-200/70 p-3 rounded-xl flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-stone-200 text-stone-800 font-serif font-bold text-xs flex items-center justify-center uppercase">
                                    <?= esc(substr($u->nome, 0, 2)) ?>
                                </div>
                                <div>
                                    <span class="font-semibold text-stone-900 text-sm block"><?= esc($u->nome) ?></span>
                                    <span class="text-xs text-stone-500"><?= esc($u->email) ?> • <strong class="uppercase text-stone-700"><?= esc($u->cargo) ?></strong></span>
                                </div>
                            </div>
                            <?php if ((int) $u->id !== (int) session()->get('usuario_id')): ?>
                                <button type="button"
                                        @click="abrirModalConfirmacao('Excluir Usuário', 'Deseja realmente excluir este usuário do sistema?', 'Sim, Excluir', 'red', () => { submeterPost('<?= base_url('painel/usuario/excluir/' . $u->id) ?>'); })"
                                        class="w-8 h-8 rounded-lg hover:bg-red-50 text-stone-400 hover:text-red-700 flex items-center justify-center transition"
                                        title="Excluir usuário">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            <?php else: ?>
                                <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-semibold">Você</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

    </main>

    <!-- MODAL DE CONFIRMAÇÃO E AVISO REATIVO COM ALPINE.JS -->
    <div x-show="modalConfirmacao" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
            <!-- Backdrop -->
            <div x-show="modalConfirmacao"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="fecharModalConfirmacao()"
                 class="fixed inset-0 bg-stone-900/60 backdrop-blur-sm transition-opacity" 
                 aria-hidden="true"></div>

            <!-- Card Modal -->
            <div x-show="modalConfirmacao"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-2xl bg-white p-6 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-stone-200">
                
                <div class="flex items-start gap-4">
                    <div :class="modalCorBotao === 'red' ? 'bg-red-100 text-red-700' : 'bg-brand-100 text-brand-800'"
                         class="mx-auto flex h-12 w-12 shrink-0 items-center justify-center rounded-xl sm:mx-0">
                        <span class="material-symbols-outlined text-[24px]" x-text="modalCorBotao === 'red' ? 'warning' : 'help'"></span>
                    </div>
                    <div class="mt-0.5 text-left flex-1">
                        <h3 class="text-base font-bold text-stone-900 font-serif" id="modal-title" x-text="modalTitulo"></h3>
                        <div class="mt-2">
                            <p class="text-xs text-stone-600 leading-relaxed" x-text="modalMsg"></p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-2.5">
                    <button type="button" 
                            x-show="!modalApenasAviso"
                            @click="fecharModalConfirmacao()"
                            class="inline-flex w-full sm:w-auto justify-center rounded-xl bg-stone-100 px-4 py-2.5 text-xs font-semibold text-stone-700 hover:bg-stone-200 transition focus:outline-none">
                        Cancelar
                    </button>
                    <button type="button"
                            @click="executarAcaoConfirmacao()"
                            :class="modalCorBotao === 'red' ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-brand-800 hover:bg-brand-900 text-white'"
                            class="inline-flex w-full sm:w-auto justify-center rounded-xl px-4 py-2.5 text-xs font-semibold shadow-sm transition focus:outline-none">
                        <span x-text="modalBotaoTexto"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPTS ALPINE.JS DO PAINEL -->
    <script>
        function painelApp() {
            const isAdmin = <?= $isAdmin ? 'true' : 'false' ?>;
            const abasPermitidas = isAdmin ? ['pedidos', 'produtos', 'categorias', 'bordas', 'usuarios'] : ['pedidos'];
            const urlParams = new URLSearchParams(window.location.search);
            const reqTab = urlParams.get('aba') || 'pedidos';
            const initialTab = abasPermitidas.includes(reqTab) ? reqTab : 'pedidos';

            return {
                abaAtiva: initialTab,
                pedidosIndice: <?= json_encode($pedidosIndice) ?>,
                produtosIndice: <?= json_encode($produtosIndice) ?>,

                // Estado de Edição de Produto
                formProduto: {
                    id: '',
                    nome: '',
                    categoria_id: '<?= ! empty($categorias) ? (int) $categorias[0]->id : 1 ?>',
                    tipo: 'pizza',
                    preco_base: '',
                    destaque: false,
                    descricao: '',
                    imagem: ''
                },

                editarProduto(p) {
                    this.formProduto = {
                        id: p.id,
                        nome: p.nome,
                        categoria_id: String(p.categoria_id),
                        tipo: p.tipo,
                        preco_base: p.preco_base,
                        destaque: Boolean(p.destaque),
                        descricao: p.descricao || '',
                        imagem: p.imagem || ''
                    };
                    window.scrollTo({ top: 180, behavior: 'smooth' });
                },

                limparFormProduto() {
                    this.formProduto = {
                        id: '',
                        nome: '',
                        categoria_id: '<?= ! empty($categorias) ? (int) $categorias[0]->id : 1 ?>',
                        tipo: 'pizza',
                        preco_base: '',
                        destaque: false,
                        descricao: '',
                        imagem: ''
                    };
                },

                submeterPost(actionUrl) {
                    const form = document.getElementById('form-acao-post');
                    if (form) {
                        form.action = actionUrl;
                        form.submit();
                    }
                },

                // Modal de Confirmação & Alertas
                modalConfirmacao: false,
                modalTitulo: '',
                modalMsg: '',
                modalBotaoTexto: 'Confirmar',
                modalCorBotao: 'brand',
                modalApenasAviso: false,
                acaoConfirmar: null,

                abrirModalConfirmacao(titulo, msg, btnTexto = 'Confirmar', cor = 'brand', acao = null, apenasAviso = false) {
                    this.modalTitulo = titulo;
                    this.modalMsg = msg;
                    this.modalBotaoTexto = btnTexto;
                    this.modalCorBotao = cor;
                    this.acaoConfirmar = acao;
                    this.modalApenasAviso = Boolean(apenasAviso);
                    this.modalConfirmacao = true;
                },

                fecharModalConfirmacao() {
                    this.modalConfirmacao = false;
                    this.acaoConfirmar = null;
                },

                executarAcaoConfirmacao() {
                    const acao = this.acaoConfirmar;
                    this.fecharModalConfirmacao();
                    if (typeof acao === 'function') {
                        acao();
                    }
                },

                // Filtros de Ordens
                buscaPedido: '',
                filtroStatusPedido: 'todos',

                // Filtros de Produtos
                buscaProduto: '',
                filtroCategoriaProduto: 'todas',
                filtroTipoProduto: 'todos',

                mudarAba(aba) {
                    if (!abasPermitidas.includes(aba)) {
                        aba = 'pedidos';
                    }
                    this.abaAtiva = aba;
                    const url = new URL(window.location.href);
                    url.searchParams.set('aba', aba);
                    window.history.replaceState({}, '', url);
                },

                limparFiltrosPedido() {
                    this.buscaPedido = '';
                    this.filtroStatusPedido = 'todos';
                },

                limparFiltrosProduto() {
                    this.buscaProduto = '';
                    this.filtroCategoriaProduto = 'todas';
                    this.filtroTipoProduto = 'todos';
                },

                get temResultadoPedido() {
                    if (!this.pedidosIndice.length) return true;
                    return this.pedidosIndice.some((p) => this.pedidoVisivel(p.codigo, p.cliente, p.telefone, p.status));
                },

                get temResultadoProduto() {
                    if (!this.produtosIndice.length) return true;
                    return this.produtosIndice.some((p) => this.produtoVisivel(p.nome, p.desc, p.cat, p.tipo));
                },

                pedidoVisivel(elOrCodigo, clienteNomeArg, clienteTelefoneArg, statusArg) {
                    let codigo = elOrCodigo;
                    let clienteNome = clienteNomeArg || '';
                    let clienteTelefone = clienteTelefoneArg || '';
                    let status = statusArg || '';

                    if (elOrCodigo && typeof elOrCodigo === 'object' && elOrCodigo.dataset) {
                        codigo = elOrCodigo.dataset.codigo || '';
                        clienteNome = elOrCodigo.dataset.cliente || '';
                        clienteTelefone = elOrCodigo.dataset.telefone || '';
                        status = elOrCodigo.dataset.status || '';
                    }

                    const termo = this.buscaPedido.trim().toLowerCase();
                    const statusOk = (this.filtroStatusPedido === 'todos' || status === this.filtroStatusPedido);
                    const telLimpo = clienteTelefone.replace(/\D/g, '');
                    const termoDigitos = termo.replace(/\D/g, '');
                    const telOk = termoDigitos.length > 0 && telLimpo.includes(termoDigitos);
                    const buscaOk = !termo || 
                        codigo.includes(termo) || 
                        clienteNome.includes(termo) || 
                        clienteTelefone.includes(termo) ||
                        telOk;
                    return statusOk && buscaOk;
                },

                produtoVisivel(elOrNome, descArg, catIdArg, tipoArg) {
                    let nome = elOrNome;
                    let desc = descArg || '';
                    let catId = catIdArg || '';
                    let tipo = tipoArg || '';

                    if (elOrNome && typeof elOrNome === 'object' && elOrNome.dataset) {
                        nome = elOrNome.dataset.nome || '';
                        desc = elOrNome.dataset.desc || '';
                        catId = elOrNome.dataset.cat || '';
                        tipo = elOrNome.dataset.tipo || '';
                    }

                    const termo = this.buscaProduto.trim().toLowerCase();
                    const buscaOk = !termo || nome.includes(termo) || (desc && desc.includes(termo));
                    const catOk = (this.filtroCategoriaProduto === 'todas' || String(catId) === String(this.filtroCategoriaProduto));
                    const tipoOk = (this.filtroTipoProduto === 'todos' || tipo === this.filtroTipoProduto);
                    return buscaOk && catOk && tipoOk;
                },

                atualizarStatus(pedidoId, novoStatus) {
                    const statusLegivel = novoStatus.replace('_', ' ');
                    this.abrirModalConfirmacao(
                        'Alterar Status',
                        `Deseja alterar o status do pedido para "${statusLegivel}"?`,
                        'Confirmar',
                        'brand',
                        async () => {
                            try {
                                const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                                const headerMeta = document.querySelector('meta[name="csrf-header"]');
                                const formData = new FormData();
                                formData.append('pedido_id', pedidoId);
                                formData.append('status', novoStatus);
                                formData.append('<?= csrf_token() ?>', tokenMeta ? tokenMeta.content : '<?= csrf_hash() ?>');

                                const headers = {
                                    'X-Requested-With': 'XMLHttpRequest'
                                };
                                if (tokenMeta && headerMeta) {
                                    headers[headerMeta.content] = tokenMeta.content;
                                }

                                const resp = await fetch('<?= base_url('painel/pedido/status') ?>', {
                                    method: 'POST',
                                    body: formData,
                                    headers
                                });

                                const data = await resp.json();
                                if (data.sucesso) {
                                    window.location.reload();
                                } else {
                                    this.abrirModalConfirmacao('Aviso', data.erro || 'Não foi possível alterar o status.', 'OK', 'brand', null, true);
                                }
                            } catch (e) {
                                console.error('Erro ao atualizar status', e);
                                this.abrirModalConfirmacao('Erro', 'Falha de comunicação ao atualizar o status do pedido.', 'OK', 'brand', null, true);
                            }
                        }
                    );
                }
            };
        }
    </script>
</body>
</html>
