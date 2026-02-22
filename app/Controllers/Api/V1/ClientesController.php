<?php
namespace App\Controllers\Api\V1;

use App\Models\ClienteModel;
use CodeIgniter\RESTful\ResourceController;

class ClientesController extends ResourceController
{
    protected $format = 'json';
    protected $model;

    public function __construct()
    {
        $this->model = new ClienteModel();
    }

public function index() 
{
    $page = (int) $this->request->getGet('page') ?? 1;
    $perPage = 10;

    $clientes = $this->model->paginate($perPage, 'default', $page);

    $pager = $this->model->pager;

    return $this->respond([
        'data' => $clientes,
        'currentPage' => $pager->getCurrentPage(),
        'perPage' => $pager->getPerPage(),
        'total' => $pager->getTotal(),
        'lastPage' => $pager->getPageCount(),
    ]);
}

    public function show($id = null) // GET /api/v1/clientes/{id}
    {
        $cliente = $this->model->find($id);
        if (!$cliente) return $this->failNotFound('Cliente não encontrado.');
        return $this->respond($cliente);
    }

    public function create() // POST /api/v1/clientes
    {
        $data = $this->request->getJSON(true);
        if (!$this->model->insert($data)) return $this->failValidationErrors($this->model->errors());
        $id = $this->model->getInsertID();
        return $this->respondCreated($this->model->find($id));
    }

    public function update($id = null) // PATCH /api/v1/clientes/{id}
    {
        $data = $this->request->getJSON(true);
        if (!$this->model->find($id)) return $this->failNotFound('Cliente não encontrado.');
        if (!$this->model->update($id, $data)) return $this->failValidationErrors($this->model->errors());
        return $this->respond($this->model->find($id));
    }
}