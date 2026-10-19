<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'amount',
        'purpose',
        'transaction_date',
        'source',
        'payment_method',
        'reference_type',
        'reference_id',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo('reference');
    }

    public function isCredit(): bool
    {
        return $this->type === 'credit';
    }

    public function isDebit(): bool
    {
        return $this->type === 'debit';
    }

    public function isManual(): bool
    {
        return $this->source === 'manual';
    }

    public function getSourceLabelAttribute(): string
    {
        return match ($this->source) {
            'sale_payment' => 'Customer Sale',
            'purchase_payment' => 'Supplier Purchase',
            'expense' => 'Operating Expense',
            default => 'Manual Entry',
        };
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match (strtolower($this->payment_method ?? 'cash')) {
            'cash' => 'Cash',
            'mpay' => 'mPay',
            'gpay' => 'GPay',
            'paytm' => 'Paytm',
            'phonepe' => 'PhonePe',
            'bank_transfer' => 'Bank Transfer',
            'cheque' => 'Cheque',
            default => ucwords(str_replace('_', ' ', $this->payment_method ?? 'Cash')),
        };
    }
}
