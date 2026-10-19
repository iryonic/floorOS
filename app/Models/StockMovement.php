<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'product_shade_id',
        'sku',
        'type',
        'direction',
        'quantity',
        'unit',
        'unit_cost',
        'previous_stock',
        'new_stock',
        'related_type',
        'related_id',
        'reason',
        'notes',
        'user_id',
        'occurred_at',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'previous_stock' => 'decimal:2',
        'new_stock' => 'decimal:2',
        'occurred_at' => 'datetime',
    ];

    public function shade()
    {
        return $this->belongsTo(ProductShade::class, 'product_shade_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function related()
    {
        return $this->morphTo();
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'opening' => 'Opening Stock',
            'purchase' => 'Purchase Receipt',
            'sale' => 'Sale Deduction',
            'customer_return' => 'Customer Return',
            'supplier_return' => 'Supplier Return',
            'damage' => 'Damaged / Wastage',
            'adjustment' => 'Manual Adjustment',
            'audit' => 'Physical Count / Audit',
            default => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }
}
