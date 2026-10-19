<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'company_name',
        'contact_person',
        'phone',
        'email',
        'address',
        'city',
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

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function recalculateBalance(): float
    {
        $purchasesTotal = $this->purchases()
            ->where('status', 'received')
            ->sum('total_amount');

        $paymentsTotal = $this->payments()->sum('amount');

        $balance = (float) ($this->opening_balance + $purchasesTotal - $paymentsTotal);
        $this->update(['current_balance' => $balance]);

        return $balance;
    }
}
