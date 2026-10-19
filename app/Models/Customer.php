<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'company_name',
        'customer_type',
        'phone',
        'email',
        'address',
        'city',
        'pincode',
        'gstin',
        'opening_balance',
        'current_balance',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function recalculateBalance(): float
    {
        $salesTotal = $this->sales()
            ->where('status', 'confirmed')
            ->sum('total_amount');

        $paymentsTotal = $this->payments()->sum('amount');

        $balance = (float) ($this->opening_balance + $salesTotal - $paymentsTotal);
        $this->update(['current_balance' => $balance]);

        return $balance;
    }
}
