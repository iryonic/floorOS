<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'role_id',
        'phone',
        'avatar',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roleRelation()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function hasPermission(string $permissionSlug): bool
    {
        if ($this->isOwner()) {
            return true;
        }

        if ($this->roleRelation) {
            return $this->roleRelation->hasPermission($permissionSlug);
        }

        // Fallback mapping for legacy string roles
        return match ($this->role) {
            'owner' => true,
            'manager' => in_array($permissionSlug, [
                'products.view', 'products.manage', 'inventory.view', 'inventory.manage',
                'cost_price.view', 'sales.view', 'sales.manage', 'quotations.manage',
                'customers.manage', 'purchases.manage', 'suppliers.manage', 'expenses.manage',
                'reports.view', 'users.manage'
            ]),
            'sales' => in_array($permissionSlug, [
                'products.view', 'inventory.view', 'sales.view', 'sales.manage',
                'quotations.manage', 'customers.manage'
            ]),
            'inventory' => in_array($permissionSlug, [
                'products.view', 'products.manage', 'inventory.view', 'inventory.manage',
                'cost_price.view', 'purchases.manage', 'suppliers.manage'
            ]),
            'accountant' => in_array($permissionSlug, [
                'inventory.view', 'cost_price.view', 'sales.view', 'customers.manage',
                'purchases.manage', 'suppliers.manage', 'expenses.manage', 'accounting.manage',
                'reports.view'
            ]),
            default => false,
        };
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner' || optional($this->roleRelation)->slug === 'owner';
    }

    public function isManager(): bool
    {
        return in_array($this->role, ['owner', 'manager']) || in_array(optional($this->roleRelation)->slug, ['owner', 'manager']);
    }

    public function canManageProducts(): bool
    {
        return $this->hasPermission('products.manage');
    }

    public function canManageInventory(): bool
    {
        return $this->hasPermission('inventory.manage');
    }

    public function canManageSales(): bool
    {
        return $this->hasPermission('sales.manage');
    }

    public function canManageFinances(): bool
    {
        return $this->hasPermission('accounting.manage') || $this->hasPermission('expenses.manage');
    }

    public function canManageSettings(): bool
    {
        return $this->hasPermission('settings.manage');
    }

    public function canViewCostPrice(): bool
    {
        return $this->hasPermission('cost_price.view');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'created_by');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'created_by');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'created_by');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'user_id');
    }

    public function enquiries()
    {
        return $this->hasMany(Enquiry::class, 'assigned_to');
    }
}
