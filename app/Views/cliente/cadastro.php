<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Criar Minha Conta — Forno d'Oro</title>
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

    <header class="border-b border-stone-200 bg-white/80 backdrop-blur-md">
        <div class="max-w-5xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="<?= base_url('/') ?>" class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-brand-700 text-white flex items-center justify-center text-sm font-serif font-bold">F</span>
                <span class="font-serif font-bold text-lg text-stone-900 tracking-tight">Forno d'Oro</span>
            </a>
            <a href="<?= base_url('/') ?>" class="text-xs font-medium text-stone-500 hover:text-stone-900 flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Voltar ao Cardápio
            </a>
        </div>
    </header>

    <main class="flex-1 flex items-center justify-center p-4 my-8">
        <div class="w-full max-w-lg bg-white border border-stone-200/80 rounded-2xl p-6 sm:p-8 shadow-sm">
            <div class="mb-6">
                <h1 class="font-serif font-bold text-2xl text-stone-900">Crie seu perfil</h1>
                <p class="text-xs text-stone-500 mt-1">Cadastre-se uma vez para pedir com 1 clique e receber ofertas exclusivas</p>
            </div>

            <?php if (session()->getFlashdata('erro')): ?>
                <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs font-medium flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">error</span>
                    <span><?= esc(session()->getFlashdata('erro')) ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('cliente/cadastro') ?>" method="post" class="space-y-4 text-xs sm:text-sm">
                <?= csrf_field() ?>

                <div>
                    <label class="block font-medium text-stone-700 mb-1">Nome Completo *</label>
                    <input type="text" name="nome" value="<?= old('nome') ?>" required placeholder="Ex: Felipe Silva"
                           class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-600 focus:bg-white rounded-xl p-3 text-stone-900 focus:outline-none transition" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-stone-700 mb-1">E-mail *</label>
                        <input type="email" name="email" value="<?= old('email') ?>" required placeholder="seu@email.com"
                               class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-600 focus:bg-white rounded-xl p-3 text-stone-900 focus:outline-none transition" />
                    </div>
                    <div>
                        <label class="block font-medium text-stone-700 mb-1">WhatsApp / Telefone *</label>
                        <input type="tel" name="telefone" value="<?= old('telefone') ?>" required placeholder="(11) 99999-9999"
                               class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-600 focus:bg-white rounded-xl p-3 text-stone-900 focus:outline-none transition" />
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-stone-700 mb-1">Crie uma Senha *</label>
                    <input type="password" name="senha" required placeholder="Pelo menos 4 caracteres"
                           class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-600 focus:bg-white rounded-xl p-3 text-stone-900 focus:outline-none transition" />
                </div>

                <div class="pt-3 border-t border-stone-100">
                    <span class="block text-xs font-semibold uppercase tracking-wider text-stone-400 mb-2">Endereço de Entrega Principal (Opcional)</span>
                    
                    <div class="grid grid-cols-3 gap-2 mb-2">
                        <div class="col-span-2">
                            <input type="text" name="endereco" value="<?= old('endereco') ?>" placeholder="Rua / Avenida"
                                   class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-600 focus:bg-white rounded-xl p-2.5 text-stone-900 focus:outline-none transition text-xs" />
                        </div>
                        <div>
                            <input type="text" name="numero" value="<?= old('numero') ?>" placeholder="Nº"
                                   class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-600 focus:bg-white rounded-xl p-2.5 text-stone-900 focus:outline-none transition text-xs" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" name="bairro" value="<?= old('bairro') ?>" placeholder="Bairro"
                               class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-600 focus:bg-white rounded-xl p-2.5 text-stone-900 focus:outline-none transition text-xs" />
                        <input type="text" name="complemento" value="<?= old('complemento') ?>" placeholder="Apto / Bloco"
                               class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-600 focus:bg-white rounded-xl p-2.5 text-stone-900 focus:outline-none transition text-xs" />
                    </div>
                </div>

                <button type="submit" class="w-full bg-brand-700 hover:bg-brand-800 text-white font-semibold py-3.5 rounded-xl text-sm transition shadow-sm mt-2">
                    Criar Perfil e Continuar
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-stone-100 text-center">
                <p class="text-xs text-stone-500">
                    Já tem uma conta?
                    <a href="<?= base_url('cliente/login') ?>" class="font-semibold text-brand-700 hover:underline">Fazer login</a>
                </p>
            </div>
        </div>
    </main>

    <footer class="text-center py-4 text-xs text-stone-400">
        &copy; <?= date('Y') ?> Forno d'Oro Pizzaria Artesanal.
    </footer>

</body>
</html>
