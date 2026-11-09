<?php

namespace App\Models;

use App\Entities\Tamanho;
use CodeIgniter\Model;

class TamanhoModel extends Model
{
    protected $table            = 'tamanhos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Tamanho::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nome',
        'sigla',
        'fatias',
        'max_sabores',
        'ordem',
        'ativo',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'nome'        => 'required|min_length[2]|max_length[60]',
        'fatias'      => 'required|integer',
        'max_sabores' => 'required|integer',
    ];
}
