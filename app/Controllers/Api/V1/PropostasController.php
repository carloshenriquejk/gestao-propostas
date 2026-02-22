<?php
namespace App\Controllers\Api\V1;

use App\Services\PropostaService;
use App\Services\PropostaStatusService;
use App\Exceptions\VersionConflictException;
use CodeIgniter\RESTful\ResourceController;
use DomainException;
use RuntimeException;

class PropostasController extends ResourceController
{
    protected $format = 'json';

    protected PropostaService $propostaService;
    protected PropostaStatusService $statusService;

    public function __construct()
    {
        $this->propostaService = new PropostaService();
        $this->statusService = new PropostaStatusService();
    }

    /**
     * POST /propostas
     * Cria proposta (idempotente)
     */
public function create()
{
    try {
        $data = $this->request->getJSON(true);

        $idempotencyKey = $this->request->getHeaderLine('Idempotency-Key');
        $actor = $this->request->getHeaderLine('X-Actor') ?: 'system';

        if (!$idempotencyKey) {
            return $this->failValidationErrors('Header Idempotency-Key é obrigatório.');
        }

        $result = $this->propostaService->create(
            data: $data,
            idempotencyKey: $idempotencyKey,
            actor: $actor
        );

        return $this->respondCreated($result);

    } catch (\DomainException $e) {
        return $this->failValidationErrors($e->getMessage());

    } catch (\Throwable $e) {
        return $this->failServerError($e->getMessage());
    }
}

    /**
     * PUT /propostas/{id}
     * Atualiza dados (com optimistic lock)
     */
    public function update($id = null)
    {
        try {
            $data = $this->request->getJSON(true);

            if (!isset($data['versao'])) {
                return $this->failValidationErrors('Versão é obrigatória para atualização.');
            }

            $result = $this->propostaService->update(
                propostaId: (int) $id,
                data: $data,
                versao: (int) $data['versao']
            );

            return $this->respond($result);

        } catch (VersionConflictException $e) {
            return $this->fail($e->getMessage(), 409);

        } catch (DomainException $e) {
            return $this->failValidationErrors($e->getMessage());

        } catch (RuntimeException $e) {
            return $this->failNotFound($e->getMessage());

        } catch (\Throwable $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * POST /propostas/{id}/submit
     * Move para SUBMITTED
     */
    public function submit($id = null)
    {
        try {
            $actor = $this->request->getHeaderLine('X-Actor') ?? 'system';

            $result = $this->statusService->changeStatus(
                propostaId: (int) $id,
                novoStatus: 'SUBMITTED',
                actor: $actor
            );

            return $this->respond($result);

        } catch (DomainException $e) {
            return $this->failValidationErrors($e->getMessage());

        } catch (RuntimeException $e) {
            return $this->failNotFound($e->getMessage());

        } catch (\Throwable $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * POST /propostas/{id}/approve
     */
    public function approve($id = null)
    {
        return $this->changeStatus($id, 'APPROVED');
    }

    /**
     * POST /propostas/{id}/reject
     */
    public function reject($id = null)
    {
        return $this->changeStatus($id, 'REJECTED');
    }

    /**
     * POST /propostas/{id}/cancel
     */
    public function cancel($id = null)
    {
        return $this->changeStatus($id, 'CANCELLED');
    }

    /**
     * Método interno reutilizável
     */
    private function changeStatus($id, string $novoStatus)
    {
        try {
            $actor = $this->request->getHeaderLine('X-Actor') ?? 'system';

            $result = $this->statusService->changeStatus(
                propostaId: (int) $id,
                novoStatus: $novoStatus,
                actor: $actor
            );

            return $this->respond($result);

        } catch (DomainException $e) {
            return $this->failValidationErrors($e->getMessage());

        } catch (RuntimeException $e) {
            return $this->failNotFound($e->getMessage());

        } catch (\Throwable $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * GET /propostas/{id}
     */
    public function show($id = null)
    {
        $model = new \App\Models\PropostaModel();
        $proposta = $model->find($id);

        if (!$proposta) {
            return $this->failNotFound('Proposta não encontrada.');
        }

        return $this->respond($proposta);
    }
}