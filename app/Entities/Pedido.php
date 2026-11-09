<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Pedido extends Entity
{
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts = [
        'id'             => 'integer',
        'troco_para'     => '?float',
        'total_produtos' => 'float',
        'taxa_entrega'   => 'float',
        'total_geral'    => 'float',
    ];

    /**
     * Retorna o link de Deep Link do Telegram para o cliente acompanhar
     */
    public function getTelegramLink(string $botUsername): string
    {
        $codigo = $this->attributes['codigo'] ?? '';
        return "https://t.me/{$botUsername}?start={$codigo}";
    }

    public function isPago(): bool
    {
        return ($this->attributes['status_pagamento'] ?? '') === 'pago';
    }

    public function getTotalFormatado(): string
    {
        return 'R$ ' . number_format((float) ($this->attributes['total_geral'] ?? 0), 2, ',', '.');
    }
}
