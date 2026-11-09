<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePedidoItemAdicionaisTable extends Migration
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
            'pedido_item_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'adicional_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'preco' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('pedido_item_id', 'pedido_itens', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('adicional_id', 'adicionais', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pedido_item_adicionais');
    }

    public function down()
    {
        $this->forge->dropTable('pedido_item_adicionais', true);
    }
}
