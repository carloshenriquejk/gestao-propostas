<?php

namespace Tests\Api\V1;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

class ClientesControllerTest extends CIUnitTestCase
{
    use FeatureTestTrait;
protected function setUp(): void
{
    parent::setUp();

    $db = \Config\Database::connect('default');

    // DESABILITA temporariamente as FKs
    $db->query('SET FOREIGN_KEY_CHECKS=0');

    // Limpa as tabelas na ordem correta
    $db->table('propostas')->truncate(); // dependentes primeiro
    $db->table('clientes')->truncate();  // depois os clientes

    // HABILITA FKs novamente
    $db->query('SET FOREIGN_KEY_CHECKS=1');
}

    public function testIndexClientes()
    {
        $result = $this->get('/api/v1/clientes');
        $result->assertStatus(200);
    }

    public function testCreateCliente()
    {
        $payload = [
            'nome' => 'Teste Cliente',
            'email' => 'teste@cliente.com',
            'documento' => '12345678900'
        ];

        $result = $this->call('post', '/api/v1/clientes', $payload);
        $result->assertStatus(201);
        $result->assertJSONFragment(['nome' => 'Teste Cliente']);
    }

    public function testShowCliente()
    {
        // Supondo que cliente id=1 exista
        $result = $this->call('get', '/api/v1/clientes/1');
        $result->assertStatus(200);
        $result->assertJSONFragment(['id' => 1]);
    }
}