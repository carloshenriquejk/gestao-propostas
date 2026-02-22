<?php

namespace App\Models;

use CodeIgniter\Model;

class PropostaAuditoriaModel extends Model
{
    protected $table            = 'proposta_auditoria';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'proposta_id',
        'actor',
        'evento',
        'payload'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'proposta_id' => 'required|integer',
        'actor' => 'required',
        'evento' => 'required',
    ];
}