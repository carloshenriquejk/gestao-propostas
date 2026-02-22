<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePropostaAuditoria extends Migration
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
            'proposta_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'actor' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'evento' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'payload' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('proposta_id');
        $this->forge->addKey('created_at');

        $this->forge->addForeignKey(
            'proposta_id',
            'propostas',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('proposta_auditoria', true);
    }

    public function down()
    {
        $this->forge->dropTable('proposta_auditoria', true);
    }
}