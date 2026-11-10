<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Forno d'Oro — Pizzaria Artesanal & Forno a Lenha</title>
    <!-- Fontes Selecionadas: Inter + Playfair Display (Editorial Autêntico) -->
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
                        stone: {
                            850: '#201d1a',
                            950: '#141210',
                        }
                    },
                    fontFamily: {
                        serif: ['"Playfair Display"', 'Georgia', 'serif'],
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        };
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-[#faf8f5] text-stone-900 antialiased min-h-screen flex flex-col pb-24 selection:bg-brand-100 selection:text-brand-900">

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

            <!-- Ações Direita: Perfil & Sacola -->
            <div class="flex items-center gap-2.5 shrink-0">
                <?php if (session()->get('cliente_logado')): ?>
                    <a href="<?= base_url('cliente/perfil') ?>" class="px-3 py-1.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-xs font-semibold text-stone-800 flex items-center gap-2 transition">
                        <span class="w-5 h-5 rounded-full bg-brand-800 text-white text-[10px] flex items-center justify-center font-serif font-bold">
                            <?= esc(substr(session()->get('cliente_nome') ?? 'C', 0, 1)) ?>
                        </span>
                        <span class="hidden sm:inline"><?= esc(explode(' ', session()->get('cliente_nome') ?? 'Cliente')[0]) ?></span>
                    </a>
                <?php else: ?>
                    <a href="<?= base_url('cliente/login') ?>" class="px-3 py-1.5 rounded-xl text-xs font-semibold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px]">person</span>
                        <span class="hidden sm:inline">Entrar</span>
                    </a>
                <?php endif; ?>

                <button onclick="toggleSacola()" class="relative flex items-center gap-2 bg-brand-800 hover:bg-brand-900 active:scale-[0.98] text-white px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">shopping_bag</span>
                    <span class="hidden sm:inline">Sacola</span>
                    <span id="badge-sacola" class="px-2 py-0.2 rounded-full bg-black/25 text-xs font-bold">0</span>
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

        <!-- HERO ENXUTO: 1 momento, 1 mensagem, altura contida no primeiro viewport -->
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

        <!-- BARRA DE CONTROLE: MODO DE PEDIDO + BUSCA -->
        <section class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 mb-5">
            <!-- Seletor de Modo -->
            <div class="inline-flex p-1 bg-white border border-stone-200/80 rounded-xl shadow-xs text-xs font-semibold">
                <button onclick="selecionarModo('delivery', this)" class="btn-modo active px-3.5 py-1.5 rounded-lg bg-stone-900 text-white transition flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">moped</span>
                    <span>Entrega</span>
                </button>
                <button onclick="selecionarModo('retirada', this)" class="btn-modo px-3.5 py-1.5 rounded-lg text-stone-600 hover:text-stone-900 transition flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">storefront</span>
                    <span>Retirada</span>
                </button>
                <button onclick="selecionarModo('mesa', this)" class="btn-modo px-3.5 py-1.5 rounded-lg text-stone-600 hover:text-stone-900 transition flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">table_restaurant</span>
                    <span>Na Mesa</span>
                </button>
            </div>

            <!-- Busca Rápida -->
            <div class="relative flex-1 max-w-xs">
                <span class="material-symbols-outlined text-stone-400 text-[18px] absolute left-3 top-1/2 -translate-y-1/2">search</span>
                <input type="text" id="campo-busca" oninput="filtrarCards()" placeholder="Buscar sabor ou ingrediente..." 
                       class="w-full bg-white border border-stone-200/90 rounded-xl py-2 pl-9 pr-3 text-xs sm:text-sm text-stone-900 placeholder:text-stone-400 focus:outline-none focus:border-brand-800 shadow-xs transition" />
            </div>
        </section>

        <!-- FILTROS DE CATEGORIA (SERVER-SIDE RENDERED) -->
        <section class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 mb-5">
            <button onclick="filtrarCategoria('todas', this)" class="btn-cat shrink-0 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-stone-900 text-white transition">
                Todas as Opções
            </button>
            <?php foreach ($categorias as $cat): ?>
                <button onclick="filtrarCategoria('<?= esc($cat->slug) ?>', this)" class="btn-cat shrink-0 px-3.5 py-1.5 rounded-full text-xs font-medium bg-white border border-stone-200/90 text-stone-700 hover:border-stone-400 transition">
                    <?= esc($cat->nome) ?>
                </button>
            <?php endforeach; ?>
        </section>

        <!-- GRID DE PRODUTOS RENDERIZADOS PELO PHP -->
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" id="catalogo-produtos">
            <?php foreach ($produtos as $p): ?>
                <article class="card-produto group bg-white border border-stone-200/90 rounded-2xl overflow-hidden flex flex-col justify-between hover:border-stone-300 hover:shadow-[0_4px_16px_rgba(0,0,0,0.06)] transition duration-200"
                         data-cat="<?= esc($p->categoria_slug ?? 'tradicionais') ?>"
                         data-nome="<?= esc(strtolower($p->nome)) ?>"
                         data-desc="<?= esc(strtolower((string)$p->descricao)) ?>">
                    <div>
                        <div class="relative aspect-[16/10] bg-stone-100 overflow-hidden">
                            <img src="<?= esc($p->imagem) ?>" alt="<?= esc($p->nome) ?>" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
                            <?php if ($p->destaque): ?>
                                <span class="absolute top-2.5 left-2.5 px-2 py-0.5 rounded-md bg-stone-900/90 text-white text-[11px] font-semibold tracking-wide backdrop-blur-xs">
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
                            <button onclick="abrirModalMontagem(<?= (int)$p->id ?>, '<?= esc($p->nome) ?>', <?= (float)$p->preco_base ?>)" 
                                    class="px-3.5 py-1.5 rounded-xl bg-brand-800 hover:bg-brand-900 active:scale-[0.98] text-white font-semibold text-xs transition shadow-xs flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">tune</span>
                                <span>Montar</span>
                            </button>
                        <?php else: ?>
                            <button onclick="adicionarItemAvulso('<?= esc($p->nome) ?>', <?= (float)$p->preco_base ?>)" 
                                    class="px-3.5 py-1.5 rounded-xl bg-stone-100 hover:bg-stone-200 active:scale-[0.98] text-stone-800 font-semibold text-xs transition flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">add</span>
                                <span>Adicionar</span>
                            </button>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>

    </main>

    <!-- BARRA FLUTUANTE DA SACOLA -->
    <div id="barra-flutuante" class="fixed bottom-4 left-1/2 -translate-x-1/2 z-20 w-11/12 max-w-lg hidden">
        <button onclick="toggleSacola()" class="w-full bg-stone-900 hover:bg-black active:scale-[0.99] text-white p-3.5 rounded-2xl shadow-xl flex items-center justify-between transition">
            <div class="flex items-center gap-2.5">
                <span class="px-2 py-0.5 rounded-lg bg-brand-800 text-white text-xs font-bold" id="float-qtd">0 itens</span>
                <span class="text-xs sm:text-sm font-medium">Ver Sacola de Pedidos</span>
            </div>
            <span class="font-serif font-bold text-sm sm:text-base text-amber-300" id="float-total">R$ 0,00</span>
        </button>
    </div>

    <!-- MODAL DE MONTAGEM DE PIZZA -->
    <div id="modal-montagem" class="fixed inset-0 z-50 bg-stone-950/60 backdrop-blur-xs hidden flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="bg-white w-full sm:max-w-lg rounded-t-3xl sm:rounded-2xl max-h-[92vh] flex flex-col overflow-hidden shadow-2xl">
            
            <div class="px-5 py-4 border-b border-stone-200 flex items-center justify-between">
                <div>
                    <h3 class="font-serif font-bold text-lg text-stone-900" id="modal-titulo">Personalizar Pizza</h3>
                    <p class="text-xs text-stone-500">Escolha o tamanho e se deseja dividir sabores</p>
                </div>
                <button onclick="fecharModalMontagem()" class="w-8 h-8 rounded-full bg-stone-100 hover:bg-stone-200 text-stone-600 flex items-center justify-center">✕</button>
            </div>

            <div class="p-5 overflow-y-auto space-y-4 text-xs sm:text-sm">
                
                <!-- 1. Tamanho -->
                <div>
                    <label class="block font-semibold text-stone-900 mb-2">1. Tamanho da Pizza</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="cursor-pointer border border-stone-200 p-2.5 rounded-xl text-center has-[:checked]:border-brand-800 has-[:checked]:bg-brand-50 transition">
                            <input type="radio" name="tam" value="Broto" data-fator="0.75" class="hidden" onchange="recalcularModal()" />
                            <span class="font-bold text-stone-800 block text-xs">Broto</span>
                            <span class="text-[10px] text-stone-500">4 fatias (1 sabor)</span>
                        </label>
                        <label class="cursor-pointer border border-stone-200 p-2.5 rounded-xl text-center has-[:checked]:border-brand-800 has-[:checked]:bg-brand-50 transition">
                            <input type="radio" name="tam" value="Média" data-fator="0.9" class="hidden" onchange="recalcularModal()" />
                            <span class="font-bold text-stone-800 block text-xs">Média</span>
                            <span class="text-[10px] text-stone-500">6 fatias (até 2)</span>
                        </label>
                        <label class="cursor-pointer border border-brand-800 bg-brand-50 p-2.5 rounded-xl text-center has-[:checked]:border-brand-800 has-[:checked]:bg-brand-50 transition">
                            <input type="radio" name="tam" value="Grande" data-fator="1.0" checked class="hidden" onchange="recalcularModal()" />
                            <span class="font-bold text-brand-900 block text-xs">Grande</span>
                            <span class="text-[10px] text-brand-800 font-semibold">8 fatias (até 2)</span>
                        </label>
                    </div>
                </div>

                <!-- 2. Sabores -->
                <div>
                    <label class="block font-semibold text-stone-900 mb-2">2. Sabores (Inteira ou Meio a Meio)</label>
                    <div class="space-y-2">
                        <div class="p-2.5 rounded-xl bg-stone-50 border border-stone-200">
                            <span class="text-[10px] font-semibold text-stone-400 uppercase block">1º Sabor (Principal)</span>
                            <span class="font-semibold text-stone-900 text-xs sm:text-sm" id="modal-sabor-1-nome">Margherita</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-semibold text-stone-500 uppercase block mb-1">2º Sabor Opcional (50% / 50%)</span>
                            <select id="modal-select-sabor-2" onchange="recalcularModal()" class="w-full bg-white border border-stone-200 rounded-xl p-2.5 text-xs text-stone-900 focus:outline-none focus:border-brand-800">
                                <option value="">Sem segundo sabor (100% sabor principal)</option>
                                <?php foreach ($produtos as $p): ?>
                                    <?php if ($p->tipo === 'pizza'): ?>
                                        <option value="<?= (int)$p->id ?>" data-nome="<?= esc($p->nome) ?>" data-preco="<?= (float)$p->preco_base ?>">
                                            1/2 <?= esc($p->nome) ?> (R$ <?= number_format((float)$p->preco_base, 2, ',', '.') ?>)
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 3. Bordas do Banco -->
                <div>
                    <label class="block font-semibold text-stone-900 mb-2">3. Borda Recheada</label>
                    <div class="grid grid-cols-2 gap-2">
                        <?php foreach ($bordas as $idx => $b): ?>
                            <label class="cursor-pointer border border-stone-200 p-2.5 rounded-xl flex items-center justify-between text-xs has-[:checked]:border-brand-800 has-[:checked]:bg-brand-50 transition">
                                <input type="radio" name="borda" value="<?= (float)$b->preco ?>" data-nome="<?= esc($b->nome) ?>" <?= $idx === 0 ? 'checked' : '' ?> class="hidden" onchange="recalcularModal()" />
                                <span class="font-medium text-stone-800 truncate pr-1"><?= esc($b->nome) ?></span>
                                <span class="text-stone-500 font-semibold shrink-0">
                                    <?= (float)$b->preco <= 0 ? 'Grátis' : '+ R$ ' . number_format((float)$b->preco, 0) ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- 4. Observações -->
                <div>
                    <label class="block font-semibold text-stone-900 mb-1">Instruções para a Cozinha</label>
                    <input type="text" id="modal-obs" placeholder="Ex: tirar a cebola, massa bem crocante..." 
                           class="w-full bg-stone-50 border border-stone-200 rounded-xl p-2.5 text-xs text-stone-900 placeholder:text-stone-400 focus:outline-none focus:border-brand-800" />
                </div>

            </div>

            <!-- Rodapé Modal -->
            <div class="p-4 bg-stone-50 border-t border-stone-200 flex items-center justify-between gap-3">
                <div>
                    <span class="text-[10px] uppercase tracking-wider text-stone-500 block">Total</span>
                    <span class="font-serif font-bold text-xl text-stone-900" id="modal-preco-total">R$ 0,00</span>
                </div>
                <button onclick="adicionarPizzaMontada()" class="flex-1 max-w-xs bg-brand-800 hover:bg-brand-900 text-white font-semibold py-2.5 px-4 rounded-xl text-xs sm:text-sm transition shadow-sm">
                    Adicionar à Sacola
                </button>
            </div>

        </div>
    </div>

    <!-- DRAWER LATERAL: SACOLA & CHECKOUT -->
    <div id="drawer-sacola" class="fixed inset-0 z-50 bg-stone-950/60 backdrop-blur-xs hidden flex justify-end">
        <div class="bg-white border-l border-stone-200 w-full max-w-md h-full flex flex-col justify-between shadow-2xl">
            
            <div class="p-4 border-b border-stone-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-brand-800">shopping_bag</span>
                    <h3 class="font-serif font-bold text-lg text-stone-900">Sua Sacola</h3>
                </div>
                <button onclick="toggleSacola()" class="w-8 h-8 rounded-full bg-stone-100 hover:bg-stone-200 text-stone-600 flex items-center justify-center">✕</button>
            </div>

            <div class="p-4 overflow-y-auto flex-1 space-y-4">
                <div id="sacola-lista" class="space-y-2"></div>

                <!-- Formulário de Finalização -->
                <div id="form-checkout" class="pt-4 border-t border-stone-200 space-y-3">
                    <h4 class="font-serif font-bold text-sm text-stone-900">Dados do Pedido</h4>
                    
                    <div class="space-y-2 text-xs">
                        <input type="text" id="pedido-nome" value="<?= esc($cliente->nome ?? '') ?>" placeholder="Seu Nome Completo *"
                               class="w-full bg-stone-50 border border-stone-200 rounded-xl p-2.5 text-stone-900 focus:outline-none focus:border-brand-800" />
                        
                        <input type="tel" id="pedido-fone" value="<?= esc($cliente->telefone ?? '') ?>" placeholder="WhatsApp para aviso de entrega *"
                               class="w-full bg-stone-50 border border-stone-200 rounded-xl p-2.5 text-stone-900 focus:outline-none focus:border-brand-800" />
                        
                        <div id="box-endereco">
                            <input type="text" id="pedido-endereco" value="<?= esc($cliente ? $cliente->getEnderecoCompleto() : '') ?>" placeholder="Endereço de entrega (Rua, Nº, Bairro) *"
                                   class="w-full bg-stone-50 border border-stone-200 rounded-xl p-2.5 text-stone-900 focus:outline-none focus:border-brand-800" />
                        </div>
                    </div>

                    <div>
                        <span class="text-xs font-semibold text-stone-700 block mb-1.5">Forma de Pagamento</span>
                        <div class="grid grid-cols-3 gap-1.5">
                            <label class="cursor-pointer border border-stone-200 p-2 rounded-xl text-center has-[:checked]:border-brand-800 has-[:checked]:bg-brand-50 transition">
                                <input type="radio" name="pag" value="pix" checked class="hidden" />
                                <span class="font-bold text-xs text-stone-900 block">PIX</span>
                                <span class="text-[10px] text-emerald-700 font-semibold block">-10% OFF</span>
                            </label>
                            <label class="cursor-pointer border border-stone-200 p-2 rounded-xl text-center has-[:checked]:border-brand-800 has-[:checked]:bg-brand-50 transition">
                                <input type="radio" name="pag" value="cartao" class="hidden" />
                                <span class="font-bold text-xs text-stone-900 block">Cartão</span>
                                <span class="text-[10px] text-stone-500 block">Na entrega</span>
                            </label>
                            <label class="cursor-pointer border border-stone-200 p-2 rounded-xl text-center has-[:checked]:border-brand-800 has-[:checked]:bg-brand-50 transition">
                                <input type="radio" name="pag" value="dinheiro" class="hidden" />
                                <span class="font-bold text-xs text-stone-900 block">Dinheiro</span>
                                <span class="text-[10px] text-stone-500 block">Com troco</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rodapé da Sacola -->
            <div class="p-4 bg-stone-50 border-t border-stone-200 space-y-3">
                <div class="space-y-1 text-xs">
                    <div class="flex justify-between text-stone-500">
                        <span>Subtotal</span>
                        <span id="sacola-subtotal">R$ 0,00</span>
                    </div>
                    <div class="flex justify-between text-stone-500">
                        <span>Taxa de Entrega</span>
                        <span id="sacola-taxa">R$ 5,00</span>
                    </div>
                    <div class="flex justify-between text-sm font-bold text-stone-900 pt-1 border-t border-stone-200">
                        <span>Total Final</span>
                        <span class="text-brand-900" id="sacola-total">R$ 0,00</span>
                    </div>
                </div>

                <button id="btn-enviar-pedido" onclick="enviarPedido()" class="w-full bg-brand-800 hover:bg-brand-900 disabled:opacity-40 text-white font-semibold py-3 rounded-xl text-sm transition shadow-sm">
                    Confirmar e Enviar Pedido
                </button>
            </div>

        </div>
    </div>

    <!-- MODAL DE SUCESSO DO PEDIDO -->
    <div id="modal-sucesso" class="fixed inset-0 z-50 bg-stone-950/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-sm rounded-2xl p-6 text-center shadow-xl">
            <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl mx-auto mb-3">
                ✓
            </div>
            <h3 class="font-serif font-bold text-xl text-stone-900">Pedido Confirmado!</h3>
            <p class="text-xs text-stone-500 mt-1">Seu pedido foi enviado para o forno.</p>
            <div class="my-4 p-3 bg-stone-50 rounded-xl border border-stone-200">
                <span class="text-[11px] text-stone-400 block">Código de Acompanhamento</span>
                <span class="font-serif font-bold text-lg text-brand-900" id="codigo-confirmado">#FORNO-1234</span>
            </div>
            <button onclick="location.reload()" class="w-full bg-stone-900 hover:bg-black text-white font-medium py-2.5 rounded-xl text-xs transition">
                Voltar ao Cardápio
            </button>
        </div>
    </div>

    <!-- JAVASCRIPT ULTRA-LEVE (Pouquíssimas linhas, sem templates complexos de IA) -->
    <script>
        let carrinho = [];
        let modoAtual = 'delivery';
        let pizzaTemp = { id: 0, nome: '', precoBase: 0 };

        function formatarMoeda(val) {
            return "R$ " + Number(val).toFixed(2).replace(".", ",");
        }

        function filtrarCategoria(cat, btn) {
            document.querySelectorAll('.btn-cat').forEach(b => {
                b.className = "btn-cat shrink-0 px-3.5 py-1.5 rounded-full text-xs font-medium bg-white border border-stone-200/90 text-stone-700 hover:border-stone-400 transition";
            });
            btn.className = "btn-cat shrink-0 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-stone-900 text-white transition";

            document.querySelectorAll('.card-produto').forEach(card => {
                card.style.display = (cat === 'todas' || card.dataset.cat === cat) ? 'flex' : 'none';
            });
        }

        function filtrarCards() {
            const termo = document.getElementById('campo-busca').value.toLowerCase();
            document.querySelectorAll('.card-produto').forEach(card => {
                const bate = card.dataset.nome.includes(termo) || card.dataset.desc.includes(termo);
                card.style.display = bate ? 'flex' : 'none';
            });
        }

        function selecionarModo(modo, btn) {
            modoAtual = modo;
            document.querySelectorAll('.btn-modo').forEach(b => {
                b.className = "btn-modo px-3.5 py-1.5 rounded-lg text-stone-600 hover:text-stone-900 transition flex items-center gap-1.5";
            });
            btn.className = "btn-modo active px-3.5 py-1.5 rounded-lg bg-stone-900 text-white transition flex items-center gap-1.5";

            const boxEnd = document.getElementById('box-endereco');
            if (boxEnd) boxEnd.style.display = (modo === 'delivery') ? 'block' : 'none';

            atualizarSacola();
        }

        function abrirModalMontagem(id, nome, preco) {
            pizzaTemp = { id, nome, precoBase: preco };
            document.getElementById('modal-titulo').textContent = nome;
            document.getElementById('modal-sabor-1-nome').textContent = nome;
            document.getElementById('modal-select-sabor-2').value = '';
            document.getElementById('modal-obs').value = '';
            recalcularModal();
            document.getElementById('modal-montagem').classList.remove('hidden');
        }

        function fecharModalMontagem() {
            document.getElementById('modal-montagem').classList.add('hidden');
        }

        function recalcularModal() {
            const tamRadio = document.querySelector('input[name="tam"]:checked');
            const fator = tamRadio ? parseFloat(tamRadio.dataset.fator) : 1.0;

            const bordaRadio = document.querySelector('input[name="borda"]:checked');
            const precoBorda = bordaRadio ? parseFloat(bordaRadio.value) : 0;

            const sel2 = document.getElementById('modal-select-sabor-2');
            let precoBase = pizzaTemp.precoBase;

            if (sel2 && sel2.value) {
                const opt = sel2.options[sel2.selectedIndex];
                const precoS2 = parseFloat(opt.dataset.preco || 0);
                precoBase = Math.max(pizzaTemp.precoBase, precoS2);
            }

            const total = (precoBase * fator) + precoBorda;
            document.getElementById('modal-preco-total').textContent = formatarMoeda(total);
        }

        function adicionarPizzaMontada() {
            const tamRadio = document.querySelector('input[name="tam"]:checked');
            const tamanho = tamRadio ? tamRadio.value : 'Grande';
            const fator = tamRadio ? parseFloat(tamRadio.dataset.fator) : 1.0;

            const bordaRadio = document.querySelector('input[name="borda"]:checked');
            const bordaNome = bordaRadio ? bordaRadio.dataset.nome : 'Tradicional';
            const precoBorda = bordaRadio ? parseFloat(bordaRadio.value) : 0;

            const sel2 = document.getElementById('modal-select-sabor-2');
            let titulo = `Pizza ${tamanho} — ${pizzaTemp.nome}`;
            let precoBase = pizzaTemp.precoBase;

            if (sel2 && sel2.value) {
                const opt = sel2.options[sel2.selectedIndex];
                const nome2 = opt.dataset.nome;
                const precoS2 = parseFloat(opt.dataset.preco || 0);
                precoBase = Math.max(pizzaTemp.precoBase, precoS2);
                titulo = `Pizza ${tamanho} (1/2 ${pizzaTemp.nome} + 1/2 ${nome2})`;
            }

            const obs = document.getElementById('modal-obs').value.trim();
            const total = (precoBase * fator) + precoBorda;

            carrinho.push({
                titulo: titulo,
                detalhes: `Borda: ${bordaNome}${obs ? ' • Obs: ' + obs : ''}`,
                preco: total
            });

            fecharModalMontagem();
            atualizarSacola();
        }

        function adicionarItemAvulso(nome, preco) {
            carrinho.push({
                titulo: nome,
                detalhes: 'Item avulso',
                preco: preco
            });
            atualizarSacola();
        }

        function removerItem(idx) {
            carrinho.splice(idx, 1);
            atualizarSacola();
        }

        function atualizarSacola() {
            const qtd = carrinho.length;
            const subtotal = carrinho.reduce((sum, item) => sum + item.preco, 0);
            const taxa = (modoAtual === 'delivery' && qtd > 0) ? 5.00 : 0.00;
            const total = subtotal + taxa;

            document.getElementById('badge-sacola').textContent = qtd;
            document.getElementById('float-qtd').textContent = `${qtd} ${qtd === 1 ? 'item' : 'itens'}`;
            document.getElementById('float-total').textContent = formatarMoeda(total);
            document.getElementById('barra-flutuante').classList.toggle('hidden', qtd === 0);

            const lista = document.getElementById('sacola-lista');
            if (qtd === 0) {
                lista.innerHTML = `<div class="text-center py-8 text-stone-400 text-xs">Sua sacola está vazia.</div>`;
                document.getElementById('btn-enviar-pedido').disabled = true;
            } else {
                document.getElementById('btn-enviar-pedido').disabled = false;
                lista.innerHTML = carrinho.map((item, i) => `
                    <div class="p-3 bg-stone-50 rounded-xl border border-stone-200/80 flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <span class="font-medium text-stone-900 text-xs block truncate">${item.titulo}</span>
                            <span class="text-[11px] text-stone-500 block truncate">${item.detalhes}</span>
                            <span class="text-xs font-bold text-stone-900 block mt-0.5">${formatarMoeda(item.preco)}</span>
                        </div>
                        <button onclick="removerItem(${i})" class="w-6 h-6 rounded-lg bg-stone-200 hover:bg-red-100 hover:text-red-700 text-stone-600 flex items-center justify-center text-xs">✕</button>
                    </div>
                `).join('');
            }

            document.getElementById('sacola-subtotal').textContent = formatarMoeda(subtotal);
            document.getElementById('sacola-taxa').textContent = formatarMoeda(taxa);
            document.getElementById('sacola-total').textContent = formatarMoeda(total);
        }

        function toggleSacola() {
            document.getElementById('drawer-sacola').classList.toggle('hidden');
        }

        function enviarPedido() {
            const nome = document.getElementById('pedido-nome').value.trim();
            const fone = document.getElementById('pedido-fone').value.trim();

            if (!nome || !fone) {
                alert('Por favor, informe seu Nome e WhatsApp.');
                return;
            }

            const codigo = '#FORNO-' + Math.floor(1000 + Math.random() * 9000);
            document.getElementById('codigo-confirmado').textContent = codigo;

            carrinho = [];
            atualizarSacola();
            toggleSacola();
            document.getElementById('modal-sucesso').classList.remove('hidden');
        }
    </script>
</body>
</html>
