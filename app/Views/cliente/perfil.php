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
    </style>
</head>
<body class="bg-[#faf8f5] text-stone-900 font-sans antialiased min-h-screen flex flex-col justify-between selection:bg-brand-100 selection:text-brand-900">

    <!-- Header Elegante -->
    <header class="border-b border-stone-200/80 bg-white/95 backdrop-blur-md sticky top-0 z-20 shadow-[0_1px_3px_rgba(0,0,0,0.03)]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="<?= base_url('/') ?>" class="flex items-center gap-3 group">
                <div class="w-9 h-9 rounded-xl bg-brand-800 text-white flex items-center justify-center font-serif font-bold text-lg shadow-sm group-hover:bg-brand-900 transition">
                    F
                </div>
                <div>
                    <span class="font-serif font-bold text-lg text-stone-900 tracking-tight block leading-none">Forno d'Oro</span>
                    <span class="text-[11px] text-stone-500 font-medium block mt-0.5">Pizzaria Napolitana</span>
                </div>
            </a>
            <div class="flex items-center gap-3">
                <a href="<?= base_url('/') ?>" class="px-3.5 py-1.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-xs font-semibold text-stone-800 transition flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-brand-800">restaurant_menu</span>
                    <span>Cardápio</span>
                </a>
                <a href="<?= base_url('cliente/logout') ?>" class="text-xs font-semibold text-stone-500 hover:text-red-700 transition px-2 py-1">
                    Sair
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto w-full px-4 sm:px-6 py-8 flex-1">

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

        <!-- Perfil Header -->
        <div class="bg-white border border-stone-200/90 rounded-2xl p-6 sm:p-8 mb-8 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-brand-50 border border-brand-200 text-brand-800 font-serif font-bold text-2xl flex items-center justify-center uppercase shadow-2xs">
                    <?= esc(substr($cliente->nome, 0, 1)) ?>
                </div>
                <div>
                    <h1 class="font-serif font-bold text-2xl text-stone-900"><?= esc($cliente->nome) ?></h1>
                    <p class="text-xs text-stone-500 mt-0.5"><?= esc($cliente->email) ?> • <?= esc($cliente->telefone) ?></p>
                    <span class="inline-block mt-2 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/60 text-[11px] font-semibold">Cliente Ativo</span>
                </div>
            </div>
            <a href="<?= base_url('/') ?>" class="inline-flex items-center justify-center gap-2 bg-brand-800 hover:bg-brand-900 active:scale-[0.98] text-white font-semibold px-5 py-2.5 rounded-xl text-xs sm:text-sm transition shadow-sm">
                <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                Pedir Pizza Agora
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Coluna 1: Atualização de Dados e Endereço -->
            <div x-data="{ showNovaSenha: false, telefone: '<?= esc($cliente->telefone) ?>' }" class="lg:col-span-5 bg-white border border-stone-200/90 rounded-2xl p-6 shadow-sm">
                <h2 class="font-serif font-bold text-lg text-stone-900 mb-1">Meus Dados & Endereço</h2>
                <p class="text-xs text-stone-500 mb-5">Mantenha seu endereço atualizado para entregas rápidas.</p>

                <form action="<?= base_url('cliente/perfil') ?>" method="post" class="space-y-3.5 text-xs sm:text-sm">
                    <?= csrf_field() ?>

                    <div>
                        <label class="block font-medium text-stone-700 mb-1">Nome Completo</label>
                        <input type="text" name="nome" value="<?= esc($cliente->nome) ?>" required
                               class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-800 focus:bg-white rounded-xl p-2.5 text-stone-900 focus:outline-none transition" />
                    </div>

                    <div>
                        <label class="block font-medium text-stone-700 mb-1">WhatsApp / Telefone</label>
                        <input type="tel" name="telefone" x-model="telefone" required
                               class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-800 focus:bg-white rounded-xl p-2.5 text-stone-900 focus:outline-none transition" />
                    </div>

                    <div class="pt-3 border-t border-stone-100">
                        <span class="block text-xs font-semibold uppercase tracking-wider text-stone-400 mb-2">Endereço de Entrega Principal</span>
                        
                        <div class="grid grid-cols-3 gap-2 mb-2">
                            <div class="col-span-2">
                                <label class="block text-[11px] text-stone-500 mb-0.5">Rua / Logradouro</label>
                                <input type="text" name="endereco" value="<?= esc($cliente->endereco) ?>" placeholder="Rua..."
                                       class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-800 focus:bg-white rounded-xl p-2 text-stone-900 focus:outline-none transition text-xs" />
                            </div>
                            <div>
                                <label class="block text-[11px] text-stone-500 mb-0.5">Número</label>
                                <input type="text" name="numero" value="<?= esc($cliente->numero) ?>" placeholder="123"
                                       class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-800 focus:bg-white rounded-xl p-2 text-stone-900 focus:outline-none transition text-xs" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 mb-2">
                            <div>
                                <label class="block text-[11px] text-stone-500 mb-0.5">Bairro</label>
                                <input type="text" name="bairro" value="<?= esc($cliente->bairro) ?>" placeholder="Bairro"
                                       class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-800 focus:bg-white rounded-xl p-2 text-stone-900 focus:outline-none transition text-xs" />
                            </div>
                            <div>
                                <label class="block text-[11px] text-stone-500 mb-0.5">Complemento</label>
                                <input type="text" name="complemento" value="<?= esc($cliente->complemento) ?>" placeholder="Apto 42"
                                       class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-800 focus:bg-white rounded-xl p-2 text-stone-900 focus:outline-none transition text-xs" />
                            </div>
                        </div>
                    </div>

                    <div class="pt-2">
                        <label class="block font-medium text-stone-700 mb-1">Alterar Senha (Opcional)</label>
                        <div class="relative">
                            <input :type="showNovaSenha ? 'text' : 'password'" name="nova_senha" placeholder="Deixe em branco para não mudar"
                                   class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-800 focus:bg-white rounded-xl p-2 pr-10 text-stone-900 focus:outline-none transition text-xs" />
                            <button type="button" 
                                    @click="showNovaSenha = !showNovaSenha" 
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-700 flex items-center justify-center transition">
                                <span class="material-symbols-outlined text-[16px]" x-text="showNovaSenha ? 'visibility_off' : 'visibility'">visibility</span>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-stone-900 hover:bg-black text-white font-medium py-2.5 rounded-xl text-xs sm:text-sm transition mt-3">
                        Salvar Alterações
                    </button>
                </form>
            </div>

            <!-- Coluna 2: Histórico de Pedidos com Badges Semânticos -->
            <div class="lg:col-span-7 bg-white border border-stone-200/90 rounded-2xl p-6 shadow-sm">
                <h2 class="font-serif font-bold text-lg text-stone-900 mb-1">Histórico de Pedidos</h2>
                <p class="text-xs text-stone-500 mb-5">Acompanhe seus pedidos anteriores e status atual.</p>

                <?php if (empty($pedidos)): ?>
                    <div class="text-center py-12 px-4 border border-dashed border-stone-200 rounded-xl">
                        <span class="material-symbols-outlined text-4xl text-stone-300 mb-2 block">receipt_long</span>
                        <h4 class="font-medium text-stone-700 text-sm">Você ainda não realizou nenhum pedido</h4>
                        <p class="text-xs text-stone-400 mt-1 mb-4">Escolha sua pizza favorita feita no forno a lenha.</p>
                        <a href="<?= base_url('/') ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-brand-800 hover:bg-brand-900 text-white text-xs font-semibold transition">
                            Explorar Cardápio
                        </a>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($pedidos as $p): ?>
                            <?php
                            $badgeColor = match($p->status) {
                                'entregue'     => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                'confirmado'   => 'bg-blue-50 text-blue-800 border-blue-200',
                                'em_preparo'   => 'bg-purple-50 text-purple-800 border-purple-200',
                                'saiu_entrega' => 'bg-cyan-50 text-cyan-800 border-cyan-200',
                                'cancelado'    => 'bg-rose-50 text-rose-800 border-rose-200',
                                default        => 'bg-amber-50 text-amber-800 border-amber-200',
                            };
                            $statusLabels = [
                                'pendente'     => 'Pendente',
                                'confirmado'   => 'Confirmado',
                                'em_preparo'   => 'Em Preparo',
                                'saiu_entrega' => 'Saiu para Entrega',
                                'entregue'     => 'Entregue',
                                'cancelado'    => 'Cancelado',
                            ];
                            ?>
                            <div class="border border-stone-200/90 hover:border-stone-300 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 transition">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-stone-900 text-sm"><?= esc($p->codigo) ?></span>
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold border <?= $badgeColor ?>">
                                            <?= $statusLabels[$p->status] ?? ucfirst(str_replace('_', ' ', esc($p->status))) ?>
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
