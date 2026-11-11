<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Entrar na Minha Conta — Forno d'Oro</title>
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

    <!-- Header Limpo -->
    <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-stone-200/80 shadow-[0_1px_3px_rgba(0,0,0,0.03)]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
            <a href="<?= base_url('/') ?>" class="flex items-center gap-3 group shrink-0">
                <div class="w-9 h-9 rounded-xl bg-brand-800 text-white flex items-center justify-center font-serif font-bold text-lg shadow-sm group-hover:bg-brand-900 transition">
                    F
                </div>
                <div>
                    <span class="font-serif font-bold text-lg text-stone-900 tracking-tight block leading-none">Forno d'Oro</span>
                    <span class="text-[11px] text-stone-500 font-medium block mt-0.5">Pizzaria Napolitana</span>
                </div>
            </a>
            <a href="<?= base_url('/') ?>" class="text-xs font-semibold text-stone-600 hover:text-stone-900 hover:bg-stone-100 px-3 py-1.5 rounded-xl transition flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]">restaurant_menu</span> Cardápio
            </a>
        </div>
    </header>

    <main class="flex-1 flex items-center justify-center p-4 my-8">
        <div x-data="{ showSenha: false }" class="w-full max-w-md bg-white border border-stone-200/90 rounded-2xl p-6 sm:p-8 shadow-[0_1px_4px_rgba(0,0,0,0.04)]">
            <div class="text-center mb-6">
                <h1 class="font-serif font-bold text-2xl text-stone-900">Entrar na sua conta</h1>
                <p class="text-xs text-stone-500 mt-1">Acesse seus endereços salvos e acompanhe seus pedidos</p>
            </div>

            <?php if (session()->getFlashdata('erro')): ?>
                <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs font-medium flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">error</span>
                    <span><?= esc(session()->getFlashdata('erro')) ?></span>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('sucesso')): ?>
                <div class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    <span><?= esc(session()->getFlashdata('sucesso')) ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('login') ?>" method="post" class="space-y-4 text-xs sm:text-sm">
                <?= csrf_field() ?>

                <div>
                    <label class="block font-medium text-stone-700 mb-1">E-mail</label>
                    <input type="email" name="email" value="<?= old('email') ?>" required placeholder="seu@email.com"
                           class="w-full bg-stone-50/60 border border-stone-300 focus:border-brand-800 focus:bg-white rounded-xl p-2.5 text-stone-900 focus:outline-none transition" />
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="font-medium text-stone-700">Senha</label>
                    </div>
                    <div class="relative">
                        <input :type="showSenha ? 'text' : 'password'" name="senha" required placeholder="Sua senha de acesso"
                               class="w-full bg-stone-50/60 border border-stone-300 focus:border-brand-800 focus:bg-white rounded-xl p-2.5 pr-10 text-stone-900 focus:outline-none transition" />
                        <button type="button" 
                                @click="showSenha = !showSenha" 
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-700 flex items-center justify-center transition">
                            <span class="material-symbols-outlined text-[18px]" x-text="showSenha ? 'visibility_off' : 'visibility'">visibility</span>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full bg-brand-800 hover:bg-brand-900 active:scale-[0.98] text-white font-semibold py-3 rounded-xl text-sm transition shadow-sm mt-1">
                    Entrar
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-stone-100 text-center">
                <p class="text-xs text-stone-500">
                    Ainda não tem conta?
                    <a href="<?= base_url('cadastro') ?>" class="font-semibold text-brand-800 hover:underline">Cadastre-se grátis</a>
                </p>
            </div>
        </div>
    </main>

    <footer class="text-center py-4 text-xs text-stone-400">
        &copy; <?= date('Y') ?> Forno d'Oro Pizzaria Artesanal.
    </footer>

</body>
</html>
