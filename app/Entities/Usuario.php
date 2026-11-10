<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Usuario extends Entity
{
    protected $datamap = [];
    protected $dates   = ['ultimo_acesso', 'created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [
        'id'    => 'integer',
        'ativo' => 'boolean',
    ];

    /**
     * Faz o hash automático caso seja atribuída uma senha em texto puro
     */
    public function setSenha(string $senha): self
    {
        $this->attributes['senha'] = password_hash($senha, PASSWORD_DEFAULT);
        return $this;
    }

    /**
     * Verifica se a senha informada confere com o hash salvo
     */
    public function verificarSenha(string $senhaPura): bool
    {
        return password_verify($senhaPura, $this->attributes['senha'] ?? '');
    }
}
