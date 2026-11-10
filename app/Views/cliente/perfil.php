<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Meu Perfil — Forno d'Oro</title>
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
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                        },
                    },
                    fontFamily: {
                        serif: ['"Playfair Display"', 'serif'],
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        };
    </script>
</head>
<body class="bg-[#fbf9f6] text-stone-800 font-sans antialiased min-h-screen flex flex-col justify-between">

    <!-- Header Elegante -->
    <header class="border-b border-stone-200 bg-white/80 backdrop-blur-md sticky top-0 z-20">
        <div class="max-w-5xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="<?= base_url('/') ?>" class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-brand-700 text-white flex items-center justify-center text-sm font-serif font-bold">F</span>
                <span class="font-serif font-bold text-lg text-stone-900 tracking-tight">Forno d'Oro</span>
            </a>
            <div class="flex items-center gap-3">
                <a href="<?= base_url('/') ?>" class="px-3.5 py-1.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-xs font-semibold text-stone-800 transition flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-brand-700">restaurant_menu</span>
                    <span>Pedir Agora</span>
                </a>
                <a href="<?= base_url('cliente/logout') ?>" class="text-xs font-medium text-stone-500 hover:text-red-700 transition">
                    Sair
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto w-full px-4 py-8 flex-1">

        <?php if (session()->getFlashdata('sucesso')): ?>
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-medium flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                <span><?= esc(session()->getFlashdata('sucesso')) ?></span>
            </div>
        <?php endif; ?>

        <!-- Perfil Header -->
        <div class="bg-white border border-stone-200/80 rounded-2xl p-6 sm:p-8 mb-8 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-stone-100 border border-stone-200 text-brand-800 font-serif font-bold text-2xl flex items-center justify-center uppercase">
                    <?= esc(substr($cliente->nome, 0, 1)) ?>
                </div>
                <div>
                    <h1 class="font-serif font-bold text-2xl text-stone-900"><?= esc($cliente->nome) ?></h1>
                    <p class="text-xs text-stone-500 mt-0.5"><?= esc($cliente->email) ?> • <?= esc($cliente->telefone) ?></p>
                    <span class="inline-block mt-2 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-semibold">Cliente Ativo</span>
                </div>
            </div>
            <a href="<?= base_url('/') ?>" class="inline-flex items-center justify-center gap-2 bg-brand-700 hover:bg-brand-800 text-white font-semibold px-5 py-2.5 rounded-xl text-xs sm:text-sm transition shadow-sm">
                <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                Novo Pedido
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Coluna 1: Atualização de Dados e Endereço -->
            <div class="lg:col-span-5 bg-white border border-stone-200/80 rounded-2xl p-6 shadow-sm">
                <h2 class="font-serif font-bold text-lg text-stone-900 mb-1">Meus Dados & Endereço</h2>
                <p class="text-xs text-stone-500 mb-5">Mantenha seu endereço atualizado para entregas rápidas.</p>

                <form action="<?= base_url('cliente/perfil') ?>" method="post" class="space-y-3.5 text-xs sm:text-sm">
                    <?= csrf_field() ?>

                    <div>
                        <label class="block font-medium text-stone-700 mb-1">Nome Completo</label>
                        <input type="text" name="nome" value="<?= esc($cliente->nome) ?>" required
                               class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-600 focus:bg-white rounded-xl p-2.5 text-stone-900 focus:outline-none transition" />
                    </div>

                    <div>
                        <label class="block font-medium text-stone-700 mb-1">WhatsApp / Telefone</label>
                        <input type="tel" name="telefone" value="<?= esc($cliente->telefone) ?>" required
                               class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-600 focus:bg-white rounded-xl p-2.5 text-stone-900 focus:outline-none transition" />
                    </div>

                    <div class="pt-3 border-t border-stone-100">
                        <span class="block text-xs font-semibold uppercase tracking-wider text-stone-400 mb-2">Endereço de Entrega</span>
                        
                        <div class="grid grid-cols-3 gap-2 mb-2">
                            <div class="col-span-2">
                                <label class="block text-[11px] text-stone-500 mb-0.5">Rua / Logradouro</label>
                                <input type="text" name="endereco" value="<?= esc($cliente->endereco) ?>" placeholder="Rua..."
                                       class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-600 focus:bg-white rounded-xl p-2 text-stone-900 focus:outline-none transition text-xs" />
                            </div>
                            <div>
                                <label class="block text-[11px] text-stone-500 mb-0.5">Número</label>
                                <input type="text" name="numero" value="<?= esc($cliente->numero) ?>" placeholder="123"
                                       class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-600 focus:bg-white rounded-xl p-2 text-stone-900 focus:outline-none transition text-xs" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 mb-2">
                            <div>
                                <label class="block text-[11px] text-stone-500 mb-0.5">Bairro</label>
                                <input type="text" name="bairro" value="<?= esc($cliente->bairro) ?>" placeholder="Bairro"
                                       class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-600 focus:bg-white rounded-xl p-2 text-stone-900 focus:outline-none transition text-xs" />
                            </div>
                            <div>
                                <label class="block text-[11px] text-stone-500 mb-0.5">Complemento</label>
                                <input type="text" name="complemento" value="<?= esc($cliente->complemento) ?>" placeholder="Apto 42"
                                       class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-600 focus:bg-white rounded-xl p-2 text-stone-900 focus:outline-none transition text-xs" />
                            </div>
                        </div>
                    </div>

                    <div class="pt-2">
                        <label class="block font-medium text-stone-700 mb-1">Alterar Senha (Opcional)</label>
                        <input type="password" name="nova_senha" placeholder="Deixe em branco para não mudar"
                               class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-600 focus:bg-white rounded-xl p-2 text-stone-900 focus:outline-none transition text-xs" />
                    </div>

                    <button type="submit" class="w-full bg-stone-900 hover:bg-black text-white font-medium py-2.5 rounded-xl text-xs sm:text-sm transition mt-3">
                        Salvar Alterações
                    </button>
                </form>
            </div>

            <!-- Coluna 2: Histórico de Pedidos -->
            <div class="lg:col-span-7 bg-white border border-stone-200/80 rounded-2xl p-6 shadow-sm">
                <h2 class="font-serif font-bold text-lg text-stone-900 mb-1">Histórico de Pedidos</h2>
                <p class="text-xs text-stone-500 mb-5">Acompanhe seus pedidos anteriores e status atual.</p>

                <?php if (empty($pedidos)): ?>
                    <div class="text-center py-12 px-4 border border-dashed border-stone-200 rounded-xl">
                        <span class="material-symbols-outlined text-4xl text-stone-300 mb-2 block">receipt_long</span>
                        <h4 class="font-medium text-stone-700 text-sm">Você ainda não realizou nenhum pedido</h4>
                        <p class="text-xs text-stone-400 mt-1 mb-4">Escolha sua pizza favorita feita no forno a lenha.</p>
                        <a href="<?= base_url('/') ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-semibold transition">
                            Explorar Cardápio
                        </a>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($pedidos as $p): ?>
                            <div class="border border-stone-200 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-stone-900 text-sm"><?= esc($p->codigo) ?></span>
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold capitalize bg-amber-50 text-amber-800">
                                            <?= str_replace('_', ' ', esc($p->status)) ?>
                                        </span>
                                    </div>
                                    <span class="text-xs text-stone-500 block mt-1">
                                        <?= date('d/m/Y \à\s H:i', strtotime($p->created_at ?? 'now')) ?> • <?= ucfirst(esc($p->tipo_entrega)) ?>
                                    </span>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-stone-900 text-sm block">R$ <?= number_format((float) $p->total_geral, 2, ',', '.') ?></span>
                                    <span class="text-[11px] text-stone-500 capitalize"><?= str_replace('_', ' ', esc($p->forma_pagamento)) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>

    </main>

    <footer class="text-center py-4 text-xs text-stone-400 border-t border-stone-200/60 mt-12 bg-white">
        &copy; <?= date('Y') ?> Forno d'Oro Pizzaria Artesanal.
    </footer>

</body>
</html>
