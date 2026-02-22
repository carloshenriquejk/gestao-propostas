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

    $db = \Config\Database::connect('default');

    // DESABILITA temporariamente as FKs
    $db->query('SET FOREIGN_KEY_CHECKS=0');

    // Limpa as tabelas na ordem correta
    $db->table('propostas')->truncate(); // dependentes primeiro
    $db->table('clientes')->truncate();  // depois os clientes

    // HABILITA FKs novamente
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
        // Supondo que proposta id=1 exista
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