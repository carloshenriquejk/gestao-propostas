<?php

namespace App\Exceptions;

class VersionConflictException extends \RuntimeException
{
    protected $message = 'Conflito de versão. Registro alterado por outro processo.';
}