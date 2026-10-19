<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_number',
        'expense_category_id',
        'date',
        'title',
        'description',
        'amount',
        'payment_method',
        'payee',
        'receipt_attachment',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function booted()
    {
        static::saved(function ($expense) {
            $catName = $expense->category?->name ?? 'General';
            FinancialTransaction::updateOrCreate(
                [
                    'reference_type' => self::class,
                    'reference_id' => $expense->id,
                ],
                [
                    'type' => 'debit',
                    'amount' => $expense->amount,
                    'purpose' => "Expense: {$expense->title} ({$catName})",
                    'transaction_date' => $expense->date ? $expense->date->startOfDay() : now(),
                    'source' => 'expense',
                    'payment_method' => $expense->payment_method ?? 'cash',
                    'created_by' => $expense->created_by ?? auth()->id(),
                    'notes' => $expense->notes,
                ]
            );
        });

        static::deleted(function ($expense) {
            FinancialTransaction::where('reference_type', self::class)
                ->where('reference_id', $expense->id)
                ->delete();
        });
    }
}
