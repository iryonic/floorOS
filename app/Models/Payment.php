<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'receipt_number',
        'customer_id',
        'sale_id',
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

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function booted()
    {
        static::saved(function ($payment) {
            if ($payment->sale) {
                $payment->sale->recalculatePaymentStatus();
            }
            if ($payment->customer) {
                $payment->customer->recalculateBalance();
            }

            $customerName = $payment->customer?->name ?? 'Customer';
            $invoiceRef = $payment->sale ? " (Invoice #{$payment->sale->invoice_number})" : " (#{$payment->receipt_number})";
            FinancialTransaction::updateOrCreate(
                [
                    'reference_type' => self::class,
                    'reference_id' => $payment->id,
                ],
                [
                    'type' => 'credit',
                    'amount' => $payment->amount,
                    'purpose' => "Customer Payment: {$customerName}{$invoiceRef}",
                    'transaction_date' => $payment->payment_date ? $payment->payment_date->startOfDay() : now(),
                    'source' => 'sale_payment',
                    'payment_method' => $payment->payment_method ?? 'cash',
                    'created_by' => $payment->created_by ?? auth()->id(),
                    'notes' => $payment->notes,
                ]
            );
        });

        static::deleted(function ($payment) {
            if ($payment->sale) {
                $payment->sale->recalculatePaymentStatus();
            }
            if ($payment->customer) {
                $payment->customer->recalculateBalance();
            }

            FinancialTransaction::where('reference_type', self::class)
                ->where('reference_id', $payment->id)
                ->delete();
        });
    }
}
