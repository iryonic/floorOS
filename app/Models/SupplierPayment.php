<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payment_number',
        'supplier_id',
        'purchase_id',
        'payment_date',
        'amount',
        'payment_method',
        'reference_number',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function booted()
    {
        static::saved(function ($payment) {
            if ($payment->purchase) {
                $payment->purchase->recalculatePaymentStatus();
            }
            if ($payment->supplier) {
                $payment->supplier->recalculateBalance();
            }

            $supplierName = $payment->supplier?->name ?? 'Supplier';
            $poRef = $payment->purchase ? " (PO #{$payment->purchase->purchase_number})" : " (#{$payment->payment_number})";
            FinancialTransaction::updateOrCreate(
                [
                    'reference_type' => self::class,
                    'reference_id' => $payment->id,
                ],
                [
                    'type' => 'debit',
                    'amount' => $payment->amount,
                    'purpose' => "Supplier Payment: {$supplierName}{$poRef}",
                    'transaction_date' => $payment->payment_date ? $payment->payment_date->startOfDay() : now(),
                    'source' => 'purchase_payment',
                    'payment_method' => $payment->payment_method ?? 'cash',
                    'created_by' => $payment->created_by ?? auth()->id(),
                    'notes' => $payment->notes,
                ]
            );
        });

        static::deleted(function ($payment) {
            if ($payment->purchase) {
                $payment->purchase->recalculatePaymentStatus();
            }
            if ($payment->supplier) {
                $payment->supplier->recalculateBalance();
            }

            FinancialTransaction::where('reference_type', self::class)
                ->where('reference_id', $payment->id)
                ->delete();
        });
    }
}
