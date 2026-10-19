<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'brand',
        'collection_name',
        'material_type',
        'thickness',
        'dimensions',
        'surface_finish',
        'suitable_for',
        'warranty_years',
        'installation_type',
        'unit_of_measure',
        'coverage_per_box',
        'primary_image',
        'gallery_images',
        'description',
        'specifications',
        'base_price',
        'is_featured',
        'status',
        'is_public',
        'seo_title',
        'seo_description',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'specifications' => 'array',
        'base_price' => 'decimal:2',
        'coverage_per_box' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_public' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function shades()
    {
        return $this->hasMany(ProductShade::class);
    }

    public function activeShades()
    {
        return $this->hasMany(ProductShade::class)->where('is_active', true);
    }

    public function publicShades()
    {
        return $this->hasMany(ProductShade::class)->where('is_active', true)->where('is_public', true);
    }

    public function enquiries()
    {
        return $this->hasMany(Enquiry::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePublicCatalog($query)
    {
        return $query->where('status', 'published')->where('is_public', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->where('status', 'published')->where('is_public', true);
    }

    public function getTotalStockAttribute(): float
    {
        return (float) $this->shades->sum('stock_quantity');
    }

    public function getMinPriceAttribute(): float
    {
        $min = $this->shades->where('selling_price', '>', 0)->min('selling_price');
        return (float) ($min ?: $this->base_price);
    }

    public function getMaxPriceAttribute(): float
    {
        $max = $this->shades->where('selling_price', '>', 0)->max('selling_price');
        return (float) ($max ?: $this->base_price);
    }

    public function hasLowStock(): bool
    {
        return $this->shades->contains(function ($shade) {
            return $shade->stock_quantity <= $shade->reorder_threshold;
        });
    }
}
