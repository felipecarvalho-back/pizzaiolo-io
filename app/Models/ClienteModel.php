<?php

namespace App\Models;

use App\Entities\Cliente;
use CodeIgniter\Model;

class ClienteModel extends Model
{
    protected $table            = 'clientes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Cliente::class;
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nome',
        'email',
        'telefone',
        'senha',
        'cep',
        'endereco',
        'numero',
        'bairro',
        'complemento',
        'cidade',
        'ativo',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function buscarPorEmail(string $email): ?Cliente
    {
        return $this->where('email', $email)
                    ->where('ativo', 1)
                    ->first();
    }
}
