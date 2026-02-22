<?php

namespace App\Services;

use App\Models\PropostaModel;
use App\Services\AuditoriaService;
use App\Enums\PropostaStatus;
use App\Exceptions\VersionConflictException;

class PropostaService
{
    protected PropostaModel $model;
    protected AuditoriaService $auditoria;

    public function __construct()
    {
        $this->model = new PropostaModel();
        $this->auditoria = new AuditoriaService();
    }

    /**
     * Criação com Idempotency-Key
     */
public function create(array $data, string $idempotencyKey, string $actor): array
{
    if ($idempotencyKey) {
        $existente = $this->model
            ->where('idempotency_key', $idempotencyKey)
            ->first();

        if ($existente) {
            return $existente;
        }
    }

    $data['status'] = PropostaStatus::DRAFT->value;
    $data['versao'] = 1;
    $data['idempotency_key'] = $idempotencyKey;

    $this->model->insert($data);

    $id = $this->model->getInsertID();

    if (!$id) {
        throw new \RuntimeException('Falha ao inserir proposta.');
    }

    $this->auditoria->registrar(
        propostaId: $id,
        actor: $actor,
        evento: 'CREATED',
        payload: $data
    );

    return $this->model->find($id);
}

    /**
     * Atualização com optimistic lock
     */
    public function atualizar(int $id, array $dados, int $versao, string $actor): array
    {
        $proposta = $this->model->find($id);

        if (!$proposta) {
            throw new \RuntimeException('Proposta não encontrada.');
        }

        if ($proposta['versao'] != $versao) {
            throw new VersionConflictException();
        }

        $builder = $this->model->builder();
        $builder->where('id', $id);
        $builder->where('versao', $versao);

        $builder->update([
            ...$dados,
            'versao' => $versao + 1
        ]);

        if ($builder->db()->affectedRows() === 0) {
            throw new VersionConflictException();
        }

        // Auditoria de campos sensíveis
        $this->auditoria->registrar(
            propostaId: $id,
            actor: $actor,
            evento: 'UPDATED_FIELDS',
            payload: [
                'before' => $proposta,
                'after' => $dados
            ]
        );

        return $this->model->find($id);
    }
}