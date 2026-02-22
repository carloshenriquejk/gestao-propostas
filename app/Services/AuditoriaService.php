<?php

namespace App\Services;

use App\Models\PropostaAuditoriaModel;

class AuditoriaService
{
    protected PropostaAuditoriaModel $auditoriaModel;

    public function __construct()
    {
        $this->auditoriaModel = new PropostaAuditoriaModel();
    }

    public function registrar(int $propostaId, string $actor, string $evento, array $payload = []): void
    {
        $this->auditoriaModel->insert([
            'proposta_id' => $propostaId,
            'actor' => $actor,
            'evento' => $evento,
            'payload' => json_encode($payload),
        ]);
    }
}