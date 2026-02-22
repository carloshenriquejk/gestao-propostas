<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePropostas extends Migration
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
            'cliente_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'produto' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'valor_mensal' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'DRAFT',
            ],
            'origem' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'versao' => [
                'type'    => 'INT',
                'default' => 1,
            ],
            'idempotency_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            'deleted_at DATETIME NULL',
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('cliente_id');
        $this->forge->addKey('status');
        $this->forge->addKey('created_at');
        $this->forge->addUniqueKey('idempotency_key');

        $this->forge->addForeignKey(
            'cliente_id',
            'clientes',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('propostas', true);
    }

    public function down()
    {
        $this->forge->dropTable('propostas', true);
    }
}