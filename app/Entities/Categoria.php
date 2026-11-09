<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Categoria extends Entity
{
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts = [
        'id'    => 'integer',
        'ordem' => 'integer',
        'ativo' => 'boolean',
    ];
}
