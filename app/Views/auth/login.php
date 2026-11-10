<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Acesso Restrito — Forno d'Oro</title>
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
<body class="bg-[#faf8f5] text-stone-900 font-sans antialiased min-h-screen flex flex-col justify-between p-4">

    <!-- Topbar simples -->
    <header class="max-w-md mx-auto w-full pt-4 flex justify-between items-center">
        <a href="<?= base_url('/') ?>" class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-brand-800 text-white flex items-center justify-center font-serif font-bold text-sm">F</span>
            <span class="font-serif font-bold text-lg text-stone-900">Forno d'Oro</span>
        </a>
        <a href="<?= base_url('/') ?>" class="text-xs font-medium text-stone-500 hover:text-stone-900 transition flex items-center gap-1">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Cardápio
        </a>
    </header>

    <main class="w-full max-w-md mx-auto my-auto">
        <div class="bg-white border border-stone-200/90 rounded-2xl p-6 sm:p-8 shadow-[0_1px_4px_rgba(0,0,0,0.04)]">
            
            <div class="text-center mb-6">
                <span class="text-[11px] uppercase tracking-wider text-stone-400 font-bold block mb-1">Painel Interno</span>
                <h1 class="font-serif font-bold text-2xl text-stone-900">Acesso Administrativo</h1>
                <p class="text-xs text-stone-500 mt-1">Gestão de cardápio, pedidos e equipe</p>
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
                    <input type="email" name="email" value="<?= old('email', 'admin@fornodoro.com') ?>" required
                           class="w-full bg-stone-50/60 border border-stone-300 focus:border-brand-800 focus:bg-white rounded-xl p-2.5 text-stone-900 focus:outline-none transition" />
                </div>

                <div>
                    <label class="block font-medium text-stone-700 mb-1">Senha</label>
                    <input type="password" name="senha" value="123456" required
                           class="w-full bg-stone-50/60 border border-stone-300 focus:border-brand-800 focus:bg-white rounded-xl p-2.5 text-stone-900 focus:outline-none transition" />
                </div>

                <button type="submit" class="w-full bg-brand-800 hover:bg-brand-900 active:scale-[0.98] text-white font-semibold py-3 rounded-xl text-sm transition shadow-sm mt-2">
                    Entrar no Painel
                </button>
            </form>

            <div class="mt-6 p-3 rounded-xl bg-stone-50 border border-stone-200 text-center">
                <span class="text-[11px] text-stone-500 block">Credenciais pré-configuradas:</span>
                <span class="text-xs font-mono font-medium text-stone-800">admin@fornodoro.com • 123456</span>
            </div>
        </div>
    </main>

    <footer class="text-center py-4 text-xs text-stone-400">
        &copy; <?= date('Y') ?> Forno d'Oro — Gestão Gastronômica
    </footer>

</body>
</html>
