<?php

namespace App\Models;

use App\Entities\Usuario;
use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table            = 'usuarios';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Usuario::class;
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nome',
        'email',
        'senha',
        'cargo',
        'ativo',
        'ultimo_acesso',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function buscarPorEmail(string $email): ?Usuario
    {
        return $this->where('email', $email)
                    ->where('ativo', 1)
                    ->first();
    }
}
