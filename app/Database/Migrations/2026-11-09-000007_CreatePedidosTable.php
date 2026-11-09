<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePedidosTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'codigo' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'unique'     => true,
            ],
            'cliente_nome' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'cliente_telefone' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'telegram_chat_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'tipo_entrega' => [
                'type'       => 'ENUM',
                'constraint' => ['delivery', 'retirada', 'mesa'],
                'default'    => 'delivery',
            ],
            'endereco' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'forma_pagamento' => [
                'type'       => 'ENUM',
                'constraint' => ['pix', 'cartao_credito', 'cartao_debito', 'dinheiro'],
                'default'    => 'pix',
            ],
            'troco_para' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pendente', 'confirmado', 'em_preparo', 'saiu_entrega', 'entregue', 'cancelado'],
                'default'    => 'pendente',
            ],
            'status_pagamento' => [
                'type'       => 'ENUM',
                'constraint' => ['aguardando', 'pago', 'rejeitado', 'na_entrega'],
                'default'    => 'aguardando',
            ],
            'total_produtos' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'taxa_entrega' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'total_geral' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'observacoes' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('telegram_chat_id');
        $this->forge->createTable('pedidos');
    }

    public function down()
    {
        $this->forge->dropTable('pedidos', true);
    }
}
