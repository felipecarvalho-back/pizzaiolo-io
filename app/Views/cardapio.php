<?php
$carrinhoSessao = session()->get('carrinho') ?? [];
$qtdSessao      = \App\Controllers\CarrinhoController::contarItensCarrinho($carrinhoSessao);
$totalSessao    = (float) array_sum(array_column($carrinhoSessao, 'preco'));

$catalogoIndice = [];
foreach ($produtos as $p) {
    $catalogoIndice[] = [
        'cat'  => (string) ($p->categoria_slug ?? 'tradicionais'),
        'nome' => mb_strtolower((string) $p->nome, 'UTF-8'),
        'desc' => mb_strtolower((string) ($p->descricao ?? ''), 'UTF-8'),
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
    <title>Forno d'Oro — Pizzaria Artesanal & Forno a Lenha</title>
    <!-- Google Fonts: Inter & Playfair Display -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    
    <!-- Tailwind CSS -->
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

    <!-- HTMX CDN -->
    <script src="https://unpkg.com/htmx.org@2.0.4"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body x-data="cardapioApp(<?= htmlspecialchars(json_encode($catalogoIndice), ENT_QUOTES, 'UTF-8') ?>)" 
      @keydown.escape.window="fecharTodosModais()"
      class="bg-[#faf8f5] text-stone-900 antialiased min-h-screen flex flex-col pb-24 selection:bg-brand-100 selection:text-brand-900">

    <!-- HEADER: Editorial, limpo e em linha única -->
    <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-stone-200/80 shadow-[0_1px_3px_rgba(0,0,0,0.03)]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
            
            <!-- Marca -->
            <a href="<?= base_url('/') ?>" class="flex items-center gap-3 group shrink-0">
                <div class="w-9 h-9 rounded-xl bg-brand-800 text-white flex items-center justify-center font-serif font-bold text-lg shadow-sm group-hover:bg-brand-900 transition">
                    F
                </div>
                <div>
                    <span class="font-serif font-bold text-lg text-stone-900 tracking-tight block leading-none">Forno d'Oro</span>
                    <span class="text-[11px] text-stone-500 font-medium block mt-0.5">Pizzaria Napolitana</span>
                </div>
            </a>

            <!-- Status do Restaurante -->
            <div class="hidden md:flex items-center gap-2 px-3 py-1 rounded-full bg-stone-100 text-xs text-stone-600 border border-stone-200/60">
                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                <span class="font-medium text-stone-800">Aberto agora</span>
                <span class="text-stone-300">•</span>
                <span>Entrega em 35–45 min</span>
            </div>

            <!-- Ações Direita: Admin / Cliente / Sacola -->
            <div class="flex items-center gap-2.5 shrink-0">
                <?php if (session()->get('logado')): ?>
                    <!-- Administrador Logado -->
                    <a href="<?= base_url('painel') ?>" class="px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-200 hover:bg-amber-100 text-xs font-semibold text-amber-900 flex items-center gap-1.5 transition">
                        <span class="material-symbols-outlined text-[16px] text-amber-700">dashboard</span>
                        <span>Painel Admin</span>
                    </a>
                <?php elseif (session()->get('cliente_logado')): ?>
                    <!-- Cliente Logado -->
                    <a href="<?= base_url('cliente/perfil') ?>" class="px-3 py-1.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-xs font-semibold text-stone-800 flex items-center gap-2 transition">
                        <span class="w-5 h-5 rounded-full bg-brand-800 text-white text-[10px] flex items-center justify-center font-serif font-bold">
                            <?= esc(substr(session()->get('cliente_nome') ?? 'C', 0, 1)) ?>
                        </span>
                        <span class="hidden sm:inline">Olá, <?= esc(explode(' ', session()->get('cliente_nome') ?? 'Cliente')[0]) ?></span>
                    </a>
                <?php else: ?>
                    <!-- Deslogado: Portal Único de Login -->
                    <a href="<?= base_url('login') ?>" class="px-3 py-1.5 rounded-xl text-xs font-semibold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px]">person</span>
                        <span class="hidden sm:inline">Entrar</span>
                    </a>
                <?php endif; ?>

                <!-- Botão da Sacola Reativo -->
                <button @click="toggleSacola()" 
                        type="button"
                        class="relative flex items-center gap-2 bg-brand-800 hover:bg-brand-900 active:scale-[0.98] text-white px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">shopping_bag</span>
                    <span class="hidden sm:inline">Sacola</span>
                    <span id="badge-sacola" class="px-2 py-0.5 rounded-full bg-black/25 text-xs font-bold">
                        <?= $qtdSessao ?>
                    </span>
                </button>
            </div>

        </div>
    </header>

    <main class="max-w-6xl mx-auto w-full px-4 sm:px-6 pt-5 flex-1">

        <!-- Mensagens Flash -->
        <?php if (session()->getFlashdata('sucesso')): ?>
            <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-medium flex items-center justify-between">
                <span><?= esc(session()->getFlashdata('sucesso')) ?></span>
            </div>
        <?php endif; ?>

        <!-- HERO ENXUTO: 1 momento, 1 mensagem, contido no viewport -->
        <section class="bg-white border border-stone-200/90 rounded-2xl p-5 sm:p-7 mb-6 shadow-[0_1px_4px_rgba(0,0,0,0.03)] flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="max-w-xl">
                <span class="text-[11px] uppercase tracking-wider text-brand-800 font-bold block mb-1">Massa de Fermentação Natural 48h</span>
                <h1 class="font-serif font-bold text-2xl sm:text-3xl text-stone-950 tracking-tight leading-tight mb-2">
                    A autêntica pizza napolitana assada no forno a lenha a 480°C.
                </h1>
                <p class="text-stone-600 text-xs sm:text-sm leading-relaxed mb-4">
                    Ingredientes nobres, molho de tomates San Marzano e borda aerada. Peça inteira ou meio a meio com preparo artesanal sob demanda.
                </p>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-50 border border-emerald-200/70 text-emerald-800 text-xs font-semibold">
                    <span class="material-symbols-outlined text-[16px] text-emerald-700">bolt</span>
                    <span>10% de desconto no pagamento via PIX</span>
                </div>
            </div>
            <div class="w-full md:w-64 lg:w-72 h-40 sm:h-44 rounded-xl overflow-hidden shrink-0 shadow-sm border border-stone-100">
                <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuAbHSmrnQfe2JImXlOi2O9AwNcFtfAUBMmHPtjby75N8tD5LrzQjMdsqekhXD9nm_RX5HcBBz4NyM62vjjj6nGT7PUpySXOBruhQcNPDZOyC8ptnSS1GBqtOXC2eXNqutpGmTwQBlen2UEckkll4HHpazwHsqe-Ot8ecq1Xe5EBF2vEq_6YFvoTU0Rlmi3dImclqjmNbTKmj4h4M2yoXNefhmeZXq-UWnocgsr4a4s" 
                     alt="Pizza Forno d'Oro" class="w-full h-full object-cover" />
            </div>
        </section>

        <!-- BARRA DE CONTROLE REATIVA: MODO DE PEDIDO + BUSCA DECLARATIVA -->
        <section class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 mb-5">
            <div class="inline-flex p-1 bg-white border border-stone-200/80 rounded-xl shadow-sm text-xs font-semibold">
                <button type="button" 
                        @click="selecionarModo('delivery')" 
                        :class="modoEntrega === 'delivery' ? 'bg-stone-900 text-white shadow-sm' : 'text-stone-600 hover:text-stone-900'"
                        class="btn-modo px-3.5 py-1.5 rounded-lg transition flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">moped</span>
                    <span>Entrega</span>
                </button>
                <button type="button" 
                        @click="selecionarModo('retirada')" 
                        :class="modoEntrega === 'retirada' ? 'bg-stone-900 text-white shadow-sm' : 'text-stone-600 hover:text-stone-900'"
                        class="btn-modo px-3.5 py-1.5 rounded-lg transition flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">storefront</span>
                    <span>Retirada</span>
                </button>
                <button type="button" 
                        @click="selecionarModo('mesa')" 
                        :class="modoEntrega === 'mesa' ? 'bg-stone-900 text-white shadow-sm' : 'text-stone-600 hover:text-stone-900'"
                        class="btn-modo px-3.5 py-1.5 rounded-lg transition flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">table_restaurant</span>
                    <span>Na Mesa</span>
                </button>
            </div>

            <!-- Busca Rápida Declarativa via x-model -->
            <div class="relative flex-1 max-w-xs">
                <span class="material-symbols-outlined text-stone-400 text-[18px] absolute left-3 top-1/2 -translate-y-1/2">search</span>
                <input type="text" 
                       x-model="busca" 
                       placeholder="Buscar sabor ou ingrediente..." 
                       class="w-full bg-white border border-stone-200/90 rounded-xl py-2 pl-9 pr-8 text-xs sm:text-sm text-stone-900 placeholder:text-stone-400 focus:outline-none focus:border-brand-800 shadow-sm transition" />
                <button x-show="busca.length > 0" 
                        x-cloak
                        @click="busca = ''" 
                        type="button" 
                        class="absolute right-2.5 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-600 text-xs">✕</button>
            </div>
        </section>

        <!-- FILTROS DE CATEGORIA REATIVOS -->
        <section class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 mb-5">
            <button type="button"
                    @click="categoriaAtiva = 'todas'" 
                    :class="categoriaAtiva === 'todas' ? 'bg-stone-900 text-white font-semibold shadow-sm' : 'bg-white border border-stone-200/90 text-stone-700 font-medium hover:border-stone-400'"
                    class="btn-cat shrink-0 px-3.5 py-1.5 rounded-full text-xs transition">
                Todas as Opções
            </button>
            <?php foreach ($categorias as $cat): ?>
                <button type="button"
                        @click="categoriaAtiva = '<?= esc($cat->slug) ?>'" 
                        :class="categoriaAtiva === '<?= esc($cat->slug) ?>' ? 'bg-stone-900 text-white font-semibold shadow-sm' : 'bg-white border border-stone-200/90 text-stone-700 font-medium hover:border-stone-400'"
                        class="btn-cat shrink-0 px-3.5 py-1.5 rounded-full text-xs transition">
                    <?= esc($cat->nome) ?>
                </button>
            <?php endforeach; ?>
        </section>

        <!-- GRID DE PRODUTOS: FILTRAGEM DECLARATIVA COM ALPINE.JS (SEGURA CONTRA MULTILINHA E UTF-8) -->
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" id="catalogo-produtos">
            <?php foreach ($produtos as $p): ?>
                <?php
                $catSlug = (string) ($p->categoria_slug ?? 'tradicionais');
                $nomeMin = mb_strtolower((string) $p->nome, 'UTF-8');
                $descMin = mb_strtolower((string) ($p->descricao ?? ''), 'UTF-8');
                ?>
                <article x-show="cardVisivel($el)"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="card-produto group bg-white border border-stone-200/90 rounded-2xl overflow-hidden flex flex-col justify-between hover:border-stone-300 hover:shadow-[0_4px_16px_rgba(0,0,0,0.06)] transition duration-200"
                         data-cat="<?= esc($catSlug) ?>"
                         data-nome="<?= esc($nomeMin) ?>"
                         data-desc="<?= esc($descMin) ?>">
                    <div>
                        <div class="relative aspect-[16/10] bg-stone-100 overflow-hidden">
                            <img src="<?= esc($p->imagem) ?>" alt="<?= esc($p->nome) ?>" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
                            <?php if ($p->destaque): ?>
                                <span class="absolute top-2.5 left-2.5 px-2 py-0.5 rounded-md bg-stone-900/90 text-white text-[11px] font-semibold tracking-wide backdrop-blur-sm">
                                    Destaque do Chef
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="p-4">
                            <h3 class="font-serif font-bold text-base sm:text-lg text-stone-900 leading-snug group-hover:text-brand-800 transition"><?= esc($p->nome) ?></h3>
                            <p class="text-xs text-stone-500 line-clamp-2 mt-1 leading-relaxed"><?= esc($p->descricao) ?></p>
                        </div>
                    </div>
                    <div class="px-4 pb-4 pt-3 border-t border-stone-100 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] uppercase tracking-wider text-stone-400 block"><?= $p->tipo === 'pizza' ? 'A partir de' : 'Valor' ?></span>
                            <span class="font-serif font-bold text-base sm:text-lg text-stone-900">R$ <?= number_format((float) $p->preco_base, 2, ',', '.') ?></span>
                        </div>
                        
                        <?php if ($p->tipo === 'pizza'): ?>
                            <!-- Abre o modal via HTMX com reatividade Alpine -->
                            <button hx-get="<?= base_url('carrinho/modal-pizza/' . $p->id) ?>"
                                    hx-target="#modal-container"
                                    hx-swap="innerHTML"
                                    @click="abrirModal()"
                                    class="px-3.5 py-1.5 rounded-xl bg-brand-800 hover:bg-brand-900 active:scale-[0.98] text-white font-semibold text-xs transition shadow-sm flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">tune</span>
                                <span>Montar</span>
                            </button>
                        <?php else: ?>
                            <!-- Adiciona direto via HTMX -->
                            <button hx-post="<?= base_url('carrinho/adicionar') ?>"
                                    hx-vals='{"produto_id": <?= $p->id ?>, "tipo": "simples"}'
                                    hx-target="#drawer-conteudo"
                                    hx-swap="innerHTML"
                                    @click="abrirSacola()"
                                    class="px-3.5 py-1.5 rounded-xl bg-stone-100 hover:bg-stone-200 active:scale-[0.98] text-stone-800 font-semibold text-xs transition flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">add</span>
                                <span>Adicionar</span>
                            </button>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>

        <!-- EMPTY STATE QUANDO NENHUM PRODUTO CORRESPONDER À BUSCA/FILTRO -->
        <div x-show="!temResultado" x-cloak class="bg-white border border-stone-200/90 rounded-2xl p-10 text-center my-4 shadow-sm">
            <span class="material-symbols-outlined text-4xl text-stone-300 block mb-2">search_off</span>
            <h3 class="font-serif font-bold text-lg text-stone-800">Nenhuma opção encontrada</h3>
            <p class="text-xs text-stone-500 mt-1 mb-4">Não encontramos pizzas ou itens para o filtro selecionado.</p>
            <button type="button" 
                    @click="limparFiltros()" 
                    class="px-4 py-2 rounded-xl bg-stone-900 hover:bg-black text-white text-xs font-semibold transition">
                Limpar busca e ver tudo
            </button>
        </div>

    </main>

    <!-- BARRA FLUTUANTE DA SACOLA: Atualizada dinamicamente pelo HTMX e sincronizada com modoEntrega -->
    <div id="barra-flutuante" 
         data-subtotal="<?= number_format($totalSessao, 2, '.', '') ?>"
         class="fixed bottom-4 left-1/2 -translate-x-1/2 z-20 w-11/12 max-w-lg <?= $qtdSessao === 0 ? 'hidden' : '' ?>">
        <button @click="toggleSacola()" type="button" class="w-full bg-stone-900 hover:bg-black active:scale-[0.99] text-white p-3.5 rounded-2xl shadow-xl flex items-center justify-between transition">
            <div class="flex items-center gap-2.5">
                <span class="px-2 py-0.5 rounded-lg bg-brand-800 text-white text-xs font-bold">
                    <?= $qtdSessao ?> <?= $qtdSessao === 1 ? 'item' : 'itens' ?>
                </span>
                <span class="text-xs sm:text-sm font-medium">Ver Sacola de Pedidos</span>
            </div>
            <span id="barra-flutuante-valor" class="font-serif font-bold text-sm sm:text-base text-amber-300">
                R$ <?= number_format($totalSessao + ($qtdSessao > 0 ? 5.00 : 0.00), 2, ',', '.') ?>
            </span>
        </button>
    </div>

    <!-- MODAL CONTAINER: Controlado por Alpine.js e preenchido dinamicamente pelo HTMX -->
    <div id="modal-wrapper" 
         x-cloak
         x-show="isModalOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.self="fecharModal()" 
         class="fixed inset-0 z-50 bg-stone-950/60 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4">
        
        <div x-show="isModalOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4 sm:translate-y-0"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4 sm:translate-y-0"
             id="modal-container" 
             class="w-full sm:max-w-lg">
        </div>
    </div>

    <!-- DRAWER LATERAL DA SACOLA: Animação slide-over declarativa com Alpine.js -->
    <div id="drawer-sacola" 
         x-cloak
         x-show="isDrawerOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.self="fecharSacola()" 
         class="fixed inset-0 z-50 bg-stone-950/60 backdrop-blur-sm flex justify-end">
        
        <div x-show="isDrawerOpen"
             x-transition:enter="transform transition ease-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="bg-white border-l border-stone-200 w-full max-w-md h-full flex flex-col justify-between shadow-2xl">
            
            <div class="p-4 border-b border-stone-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-brand-800">shopping_bag</span>
                    <h3 class="font-serif font-bold text-lg text-stone-900">Sua Sacola</h3>
                </div>
                <button @click="fecharSacola()" type="button" class="w-8 h-8 rounded-full bg-stone-100 hover:bg-stone-200 text-stone-600 flex items-center justify-center transition">✕</button>
            </div>

            <!-- Conteúdo dinâmico atualizado pelo HTMX -->
            <div id="drawer-conteudo" 
                 hx-get="<?= base_url('carrinho/drawer') ?>" 
                 hx-trigger="load" 
                 class="flex-1 flex flex-col overflow-hidden">
            </div>

        </div>
    </div>

    <!-- SCRIPTS ALPINE.JS & HTMX INTEGRATION -->
    <script>
        // Injeção automática de Token CSRF nas requisições HTMX
        document.addEventListener('htmx:configRequest', function(e) {
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            const headerMeta = document.querySelector('meta[name="csrf-header"]');
            if (tokenMeta && headerMeta) {
                e.detail.headers[headerMeta.content] = tokenMeta.content;
            }
        });

        // Sincronização HTMX + Alpine.js: Re-inicializa a árvore Alpine após swaps do HTMX
        document.addEventListener('htmx:afterSwap', function(e) {
            if (window.Alpine && e.detail && e.detail.target) {
                window.Alpine.initTree(e.detail.target);
            }
            if (window.cardapioState) {
                window.cardapioState.sincronizarBarraFlutuante();
            }
        });

        document.addEventListener('htmx:oobAfterSwap', function() {
            if (window.cardapioState) {
                window.cardapioState.sincronizarBarraFlutuante();
            }
        });

        document.addEventListener('htmx:afterSettle', function() {
            if (window.cardapioState) {
                window.cardapioState.sincronizarBarraFlutuante();
            }
        });

        // Componente Principal do Cardápio
        function cardapioApp(catalogo = []) {
            return {
                catalogo: Array.isArray(catalogo) ? catalogo : [],
                isDrawerOpen: false,
                isModalOpen: false,
                busca: '',
                categoriaAtiva: 'todas',
                modoEntrega: 'delivery',
                formaPagamento: 'pix',
                
                init() {
                    window.cardapioState = this;
                    this.$watch('isDrawerOpen', (val) => this.atualizarScroll(val || this.isModalOpen));
                    this.$watch('isModalOpen', (val) => this.atualizarScroll(val || this.isDrawerOpen));
                    this.$watch('modoEntrega', () => this.sincronizarBarraFlutuante());
                },

                abrirSacola() {
                    this.isDrawerOpen = true;
                },
                fecharSacola() {
                    this.isDrawerOpen = false;
                },
                toggleSacola() {
                    this.isDrawerOpen = !this.isDrawerOpen;
                },
                abrirModal() {
                    this.isModalOpen = true;
                },
                fecharModal() {
                    this.isModalOpen = false;
                },
                fecharTodosModais() {
                    this.isModalOpen = false;
                    this.isDrawerOpen = false;
                },

                limparFiltros() {
                    this.busca = '';
                    this.categoriaAtiva = 'todas';
                },

                selecionarModo(modo) {
                    this.modoEntrega = modo;
                    const inputEntrega = document.getElementById('input-tipo-entrega');
                    if (inputEntrega) {
                        inputEntrega.value = modo;
                        inputEntrega.dispatchEvent(new Event('input', { bubbles: true }));
                        inputEntrega.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                    window.dispatchEvent(new CustomEvent('modo-entrega-alterado', { detail: modo }));
                    this.sincronizarBarraFlutuante();
                },

                sincronizarBarraFlutuante() {
                    const barra = document.getElementById('barra-flutuante');
                    const valorEl = document.getElementById('barra-flutuante-valor');
                    if (!barra || !valorEl) return;
                    const subtotal = Number(barra.dataset.subtotal || 0);
                    if (subtotal <= 0) return;
                    const taxa = this.modoEntrega === 'delivery' ? 5.00 : 0.00;
                    const total = subtotal + taxa;
                    valorEl.textContent = 'R$ ' + total.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },

                cardVisivel(elOrCat, nomeArg, descArg) {
                    let cat = elOrCat;
                    let nome = nomeArg || '';
                    let desc = descArg || '';
                    if (elOrCat && typeof elOrCat === 'object' && elOrCat.dataset) {
                        cat = elOrCat.dataset.cat || 'tradicionais';
                        nome = elOrCat.dataset.nome || '';
                        desc = elOrCat.dataset.desc || '';
                    }
                    const termo = this.busca.trim().toLowerCase();
                    const categoriaOk = (this.categoriaAtiva === 'todas' || cat === this.categoriaAtiva);
                    const buscaOk = !termo || nome.includes(termo) || (desc && desc.includes(termo));
                    return categoriaOk && buscaOk;
                },

                get temResultado() {
                    if (!this.catalogo.length) return true;
                    const termo = this.busca.trim().toLowerCase();
                    return this.catalogo.some((item) => {
                        const catOk = (this.categoriaAtiva === 'todas' || item.cat === this.categoriaAtiva);
                        const buscaOk = !termo || item.nome.includes(termo) || (item.desc && item.desc.includes(termo));
                        return catOk && buscaOk;
                    });
                },

                atualizarScroll(bloquear) {
                    if (bloquear) {
                        document.body.classList.add('overflow-hidden');
                    } else {
                        document.body.classList.remove('overflow-hidden');
                    }
                }
            };
        }

        // Componente Reativo do Modal de Pizza
        function pizzaModal(config) {
            return {
                preco1: Number(config.preco1) || 0,
                precosPizzas: config.precosPizzas || {},
                precosBordas: config.precosBordas || {},
                fatoresTamanho: config.fatoresTamanho || { 'Broto': 0.75, 'Média': 0.90, 'Grande': 1.00 },
                maxSaboresTamanho: config.maxSaboresTamanho || { 'Broto': 1, 'Média': 2, 'Grande': 2 },
                tamanho: config.tamanho || 'Grande',
                sabor2Id: config.sabor2Id || '',
                bordaId: Number(config.bordaId) || 0,
                obs: '',

                init() {
                    this.$watch('tamanho', (val) => {
                        if (!this.permiteSegundoSabor) {
                            this.sabor2Id = '';
                        }
                    });
                },

                get permiteSegundoSabor() {
                    const max = this.maxSaboresTamanho[this.tamanho];
                    if (typeof max !== 'undefined') {
                        return Number(max) >= 2;
                    }
                    return this.tamanho !== 'Broto';
                },
                get fatorTamanho() {
                    if (this.fatoresTamanho[this.tamanho]) {
                        return Number(this.fatoresTamanho[this.tamanho]);
                    }
                    if (this.tamanho === 'Broto') return 0.75;
                    if (this.tamanho === 'Média') return 0.90;
                    return 1.0;
                },
                get preco2() {
                    if (!this.sabor2Id || !this.permiteSegundoSabor) return 0;
                    return Number(this.precosPizzas[this.sabor2Id]) || 0;
                },
                get precoBaseFinal() {
                    if (this.sabor2Id && this.preco2 > 0 && this.permiteSegundoSabor) {
                        return Math.max(this.preco1, this.preco2);
                    }
                    return this.preco1;
                },
                get precoBorda() {
                    return Number(this.precosBordas[this.bordaId]) || 0;
                },
                get precoTotal() {
                    return (this.precoBaseFinal * this.fatorTamanho) + this.precoBorda;
                },
                get precoTotalFormatado() {
                    return this.precoTotal.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },

                fecharModal() {
                    if (window.cardapioState) {
                        window.cardapioState.fecharModal();
                    }
                }
            };
        }

        // Componente Reativo do Drawer do Carrinho (com cálculo de 10% OFF no PIX)
        function drawerCheckout(config) {
            return {
                subtotal: Number(config.subtotal) || 0,
                tipoEntrega: config.tipoEntrega || (window.cardapioState && window.cardapioState.modoEntrega) || document.getElementById('input-tipo-entrega')?.value || 'delivery',
                formaPagamento: config.formaPagamento || (window.cardapioState && window.cardapioState.formaPagamento) || 'pix',
                trocoPara: config.trocoPara || '',

                init() {
                    window.addEventListener('modo-entrega-alterado', (e) => {
                        if (e.detail) {
                            this.tipoEntrega = e.detail;
                        }
                    });
                    this.$watch('formaPagamento', (val) => {
                        if (window.cardapioState) {
                            window.cardapioState.formaPagamento = val;
                        }
                    });
                },

                mudarModo(modo) {
                    this.tipoEntrega = modo;
                    if (window.cardapioState) {
                        window.cardapioState.modoEntrega = modo;
                        window.cardapioState.sincronizarBarraFlutuante();
                    }
                },

                get descontoPix() {
                    return this.formaPagamento === 'pix' ? Math.round(this.subtotal * 0.10 * 100) / 100 : 0.00;
                },
                get descontoPixFormatado() {
                    return '- R$ ' + this.descontoPix.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },
                get taxaEntrega() {
                    return this.tipoEntrega === 'delivery' ? 5.00 : 0.00;
                },
                get totalFinal() {
                    return Math.max(0, this.subtotal - this.descontoPix) + this.taxaEntrega;
                },
                get totalFinalFormatado() {
                    return this.totalFinal.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },
                get taxaEntregaFormatada() {
                    return this.taxaEntrega === 0 ? 'Grátis' : 'R$ ' + this.taxaEntrega.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            };
        }

        // Funções globais de retrocompatibilidade para listeners do HTMX
        window.toggleSacola = function() {
            if (window.cardapioState) window.cardapioState.toggleSacola();
        };
        window.toggleSacolaSeFechada = function() {
            if (window.cardapioState) window.cardapioState.abrirSacola();
        };
        window.abrirSacola = function() {
            if (window.cardapioState) window.cardapioState.abrirSacola();
        };
        window.abrirModal = function() {
            if (window.cardapioState) window.cardapioState.abrirModal();
        };
        window.fecharModal = function() {
            if (window.cardapioState) window.cardapioState.fecharModal();
        };
        window.selecionarModo = function(modo) {
            if (window.cardapioState) window.cardapioState.selecionarModo(modo);
        };

        // Listener HTMX para atualizar o badge do topo automaticamente após qualquer mutação
        document.body.addEventListener('atualizarCarrinho', function() {
            htmx.ajax('GET', '<?= base_url('carrinho/contador') ?>', { target: '#badge-sacola', swap: 'outerHTML' });
        });
    </script>
</body>
</html>
