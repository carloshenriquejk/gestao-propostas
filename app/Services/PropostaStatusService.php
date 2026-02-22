<?php

namespace App\Services;

use App\Models\PropostaModel;
use App\Enums\PropostaStatus;
use App\Services\AuditoriaService;
use CodeIgniter\Database\Exceptions\DatabaseException;

class PropostaStatusService
{
    protected PropostaModel $propostaModel;
    protected AuditoriaService $auditoriaService;

    public function __construct()
    {
        $this->propostaModel = new PropostaModel();
        $this->auditoriaService = new AuditoriaService();
    }

    /**
     * Altera o status da proposta com controle de versão
     */
    public function changeStatus(int $propostaId, string $novoStatus, string $actor): array
    {
        $db = db_connect();
        $db->transStart();

        $proposta = $this->propostaModel->find($propostaId);

        if (!$proposta) {
            throw new \RuntimeException('Proposta não encontrada.');
        }

        $statusAtual = PropostaStatus::from($proposta['status']);
        $statusDestino = PropostaStatus::from($novoStatus);

        // Impede alteração após estado final
        if ($statusAtual->isFinal()) {
            throw new \DomainException('Proposta está em estado final e não pode ser alterada.');
        }

        // Valida transição
        if (!$statusAtual->canTransitionTo($statusDestino)) {
            throw new \DomainException('Transição de status inválida.');
        }

        $versaoAtual = $proposta['versao'];

        // Optimistic Lock
        $builder = $this->propostaModel->builder();
        $builder->where('id', $propostaId);
        $builder->where('versao', $versaoAtual);

        $updated = $builder->update([
            'status' => $statusDestino->value,
            'versao' => $versaoAtual + 1,
        ]);

        if (!$updated || $db->affectedRows() === 0) {
            throw new \RuntimeException('Conflito de versão. Registro alterado por outro processo.');
        }

        // Auditoria
        $this->auditoriaService->registrar(
            propostaId: $propostaId,
            actor: $actor,
            evento: 'STATUS_CHANGED',
            payload: [
                'before' => $statusAtual->value,
                'after' => $statusDestino->value,
                'versao_anterior' => $versaoAtual,
                'versao_nova' => $versaoAtual + 1,
            ]
        );

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new DatabaseException('Erro ao alterar status.');
        }

        return $this->propostaModel->find($propostaId);
    }
}