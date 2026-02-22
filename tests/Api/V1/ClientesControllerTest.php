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

    $db = \Config\Database::connect('tests');

    $db->query('SET FOREIGN_KEY_CHECKS=0');

    $db->table('propostas')->truncate(); 
    $db->table('clientes')->truncate();  
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
        $result = $this->call('get', '/api/v1/clientes/1');
        $result->assertStatus(200);
        $result->assertJSONFragment(['id' => 1]);
    }
}