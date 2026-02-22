<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\ResponseInterface;

class DocsController extends ResourceController
{
    protected $format = 'json';

    /**
     * Página inicial do Swagger UI
     */
    public function index()
    {
        // Serve o HTML do Swagger UI
        return view('swagger');
    }

    /**
     * Retorna o OpenAPI YAML gerado dinamicamente
     */
public function yaml()
{
    return $this->response
                ->setContentType('text/yaml')
                ->setBody(file_get_contents(FCPATH . 'docs/openapi.yaml'));
}
}