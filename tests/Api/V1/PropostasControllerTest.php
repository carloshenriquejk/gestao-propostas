<?php

namespace Tests\Api\V1;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

class PropostasControllerTest extends CIUnitTestCase
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

    public function testIndexPropostas()
    {
        $result = $this->call('get', '/api/v1/propostas');
        $result->assertStatus(200);
        $result->assertJSONFragment(['data']);
    }

    public function testCreateProposta()
    {
        $payload = [
            'cliente_id' => 1,
            'valor_mensal' => 100,
        ];

        $headers = [
            'X-Actor' => 'tester',
            'Idempotency-Key' => uniqid()
        ];

        $result = $this->call('post', '/api/v1/propostas', $payload, [], $headers);
        $result->assertStatus(201);
        $result->assertJSONFragment(['cliente_id' => 1]);
    }

    public function testSubmitProposta()
    {
        $headers = ['X-Actor' => 'tester'];
        $result = $this->call('post', '/api/v1/propostas/1/submit', [], [], $headers);
        $result->assertStatus(200);
        $result->assertJSONFragment(['status' => 'SUBMITTED']);
    }

    public function testApproveProposta()
    {
        $headers = ['X-Actor' => 'tester'];
        $result = $this->call('post', '/api/v1/propostas/1/approve', [], [], $headers);
        $result->assertStatus(200);
        $result->assertJSONFragment(['status' => 'APPROVED']);
    }

    public function testAuditoriaProposta()
    {
        $result = $this->call('get', '/api/v1/propostas/1/auditoria');
        $result->assertStatus(200);
        $result->assertJSONFragment(['evento']);
    }
}