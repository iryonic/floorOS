<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enquiry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'enquiry_number',
        'name',
        'phone',
        'email',
        'product_id',
        'product_shade_id',
        'area_sqft',
        'preferred_contact',
        'message',
        'status',
        'assigned_to',
        'internal_notes',
        'converted_quotation_id',
        'converted_sale_id',
    ];

    protected $casts = [
        'area_sqft' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function shade()
    {
        return $this->belongsTo(ProductShade::class, 'product_shade_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function convertedQuotation()
    {
        return $this->belongsTo(Quotation::class, 'converted_quotation_id');
    }

    public function convertedSale()
    {
        return $this->belongsTo(Sale::class, 'converted_sale_id');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'new' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'label' => 'New Enquiry'],
            'contacted' => ['bg' => 'bg-blue-50 text-blue-700 border-blue-200', 'label' => 'Contacted'],
            'quotation_sent' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'label' => 'Quotation Sent'],
            'follow_up' => ['bg' => 'bg-purple-50 text-purple-700 border-purple-200', 'label' => 'Follow-up Required'],
            'converted' => ['bg' => 'bg-teal-50 text-teal-800 border-teal-200', 'label' => 'Converted to Sale'],
            'closed' => ['bg' => 'bg-neutral-100 text-neutral-600 border-neutral-200', 'label' => 'Closed'],
            default => ['bg' => 'bg-neutral-100 text-neutral-700 border-neutral-200', 'label' => ucfirst($this->status)],
        };
    }
}
