<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type', // asset, liability, equity, revenue, expense
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function getBalanceAttribute(): float
    {
        $debits = (float) $this->lines()->sum('debit');
        $credits = (float) $this->lines()->sum('credit');

        // Assets and Expenses have debit normal balance
        if (in_array($this->type, ['asset', 'expense'])) {
            return $debits - $credits;
        }

        // Liabilities, Equity, Revenue have credit normal balance
        return $credits - $debits;
    }
}
