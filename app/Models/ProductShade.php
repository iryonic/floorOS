<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductShade extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'shade_name',
        'shade_code',
        'sku',
        'color_family',
        'finish',
        'image',
        'swatch_image',
        'selling_price',
        'cost_price',
        'stock_quantity',
        'unit',
        'reorder_threshold',
        'is_active',
        'is_public',
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock_quantity' => 'decimal:2',
        'reorder_threshold' => 'decimal:2',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
    ];

    public function getNameAttribute(): string
    {
        return $this->shade_name ?? '';
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->reorder_threshold && $this->stock_quantity > 0;
    }

    public function isOutOfStock(): bool
    {
        return $this->stock_quantity <= 0;
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stock_quantity <= 0) {
            return 'Out of Stock';
        }
        if ($this->stock_quantity <= $this->reorder_threshold) {
            return 'Low Stock';
        }
        return 'In Stock';
    }

    public function getEffectiveImageAttribute(): ?string
    {
        return $this->image ?: $this->product->primary_image;
    }
}
