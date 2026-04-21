<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_purchase',
        'usage_limit',
        'usage_count',
        'valid_from',
        'valid_until',
        'active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'active' => 'boolean',
    ];

    public function isValid($cartTotal = 0)
    {
        // Verificar se está ativo
        if (!$this->active) {
            return false;
        }

        // Verificar data de validade
        $now = now();
        if ($this->valid_from && $now->lt($this->valid_from)) {
            return false;
        }
        if ($this->valid_until && $now->gt($this->valid_until)) {
            return false;
        }

        // Verificar limite de uso
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        // Verificar valor mínimo de compra
        if ($this->min_purchase && $cartTotal < $this->min_purchase) {
            return false;
        }

        return true;
    }

    public function calculateDiscount($cartTotal)
    {
        if (!$this->isValid($cartTotal)) {
            return 0;
        }

        if ($this->type === 'percentage') {
            return ($cartTotal * $this->value) / 100;
        }

        return min($this->value, $cartTotal);
    }
}
