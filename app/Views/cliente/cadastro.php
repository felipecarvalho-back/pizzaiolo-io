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

    <!-- HEADER: Mesmo navbar editorial do Home -->
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

            <!-- Ações Direita -->
            <div class="flex items-center gap-2.5 shrink-0">
                <a href="<?= base_url('login') ?>" class="px-3 py-1.5 rounded-xl text-xs font-semibold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]">person</span>
                    <span class="hidden sm:inline">Já tenho conta</span>
                </a>

                <a href="<?= base_url('/') ?>" class="relative flex items-center gap-2 bg-brand-800 hover:bg-brand-900 active:scale-[0.98] text-white px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">shopping_bag</span>
                    <span class="hidden sm:inline">Sacola</span>
                    <span id="badge-sacola" class="px-2 py-0.5 rounded-full bg-black/25 text-xs font-bold">
                        <?= \App\Controllers\CarrinhoController::contarItensCarrinho(session()->get('carrinho') ?? []) ?>
                    </span>
                </a>
            </div>

        </div>
    </header>

    <main class="flex-1 flex items-center justify-center p-4 my-8">
        <div x-data="cadastroCliente()" class="w-full max-w-lg bg-white border border-stone-200/80 rounded-2xl p-6 sm:p-8 shadow-sm">
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
                           class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-800 focus:bg-white rounded-xl p-3 text-stone-900 focus:outline-none transition" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-stone-700 mb-1">E-mail *</label>
                        <input type="email" name="email" value="<?= old('email') ?>" required placeholder="seu@email.com"
                               class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-800 focus:bg-white rounded-xl p-3 text-stone-900 focus:outline-none transition" />
                    </div>
                    <div>
                        <label class="block font-medium text-stone-700 mb-1">WhatsApp / Telefone *</label>
                        <input type="tel" name="telefone" 
                               x-model="telefone" 
                               @input="formatarTelefone()" 
                               required 
                               placeholder="(11) 99999-9999"
                               class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-800 focus:bg-white rounded-xl p-3 text-stone-900 focus:outline-none transition" />
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="font-medium text-stone-700">Crie uma Senha *</label>
                    </div>
                    <div class="relative">
                        <input :type="showSenha ? 'text' : 'password'" name="senha" required placeholder="Pelo menos 4 caracteres"
                               class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-800 focus:bg-white rounded-xl p-3 pr-10 text-stone-900 focus:outline-none transition" />
                        <button type="button" 
                                @click="showSenha = !showSenha" 
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-700 flex items-center justify-center transition">
                            <span class="material-symbols-outlined text-[18px]" x-text="showSenha ? 'visibility_off' : 'visibility'">visibility</span>
                        </button>
                    </div>
                </div>

                <div class="pt-3 border-t border-stone-100">
                    <div class="flex items-center justify-between mb-2">
                        <span class="block text-xs font-semibold uppercase tracking-wider text-stone-400">Endereço de Entrega (ViaCEP)</span>
                        <span x-show="buscandoCep" x-cloak class="text-[11px] text-brand-800 font-medium animate-pulse flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">sync</span> Buscando CEP...
                        </span>
                    </div>

                    <div class="grid grid-cols-3 gap-2 mb-2">
                        <div>
                            <input type="text" name="cep" 
                                   x-model="cep" 
                                   @input="formatarCep()" 
                                   @blur="consultarCep()" 
                                   maxlength="9" 
                                   placeholder="CEP (00000-000)"
                                   class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-800 focus:bg-white rounded-xl p-2.5 text-stone-900 focus:outline-none transition text-xs" />
                        </div>
                        <div class="col-span-2">
                            <input type="text" name="endereco" 
                                   x-model="endereco" 
                                   value="<?= old('endereco') ?>" 
                                   placeholder="Rua / Avenida"
                                   class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-800 focus:bg-white rounded-xl p-2.5 text-stone-900 focus:outline-none transition text-xs" />
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2 mb-2">
                        <div>
                            <input type="text" name="numero" value="<?= old('numero') ?>" placeholder="Nº"
                                   class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-800 focus:bg-white rounded-xl p-2.5 text-stone-900 focus:outline-none transition text-xs" />
                        </div>
                        <div class="col-span-2">
                            <input type="text" name="bairro" 
                                   x-model="bairro" 
                                   value="<?= old('bairro') ?>" 
                                   placeholder="Bairro"
                                   class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-800 focus:bg-white rounded-xl p-2.5 text-stone-900 focus:outline-none transition text-xs" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <input type="text" name="complemento" value="<?= old('complemento') ?>" placeholder="Complemento (Apto, Bloco, Referência)"
                               class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-800 focus:bg-white rounded-xl p-2.5 text-stone-900 focus:outline-none transition text-xs" />
                        <input type="text" name="cidade" x-model="cidade" value="<?= old('cidade') ?>" placeholder="Cidade"
                               class="w-full bg-stone-50/50 border border-stone-300 focus:border-brand-800 focus:bg-white rounded-xl p-2.5 text-stone-900 focus:outline-none transition text-xs" />
                    </div>
                </div>

                <button type="submit" class="w-full bg-brand-800 hover:bg-brand-900 active:scale-[0.98] text-white font-semibold py-3.5 rounded-xl text-sm transition shadow-sm mt-2">
                    Criar Perfil e Continuar
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-stone-100 text-center">
                <p class="text-xs text-stone-500">
                    Já tem uma conta?
                    <a href="<?= base_url('login') ?>" class="font-semibold text-brand-800 hover:underline">Fazer login</a>
                </p>
            </div>
        </div>
    </main>

    <footer class="text-center py-4 text-xs text-stone-400">
        &copy; <?= date('Y') ?> Forno d'Oro Pizzaria Artesanal.
    </footer>

    <script>
        function cadastroCliente() {
            return {
                showSenha: false,
                telefone: '<?= esc(old('telefone') ?? '') ?>',
                cep: '<?= esc(old('cep') ?? '') ?>',
                endereco: '<?= esc(old('endereco') ?? '') ?>',
                bairro: '<?= esc(old('bairro') ?? '') ?>',
                cidade: '<?= esc(old('cidade') ?? '') ?>',
                buscandoCep: false,

                formatarTelefone() {
                    let v = this.telefone.replace(/\D/g, '');
                    if (v.length > 11) v = v.substring(0, 11);
                    if (v.length > 10) {
                        this.telefone = `(${v.substring(0, 2)}) ${v.substring(2, 7)}-${v.substring(7)}`;
                    } else if (v.length > 6) {
                        this.telefone = `(${v.substring(0, 2)}) ${v.substring(2, 6)}-${v.substring(6)}`;
                    } else if (v.length > 2) {
                        this.telefone = `(${v.substring(0, 2)}) ${v.substring(2)}`;
                    } else if (v.length > 0) {
                        this.telefone = `(${v}`;
                    }
                },

                formatarCep() {
                    let v = this.cep.replace(/\D/g, '');
                    if (v.length > 8) v = v.substring(0, 8);
                    if (v.length > 5) {
                        this.cep = `${v.substring(0, 5)}-${v.substring(5)}`;
                    } else {
                        this.cep = v;
                    }
                    if (v.length === 8) {
                        this.consultarCep();
                    }
                },

                async consultarCep() {
                    const limpo = this.cep.replace(/\D/g, '');
                    if (limpo.length !== 8) return;

                    this.buscandoCep = true;
                    try {
                        const res = await fetch(`https://viacep.com.br/ws/${limpo}/json/`);
                        const data = await res.json();
                        if (!data.erro) {
                            if (data.logradouro) this.endereco = data.logradouro;
                            if (data.bairro) this.bairro = data.bairro;
                            if (data.localidade) this.cidade = data.localidade;
                        }
                    } catch (e) {
                        console.warn('Erro ao consultar ViaCEP', e);
                    } finally {
                        this.buscandoCep = false;
                    }
                }
            };
        }
    </script>

</body>
</html>
