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
        return $this->respond($this->model->findAll());
    }

    public function show($id = null)
    {
        $cliente = $this->model->find($id);

        if (!$cliente) {
            return $this->failNotFound('Cliente não encontrado.');
        }

        return $this->respond($cliente);
    }

    public function create()
    {
        $data = $this->request->getJSON(true);

        if (!$this->model->insert($data)) {
            return $this->failValidationErrors($this->model->errors());
        }

        $id = $this->model->getInsertID();

        return $this->respondCreated(
            $this->model->find($id)
        );
    }

    public function update($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Cliente não encontrado.');
        }

        $data = $this->request->getJSON(true);

        if (!$this->model->update($id, $data)) {
            return $this->failValidationErrors($this->model->errors());
        }

        return $this->respond($this->model->find($id));
    }

    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Cliente não encontrado.');
        }

        $this->model->delete($id);

        return $this->respondDeleted([
            'message' => 'Cliente removido com sucesso.'
        ]);
    }
}