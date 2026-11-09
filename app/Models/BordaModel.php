<?php

namespace App\Models;

use App\Entities\Borda;
use CodeIgniter\Model;

class BordaModel extends Model
{
    protected $table            = 'bordas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Borda::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nome',
        'preco',
        'ativo',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'nome'  => 'required|min_length[2]|max_length[100]',
        'preco' => 'required|decimal',
    ];
}
