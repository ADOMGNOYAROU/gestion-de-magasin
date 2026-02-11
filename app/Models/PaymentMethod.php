<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relations
    public function ventes()
    {
        return $this->hasMany(Vente::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper methods
    public function isCash()
    {
        return $this->code === 'cash';
    }

    public function isCard()
    {
        return $this->code === 'card';
    }

    public function isCheck()
    {
        return $this->code === 'check';
    }

    public function isMobile()
    {
        return $this->code === 'mobile';
    }
}
