<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InicialSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // 1. Usuário Admin Padrão
        $existeAdmin = $this->db->table('usuarios')->where('email', 'admin@fornodoro.com')->countAllResults();
        if ($existeAdmin === 0) {
            $this->db->table('usuarios')->insert([
                'nome'       => 'Chef Administrador',
                'email'      => 'admin@fornodoro.com',
                'senha'      => password_hash('123456', PASSWORD_DEFAULT),
                'cargo'      => 'admin',
                'ativo'      => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 2. Categorias Iniciais
        if ($this->db->table('categorias')->countAllResults() === 0) {
            $categorias = [
                ['nome' => 'Clássicas Napolitanas', 'slug' => 'tradicionais', 'descricao' => 'Receitas tradicionais italianas', 'ordem' => 1, 'ativo' => 1, 'created_at' => $now],
                ['nome' => 'Especiais do Chef',     'slug' => 'especiais',    'descricao' => 'Combinações autorais gourmet',    'ordem' => 2, 'ativo' => 1, 'created_at' => $now],
                ['nome' => 'Doces Artesanais',      'slug' => 'doces',        'descricao' => 'Sobremesas assadas na lenha',     'ordem' => 3, 'ativo' => 1, 'created_at' => $now],
                ['nome' => 'Bebidas',               'slug' => 'bebidas',      'descricao' => 'Refrigerantes e sodas artesanais','ordem' => 4, 'ativo' => 1, 'created_at' => $now],
            ];
            $this->db->table('categorias')->insertBatch($categorias);
        }

        // 3. Tamanhos Iniciais
        if ($this->db->table('tamanhos')->countAllResults() === 0) {
            $tamanhos = [
                ['nome' => 'Broto',  'sigla' => 'P', 'fatias' => 4, 'max_sabores' => 1, 'ordem' => 1, 'ativo' => 1, 'created_at' => $now],
                ['nome' => 'Média',  'sigla' => 'M', 'fatias' => 6, 'max_sabores' => 2, 'ordem' => 2, 'ativo' => 1, 'created_at' => $now],
                ['nome' => 'Grande', 'sigla' => 'G', 'fatias' => 8, 'max_sabores' => 2, 'ordem' => 3, 'ativo' => 1, 'created_at' => $now],
            ];
            $this->db->table('tamanhos')->insertBatch($tamanhos);
        }

        // 4. Bordas Iniciais
        if ($this->db->table('bordas')->countAllResults() === 0) {
            $bordas = [
                ['nome' => 'Tradicional (Sem Recheio)', 'preco' => 0.00,  'ativo' => 1, 'created_at' => $now],
                ['nome' => 'Catupiry Original',         'preco' => 12.00, 'ativo' => 1, 'created_at' => $now],
                ['nome' => 'Cheddar Melt',              'preco' => 12.00, 'ativo' => 1, 'created_at' => $now],
                ['nome' => 'Cream Cheese Philadelphia', 'preco' => 14.00, 'ativo' => 1, 'created_at' => $now],
            ];
            $this->db->table('bordas')->insertBatch($bordas);
        }

        // 5. Produtos Iniciais
        if ($this->db->table('produtos')->countAllResults() === 0) {
            $catMap = [];
            foreach ($this->db->table('categorias')->get()->getResultArray() as $c) {
                $catMap[$c['slug']] = $c['id'];
            }

            $produtos = [
                [
                    'categoria_id' => $catMap['tradicionais'] ?? 1,
                    'nome'         => 'Margherita di Bufala',
                    'slug'         => 'margherita-di-bufala',
                    'descricao'    => 'Molho pomodoro San Marzano D.O.P, mozzarella de búfala fresca, folhas de manjericão genovês e azeite extravirgem.',
                    'tipo'         => 'pizza',
                    'preco_base'   => 68.00,
                    'imagem'       => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDXCJbgOLcySP9RMhpEMCnfbTazeLvI2c-l0JAIyzewcdULRuBPqbtiNNssi44lRnaGQFSf5-qknWyHGDRMzlvSdSMRO66RCgqnvrz5P3Udpc_3DRHftlvK5AzUrJPbxhyrNB0D_lDBdRIQjL-4c7e0Y5mgbCgyq49lqYD7yzlsm3OpDWbixitV2K9rFajtArgCnMC6X11_kimu13zUAYt8CTxblGoq4VaT4j2Dxaw',
                    'ativo'        => 1,
                    'destaque'     => 1,
                    'created_at'   => $now,
                ],
                [
                    'categoria_id' => $catMap['tradicionais'] ?? 1,
                    'nome'         => 'Calabresa Especial Defumada',
                    'slug'         => 'calabresa-especial-defumada',
                    'descricao'    => 'Fatias finas de calabresa artesanal defumada em lenha de macieira, cebola roxa caramelizada, azeitonas pretas e orégano.',
                    'tipo'         => 'pizza',
                    'preco_base'   => 62.00,
                    'imagem'       => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDdQKiHVKA8bHJSEgMK6yPueAveqEqbRCnVpROBKcvjkt8Mr1k00I0-3vxGtAMcber1iP7qTeo8Tuqm14eO5MS7B4103DiGwNTOGqZf5RjX5CJOrXgq2X04o5NnuDcLZKs2SaBDIk9qmHB_SV_4qG8o3N2lwZQYq_vH-jg0Xnt55CwubjeqXxszAVO-u_6Tnw0-vxzLcQ1ImV6K2k3jjVZMYWtpK8NUfjRBkygljEI',
                    'ativo'        => 1,
                    'destaque'     => 0,
                    'created_at'   => $now,
                ],
                [
                    'categoria_id' => $catMap['especiais'] ?? 2,
                    'nome'         => 'Tartufo & Cogumelos',
                    'slug'         => 'tartufo-e-cogumelos',
                    'descricao'    => 'Mix de shimeji e shiitake salteados na manteiga de trufa branca, base fior di latte e lascas de Parmigiano 18 meses.',
                    'tipo'         => 'pizza',
                    'preco_base'   => 78.00,
                    'imagem'       => 'https://lh3.googleusercontent.com/aida-public/AB6AXuA6F_W-tUEi3GPe0EjWKyDRzfvrl274L1q5reBx9-2iKXF7MTaS_b0t3d7djxvmbb1zSyfCHNExNrT9jBNfpXa1ZqY004nARYoMcj92AqODoetSq8gPc_2EBxdm6V1O_0zouHxCEYh4GzVuTOPZ78i23ntxwb75m4nH0eASvn_tfGeyzXkD4HOnlzigOS-5uRb4yvo5t_8FR90UsLvoNRXxQ8RyI886mkyh04p3dNo',
                    'ativo'        => 1,
                    'destaque'     => 1,
                    'created_at'   => $now,
                ],
                [
                    'categoria_id' => $catMap['tradicionais'] ?? 1,
                    'nome'         => 'Quattro Formaggi D.O.P',
                    'slug'         => 'quattro-formaggi-dop',
                    'descricao'    => 'Harmonia de Gorgonzola dolce, provolone curado, mozzarella especial e legítimo Catupiry cremoso.',
                    'tipo'         => 'pizza',
                    'preco_base'   => 72.00,
                    'imagem'       => 'https://lh3.googleusercontent.com/aida-public/AB6AXuA_NlAxsm_ZLpLJ98d6C2QjNQcx5KaGVyTOB2xNtnDApjBswE3sPjzDdXwBQqRtnSiIBggyAryFyN7euq6kyaJFJEqIDK13t_xj0x_MLMCQyduG-9JBQ6eUX3jqfJoM1n-v3lCAc_kPOY_7jGo0nlyl23IZNMmWbyybm8rW_WuyN-A63IkP1OANT4_dUJ4JbT0hthYMVrLdnv-UJu7NbzEtNaBymq5rM888sX8AUcE',
                    'ativo'        => 1,
                    'destaque'     => 0,
                    'created_at'   => $now,
                ],
                [
                    'categoria_id' => $catMap['especiais'] ?? 2,
                    'nome'         => 'Parma & Rúcula Selvagem',
                    'slug'         => 'parma-e-rucula-selvagem',
                    'descricao'    => 'Presunto cru de Parma curado 24 meses, rúcula baby fresca, tomatinhos confitados e redução balsâmica de Modena.',
                    'tipo'         => 'pizza',
                    'preco_base'   => 79.00,
                    'imagem'       => 'https://lh3.googleusercontent.com/aida-public/AB6AXuA_OePZzBjMz8NNsy99h5CEW3GiBT7PlFedRzPDoJnti7vGLIa3UgDiDFnl7CifSZv8tBfiH6V7BeqnDxib7zSpIa5OucI92VPQ_qUJm4Ea0GHb3kGd7lvU5FaNaV8VXx7FLeW0VukdQJ0jAxo6-9cu7GQBXyzGeV5GgsnwE1ToIR0sWmtJTcecLtKiibJLHnJzpGVSx0RvEUpvmAwhjOzOubkz9TAFxpwmmhoTpuU',
                    'ativo'        => 1,
                    'destaque'     => 1,
                    'created_at'   => $now,
                ],
                [
                    'categoria_id' => $catMap['doces'] ?? 3,
                    'nome'         => 'Nutella & Morangos Frescos',
                    'slug'         => 'nutella-e-morangos-frescos',
                    'descricao'    => 'Generosa camada de Nutella original, morangos frescos fatiados e crocante de castanhas tostadas.',
                    'tipo'         => 'pizza',
                    'preco_base'   => 56.00,
                    'imagem'       => 'https://lh3.googleusercontent.com/aida-public/AB6AXuC-d6GP1bw1uV0BFIKsM9JzcnvO-aKalHJZf9Aa9ziliidhp6mPPFV1qrZQEPFVTURKbC7JOr5Hk43djMcmtgj-JGmi6Pmodxl7KsEBnFzw_JR6QhI59qOzNlDKeG1DwuTQ91k1n4BugNTRceLZm3-GwpceFnkotbVF-_EQGoffbDLdRvqAfNAGgvlrIyG0xQaFl5gjrrcmPf_Rcq23cBYEeg4dPMGPR27rdqeA3_0',
                    'ativo'        => 1,
                    'destaque'     => 0,
                    'created_at'   => $now,
                ],
                [
                    'categoria_id' => $catMap['bebidas'] ?? 4,
                    'nome'         => 'Coca-Cola Original 2L',
                    'slug'         => 'coca-cola-original-2l',
                    'descricao'    => 'Garrafa de 2 litros perfeitamente refrigerada para acompanhar sua pizza.',
                    'tipo'         => 'simples',
                    'preco_base'   => 15.00,
                    'imagem'       => 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?w=500&auto=format&fit=crop&q=80',
                    'ativo'        => 1,
                    'destaque'     => 0,
                    'created_at'   => $now,
                ],
            ];
            $this->db->table('produtos')->insertBatch($produtos);
        }

        // 6. Cliente de Teste Inicial
        if ($this->db->table('clientes')->where('email', 'cliente@fornodoro.com')->countAllResults() === 0) {
            $this->db->table('clientes')->insert([
                'nome'       => 'Cliente Exemplo',
                'email'      => 'cliente@fornodoro.com',
                'telefone'   => '(11) 98765-4321',
                'senha'      => password_hash('123456', PASSWORD_DEFAULT),
                'cep'        => '01310-100',
                'endereco'   => 'Avenida Paulista',
                'numero'     => '1000',
                'bairro'     => 'Bela Vista',
                'cidade'     => 'São Paulo',
                'ativo'      => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
