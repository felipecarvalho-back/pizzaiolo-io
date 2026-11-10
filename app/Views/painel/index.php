<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
</head>
<body class="bg-[#faf8f5] text-stone-900 font-sans antialiased min-h-screen flex flex-col">

    <!-- TOPBAR DO PAINEL -->
    <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-stone-200/90 shadow-xs">
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
                    <span>Ver Cardápio</span>
                </a>
                <div class="hidden md:flex items-center gap-2 text-xs text-stone-600 border-l border-stone-200 pl-3">
                    <span class="material-symbols-outlined text-[18px] text-emerald-700">verified_user</span>
                    <span><?= esc(session()->get('usuario_nome')) ?></span>
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
                <span class="material-symbols-outlined">check_circle</span>
                <span><?= esc(session()->getFlashdata('sucesso')) ?></span>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('erro')): ?>
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs sm:text-sm font-medium flex items-center gap-2">
                <span class="material-symbols-outlined">error</span>
                <span><?= esc(session()->getFlashdata('erro')) ?></span>
            </div>
        <?php endif; ?>

        <!-- Resumo de Métricas -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
            <div class="bg-white border border-stone-200/90 p-4 rounded-2xl shadow-xs">
                <span class="text-[11px] uppercase tracking-wider text-stone-500 font-medium block">Cardápio Ativo</span>
                <span class="font-serif font-bold text-2xl text-stone-900 mt-1 block"><?= count($produtos) ?> itens</span>
            </div>
            <div class="bg-white border border-stone-200/90 p-4 rounded-2xl shadow-xs">
                <span class="text-[11px] uppercase tracking-wider text-stone-500 font-medium block">Categorias</span>
                <span class="font-serif font-bold text-2xl text-stone-900 mt-1 block"><?= count($categorias) ?></span>
            </div>
            <div class="bg-white border border-stone-200/90 p-4 rounded-2xl shadow-xs">
                <span class="text-[11px] uppercase tracking-wider text-stone-500 font-medium block">Bordas Cadastradas</span>
                <span class="font-serif font-bold text-2xl text-stone-900 mt-1 block"><?= count($bordas) ?></span>
            </div>
            <div class="bg-white border border-stone-200/90 p-4 rounded-2xl shadow-xs">
                <span class="text-[11px] uppercase tracking-wider text-stone-500 font-medium block">Equipe do Sistema</span>
                <span class="font-serif font-bold text-2xl text-stone-900 mt-1 block"><?= count($usuarios) ?></span>
            </div>
        </div>

        <!-- NAVEGAÇÃO DE ABAS DO PAINEL -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 mb-6 border-b border-stone-200">
            <button onclick="trocarAba('produtos')" id="tab-btn-produtos" class="tab-btn px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-stone-900 text-white flex items-center gap-1.5 transition">
                <span class="material-symbols-outlined text-[18px]">local_pizza</span>
                Produtos & Pizzas
            </button>
            <button onclick="trocarAba('categorias')" id="tab-btn-categorias" class="tab-btn px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-white border border-stone-200 text-stone-600 hover:text-stone-900 flex items-center gap-1.5 transition">
                <span class="material-symbols-outlined text-[18px]">category</span>
                Categorias
            </button>
            <button onclick="trocarAba('bordas')" id="tab-btn-bordas" class="tab-btn px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-white border border-stone-200 text-stone-600 hover:text-stone-900 flex items-center gap-1.5 transition">
                <span class="material-symbols-outlined text-[18px]">radio_button_checked</span>
                Bordas
            </button>
            <button onclick="trocarAba('usuarios')" id="tab-btn-usuarios" class="tab-btn px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-white border border-stone-200 text-stone-600 hover:text-stone-900 flex items-center gap-1.5 transition">
                <span class="material-symbols-outlined text-[18px]">group</span>
                Usuários
            </button>
        </div>

        <!-- ABA 1: PRODUTOS & PIZZAS -->
        <section id="aba-produtos" class="aba-pane grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-5 bg-white border border-stone-200/90 rounded-2xl p-6 shadow-xs">
                <h2 class="font-serif font-bold text-lg text-stone-900 mb-1">Cadastrar Item</h2>
                <p class="text-xs text-stone-500 mb-5">Adicione pizzas ou bebidas ao cardápio digital.</p>

                <form action="<?= base_url('painel/produto/salvar') ?>" method="post" class="space-y-3.5 text-xs sm:text-sm">
                    <?= csrf_field() ?>
                    <div>
                        <label class="block font-medium text-stone-700 mb-1">Nome do Produto *</label>
                        <input type="text" name="nome" required placeholder="Ex: Pepperoni Especial D.O.P"
                               class="w-full bg-stone-50/60 border border-stone-300 focus:border-brand-800 rounded-xl p-2.5 text-stone-900 focus:outline-none transition" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-medium text-stone-700 mb-1">Categoria *</label>
                            <select name="categoria_id" required class="w-full bg-stone-50/60 border border-stone-300 focus:border-brand-800 rounded-xl p-2.5 text-stone-900 focus:outline-none transition">
                                <?php foreach ($categorias as $c): ?>
                                    <option value="<?= $c->id ?>"><?= esc($c->nome) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium text-stone-700 mb-1">Tipo *</label>
                            <select name="tipo" class="w-full bg-stone-50/60 border border-stone-300 focus:border-brand-800 rounded-xl p-2.5 text-stone-900 focus:outline-none transition">
                                <option value="pizza">Pizza (Montável)</option>
                                <option value="simples">Item Simples / Bebida</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 items-center">
                        <div>
                            <label class="block font-medium text-stone-700 mb-1">Preço Base (R$) *</label>
                            <input type="text" name="preco_base" required placeholder="69,90"
                                   class="w-full bg-stone-50/60 border border-stone-300 focus:border-brand-800 rounded-xl p-2.5 text-stone-900 focus:outline-none transition" />
                        </div>
                        <div class="pt-5">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="destaque" value="1" class="accent-brand-800 w-4 h-4 rounded" />
                                <span class="text-xs font-medium text-stone-700">Destaque do Chef</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium text-stone-700 mb-1">Ingredientes / Descrição</label>
                        <textarea name="descricao" rows="2" placeholder="Descreva os ingredientes..."
                                  class="w-full bg-stone-50/60 border border-stone-300 focus:border-brand-800 rounded-xl p-2.5 text-stone-900 focus:outline-none transition"></textarea>
                    </div>

                    <div>
                        <label class="block font-medium text-stone-700 mb-1">URL da Imagem (Opcional)</label>
                        <input type="url" name="imagem" placeholder="https://..."
                               class="w-full bg-stone-50/60 border border-stone-300 focus:border-brand-800 rounded-xl p-2.5 text-stone-900 focus:outline-none transition" />
                    </div>

                    <button type="submit" class="w-full bg-brand-800 hover:bg-brand-900 text-white font-semibold py-2.5 rounded-xl shadow-xs transition">
                        Salvar Item
                    </button>
                </form>
            </div>

            <div class="lg:col-span-7 bg-white border border-stone-200/90 rounded-2xl p-6 shadow-xs">
                <h3 class="font-serif font-bold text-lg text-stone-900 mb-4">Itens Cadastrados (<?= count($produtos) ?>)</h3>
                <div class="space-y-3 max-h-[520px] overflow-y-auto pr-1">
                    <?php foreach ($produtos as $p): ?>
                        <div class="bg-stone-50 border border-stone-200/70 p-3 rounded-xl flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <img src="<?= esc($p->imagem) ?>" alt="<?= esc($p->nome) ?>" class="w-12 h-12 rounded-lg object-cover bg-stone-200 shrink-0" />
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-semibold text-stone-900 text-sm truncate"><?= esc($p->nome) ?></h4>
                                        <?php if ($p->destaque): ?>
                                            <span class="px-2 py-0.2 rounded bg-amber-100 text-amber-900 text-[10px] font-bold">Destaque</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-xs text-stone-500 truncate"><?= esc($p->categoria_nome ?? 'Sem categoria') ?></p>
                                    <span class="text-xs font-bold text-stone-900">R$ <?= number_format((float) $p->preco_base, 2, ',', '.') ?></span>
                                </div>
                            </div>
                            <a href="<?= base_url('painel/produto/excluir/' . $p->id) ?>"
                               onclick="return confirm('Deseja remover este item do cardápio?')"
                               class="w-8 h-8 rounded-lg hover:bg-red-50 text-stone-400 hover:text-red-700 flex items-center justify-center shrink-0 transition">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- ABA 2: CATEGORIAS -->
        <section id="aba-categorias" class="aba-pane hidden grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
            <div class="md:col-span-5 bg-white border border-stone-200/90 rounded-2xl p-6 shadow-xs">
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

            <div class="md:col-span-7 bg-white border border-stone-200/90 rounded-2xl p-6 shadow-xs">
                <h3 class="font-serif font-bold text-lg text-stone-900 mb-4">Categorias Ativas</h3>
                <div class="space-y-2.5">
                    <?php foreach ($categorias as $c): ?>
                        <div class="bg-stone-50 border border-stone-200/70 p-3 rounded-xl flex items-center justify-between">
                            <div>
                                <span class="font-semibold text-stone-900 text-sm block"><?= esc($c->nome) ?></span>
                                <span class="text-xs text-stone-500">Slug: <?= esc($c->slug) ?></span>
                            </div>
                            <span class="px-2 py-0.5 rounded-md bg-stone-200 text-stone-700 text-xs font-semibold">Ordem <?= $c->ordem ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- ABA 3: BORDAS -->
        <section id="aba-bordas" class="aba-pane hidden grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
            <div class="md:col-span-5 bg-white border border-stone-200/90 rounded-2xl p-6 shadow-xs">
                <h2 class="font-serif font-bold text-lg text-stone-900 mb-4">Nova Borda Recheada</h2>
                <form action="<?= base_url('painel/borda/salvar') ?>" method="post" class="space-y-3.5 text-xs sm:text-sm">
                    <?= csrf_field() ?>
                    <div>
                        <label class="block font-medium text-stone-700 mb-1">Nome da Borda *</label>
                        <input type="text" name="nome" required placeholder="Ex: Borda Vulcão Catupiry" class="w-full bg-stone-50/60 border border-stone-300 focus:border-brand-800 rounded-xl p-2.5 text-stone-900 focus:outline-none transition" />
                    </div>
                    <div>
                        <label class="block font-medium text-stone-700 mb-1">Valor Adicional (R$) *</label>
                        <input type="text" name="preco" required placeholder="14,00" class="w-full bg-stone-50/60 border border-stone-300 focus:border-brand-800 rounded-xl p-2.5 text-stone-900 focus:outline-none transition" />
                    </div>
                    <button type="submit" class="w-full bg-brand-800 hover:bg-brand-900 text-white font-semibold py-2.5 rounded-xl transition">
                        Salvar Borda
                    </button>
                </form>
            </div>

            <div class="md:col-span-7 bg-white border border-stone-200/90 rounded-2xl p-6 shadow-xs">
                <h3 class="font-serif font-bold text-lg text-stone-900 mb-4">Bordas Disponíveis</h3>
                <div class="space-y-2.5">
                    <?php foreach ($bordas as $b): ?>
                        <div class="bg-stone-50 border border-stone-200/70 p-3 rounded-xl flex items-center justify-between">
                            <span class="font-semibold text-stone-900 text-sm"><?= esc($b->nome) ?></span>
                            <span class="font-bold text-sm text-stone-900">+ R$ <?= number_format((float) $b->preco, 2, ',', '.') ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- ABA 4: USUÁRIOS -->
        <section id="aba-usuarios" class="aba-pane hidden grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
            <div class="md:col-span-5 bg-white border border-stone-200/90 rounded-2xl p-6 shadow-xs">
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

            <div class="md:col-span-7 bg-white border border-stone-200/90 rounded-2xl p-6 shadow-xs">
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
                            <?php if ($u->id !== (int) session()->get('usuario_id')): ?>
                                <a href="<?= base_url('painel/usuario/excluir/' . $u->id) ?>"
                                   onclick="return confirm('Excluir este usuário?')"
                                   class="w-8 h-8 rounded-lg hover:bg-red-50 text-stone-400 hover:text-red-700 flex items-center justify-center transition">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </a>
                            <?php else: ?>
                                <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-semibold">Você</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

    </main>

    <script>
        function trocarAba(aba) {
            document.querySelectorAll('.aba-pane').forEach(el => el.classList.add('hidden'));
            document.getElementById('aba-' + aba).classList.remove('hidden');

            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.className = "tab-btn px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-white border border-stone-200 text-stone-600 hover:text-stone-900 flex items-center gap-1.5 transition";
            });
            document.getElementById('tab-btn-' + aba).className = "tab-btn px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-stone-900 text-white flex items-center gap-1.5 transition";
        }

        const params = new URLSearchParams(window.location.search);
        const abaUrl = params.get('aba');
        if (abaUrl && document.getElementById('aba-' + abaUrl)) {
            trocarAba(abaUrl);
        }
    </script>
</body>
</html>
