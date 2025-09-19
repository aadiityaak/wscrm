<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'name',
        'amount',
        'currency',
        'provider',
        'category',
        'next_billing',
        'paid_date',
        'status',
        'type',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'next_billing' => 'date',
            'paid_date' => 'date',
        ];
    }

    public function scopeMonthly($query)
    {
        return $query->where('type', 'monthly');
    }

    public function scopeYearly($query)
    {
        return $query->where('type', 'yearly');
    }

    public function scopeOneTime($query)
    {
        return $query->where('type', 'one-time');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
