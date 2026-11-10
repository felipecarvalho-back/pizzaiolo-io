<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Cliente extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [
        'id'    => 'integer',
        'ativo' => 'boolean',
    ];

    public function setSenha(string $senha): self
    {
        $this->attributes['senha'] = password_hash($senha, PASSWORD_DEFAULT);
        return $this;
    }

    public function verificarSenha(string $senhaPura): bool
    {
        return password_verify($senhaPura, $this->attributes['senha'] ?? '');
    }

    public function getEnderecoCompleto(): string
    {
        if (empty($this->attributes['endereco'])) {
            return '';
        }

        $partes = [$this->attributes['endereco']];
        if (! empty($this->attributes['numero'])) {
            $partes[] = 'nº ' . $this->attributes['numero'];
        }
        if (! empty($this->attributes['bairro'])) {
            $partes[] = $this->attributes['bairro'];
        }
        if (! empty($this->attributes['complemento'])) {
            $partes[] = '(' . $this->attributes['complemento'] . ')';
        }

        return implode(', ', $partes);
    }
}
