<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DomainPrice extends Model
{
    protected $fillable = [
        'extension',
        'base_cost',
        'renewal_cost',
        'selling_price',
        'renewal_price_with_tax',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'base_cost' => 'decimal:2',
            'renewal_cost' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'renewal_price_with_tax' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'item_id')->where('item_type', 'domain');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByExtension($query, string $extension)
    {
        return $query->where('extension', $extension);
    }
}
