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


public function index()
{
    $model = new \App\Models\PropostaModel();

    // ======================
    // PAGINAÇÃO
    // ======================
    $page    = (int) ($this->request->getGet('page') ?? 1);
    $perPage = (int) ($this->request->getGet('per_page') ?? 10);
    $perPage = min($perPage, 100); // limite anti-abuso

    // ======================
    // FILTRO POR STATUS
    // ======================
    $status = $this->request->getGet('status');

    if ($status) {
        $model->where('status', strtoupper($status));
    }

    // ======================
    // FILTRO POR PERÍODO
    // ======================
    $dateFrom = $this->request->getGet('date_from');
    $dateTo   = $this->request->getGet('date_to');

    if ($dateFrom) {
        $model->where('created_at >=', $dateFrom . ' 00:00:00');
    }

    if ($dateTo) {
        $model->where('created_at <=', $dateTo . ' 23:59:59');
    }

    // ======================
    // ORDENAÇÃO SEGURA
    // ======================
    $allowedSortFields = ['id', 'created_at', 'valor_mensal', 'status'];
    $sort  = $this->request->getGet('sort') ?? 'created_at';
    $order = strtolower($this->request->getGet('order') ?? 'desc');

    if (!in_array($sort, $allowedSortFields)) {
        $sort = 'created_at';
    }

    if (!in_array($order, ['asc', 'desc'])) {
        $order = 'desc';
    }

    $model->orderBy($sort, $order);

    // ======================
    // EXECUÇÃO
    // ======================
$data = $model->paginate($perPage, 'default', $page);

$totalPages = $model->pager->getPageCount();
$currentPage = $model->pager->getCurrentPage();

return $this->respond([
    'data' => $data,
    'meta' => [
        'currentPage' => $currentPage,
        'perPage'     => $perPage,
        'total'       => $model->pager->getTotal(),
        'totalPages'  => $totalPages,
        'hasNextPage' => $currentPage < $totalPages,
        'hasPrevPage' => $currentPage > 1,
    ]
]);
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

    public function auditoria($id)
{
    $auditoriaModel = new \App\Models\PropostaAuditoriaModel();

    $logs = $auditoriaModel
        ->where('proposta_id', $id)
        ->orderBy('created_at', 'ASC')
        ->findAll();

    if (!$logs) {
        return $this->failNotFound('Nenhum histórico encontrado.');
    }

    return $this->respond($logs);
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