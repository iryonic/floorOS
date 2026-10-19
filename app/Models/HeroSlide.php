<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    use HasFactory;

    protected $fillable = [
        'desktop_image',
        'tablet_image',
        'mobile_image',
        'link_type',
        'link_url',
        'alt_text',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Scope for active slides
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for ordered slides
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Helper to get effective destination link
     */
    public function getDestinationUrlAttribute()
    {
        if ($this->link_type === 'none' || empty($this->link_url)) {
            return null;
        }

        return $this->link_url;
    }

    /**
     * Effective Desktop Image Source URL
     */
    public function getDesktopSrcAttribute(): ?string
    {
        return $this->resolveImageUrl($this->desktop_image);
    }

    /**
     * Effective Tablet Image Source URL (falls back to desktop)
     */
    public function getTabletSrcAttribute(): ?string
    {
        return $this->resolveImageUrl($this->tablet_image) ?: $this->desktop_src;
    }

    /**
     * Effective Mobile Image Source URL (falls back to tablet then desktop)
     */
    public function getMobileSrcAttribute(): ?string
    {
        return $this->resolveImageUrl($this->mobile_image) 
            ?: ($this->resolveImageUrl($this->tablet_image) ?: $this->desktop_src);
    }

    /**
     * Normalize URL whether absolute, external, or local asset
     */
    protected function resolveImageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')) {
            return $path;
        }
        return asset(ltrim($path, '/'));
    }
}
