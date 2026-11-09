<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Tamanho extends Entity
{
    protected $dates = ['created_at', 'updated_at'];
    protected $casts = [
        'id'          => 'integer',
        'fatias'      => 'integer',
        'max_sabores' => 'integer',
        'ordem'       => 'integer',
        'ativo'       => 'boolean',
    ];
}
