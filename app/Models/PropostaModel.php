<?php

namespace App\Models;

use CodeIgniter\Model;

class PropostaModel extends Model
{
    protected $table            = 'propostas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    

    protected $allowedFields = [
        'cliente_id',
        'produto',
        'valor_mensal',
        'status',
        'origem',
        'versao',
        'idempotency_key'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'cliente_id' => 'required|integer',
        'produto' => 'required|min_length[3]',
        'valor_mensal' => 'required|decimal',
        'status' => 'required',
        'origem' => 'required|in_list[APP,SITE,API]',
        'versao' => 'permit_empty|integer'
    ];
}