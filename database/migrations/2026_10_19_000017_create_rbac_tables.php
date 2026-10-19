<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Roles table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        // 2. Permissions table
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('group', 50); // Products, Inventory, Sales, Financials, Administration
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 3. Role Permission Pivot
        Schema::create('role_permission', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->primary(['role_id', 'permission_id']);
        });

        // 4. Add role_id to users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('role')->constrained('roles')->nullOnDelete();
        });

        // 5. Seed standard roles & permissions
        $this->seedInitialRbac();
    }

    protected function seedInitialRbac(): void
    {
        $permissions = [
            // Group: Products & Catalog
            ['name' => 'View Products', 'slug' => 'products.view', 'group' => 'Products & Catalog', 'description' => 'View product listings, collections, and shades'],
            ['name' => 'Manage Products', 'slug' => 'products.manage', 'group' => 'Products & Catalog', 'description' => 'Create, edit, and delete products, categories, and shade variants'],
            
            // Group: Inventory & Stock
            ['name' => 'View Stock', 'slug' => 'inventory.view', 'group' => 'Inventory & Stock', 'description' => 'View real-time stock balances and threshold alerts'],
            ['name' => 'Manage Inventory', 'slug' => 'inventory.manage', 'group' => 'Inventory & Stock', 'description' => 'Perform stock adjustments, audits, and record movements'],
            ['name' => 'View Cost Price', 'slug' => 'cost_price.view', 'group' => 'Inventory & Stock', 'description' => 'View unit cost price and inventory valuation figures'],

            // Group: Sales & Invoicing
            ['name' => 'View Sales', 'slug' => 'sales.view', 'group' => 'Sales & Orders', 'description' => 'View customer sales invoices, orders, and receipts'],
            ['name' => 'Create & Manage Sales', 'slug' => 'sales.manage', 'group' => 'Sales & Orders', 'description' => 'Create sales invoices, collect payments, and manage orders'],
            ['name' => 'Manage Quotations', 'slug' => 'quotations.manage', 'group' => 'Sales & Orders', 'description' => 'Create and convert customer quotations & estimates'],
            ['name' => 'Manage Customers', 'slug' => 'customers.manage', 'group' => 'Sales & Orders', 'description' => 'Manage customer ledger, dues, and profiles'],

            // Group: Purchasing & Suppliers
            ['name' => 'Manage Purchases', 'slug' => 'purchases.manage', 'group' => 'Purchases & Suppliers', 'description' => 'Record supplier purchase orders and stock receipts'],
            ['name' => 'Manage Suppliers', 'slug' => 'suppliers.manage', 'group' => 'Purchases & Suppliers', 'description' => 'Manage supplier master records and payable accounts'],

            // Group: Financials & Reports
            ['name' => 'Manage Expenses', 'slug' => 'expenses.manage', 'group' => 'Financials & Reports', 'description' => 'Record operating overheads and store expense vouchers'],
            ['name' => 'Manage Accounting', 'slug' => 'accounting.manage', 'group' => 'Financials & Reports', 'description' => 'View general ledger, journal entries, and P&L statements'],
            ['name' => 'View Business Reports', 'slug' => 'reports.view', 'group' => 'Financials & Reports', 'description' => 'Access sales analytics, stock velocity, and tax reports'],

            // Group: System & Administration
            ['name' => 'Manage Staff & Users', 'slug' => 'users.manage', 'group' => 'Administration', 'description' => 'Invite, edit, and deactivate store employees'],
            ['name' => 'Manage Roles & RBAC', 'slug' => 'rbac.manage', 'group' => 'Administration', 'description' => 'Create, edit, and configure role-based permissions'],
            ['name' => 'Manage System Settings', 'slug' => 'settings.manage', 'group' => 'Administration', 'description' => 'Configure store profile, tax rules, and dynamic theme variables'],
        ];

        $now = now();
        foreach ($permissions as &$p) {
            $p['created_at'] = $now;
            $p['updated_at'] = $now;
        }
        DB::table('permissions')->insert($permissions);

        // Standard Roles
        $roles = [
            [
                'name' => 'Store Owner',
                'slug' => 'owner',
                'description' => 'Full administrative access to all store operations, financials, system settings, and RBAC control.',
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Store Manager',
                'slug' => 'manager',
                'description' => 'Operational management across catalog, sales, inventory, customers, and basic financial views.',
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Sales Specialist',
                'slug' => 'sales',
                'description' => 'Front-of-house sales operations, customer quotes, invoice generation, and customer dues.',
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Inventory Controller',
                'slug' => 'inventory',
                'description' => 'Warehouse stock adjustments, receipts, product catalog maintenance, and stock audits.',
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Head Accountant',
                'slug' => 'accountant',
                'description' => 'Ledger management, expense tracking, invoice payments, tax calculations, and financial reports.',
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('roles')->insert($roles);

        // Fetch inserted IDs
        $permMap = DB::table('permissions')->pluck('id', 'slug')->toArray();
        $roleMap = DB::table('roles')->pluck('id', 'slug')->toArray();

        // 1. Owner gets ALL permissions
        $ownerPerms = [];
        foreach ($permMap as $slug => $id) {
            $ownerPerms[] = ['role_id' => $roleMap['owner'], 'permission_id' => $id];
        }
        DB::table('role_permission')->insert($ownerPerms);

        // 2. Manager permissions
        $managerSlugs = [
            'products.view', 'products.manage', 'inventory.view', 'inventory.manage', 'cost_price.view',
            'sales.view', 'sales.manage', 'quotations.manage', 'customers.manage',
            'purchases.manage', 'suppliers.manage', 'expenses.manage', 'reports.view', 'users.manage'
        ];
        $managerPerms = [];
        foreach ($managerSlugs as $slug) {
            if (isset($permMap[$slug])) {
                $managerPerms[] = ['role_id' => $roleMap['manager'], 'permission_id' => $permMap[$slug]];
            }
        }
        DB::table('role_permission')->insert($managerPerms);

        // 3. Sales permissions
        $salesSlugs = ['products.view', 'inventory.view', 'sales.view', 'sales.manage', 'quotations.manage', 'customers.manage'];
        $salesPerms = [];
        foreach ($salesSlugs as $slug) {
            if (isset($permMap[$slug])) {
                $salesPerms[] = ['role_id' => $roleMap['sales'], 'permission_id' => $permMap[$slug]];
            }
        }
        DB::table('role_permission')->insert($salesPerms);

        // 4. Inventory permissions
        $inventorySlugs = ['products.view', 'products.manage', 'inventory.view', 'inventory.manage', 'cost_price.view', 'purchases.manage', 'suppliers.manage'];
        $inventoryPerms = [];
        foreach ($inventorySlugs as $slug) {
            if (isset($permMap[$slug])) {
                $inventoryPerms[] = ['role_id' => $roleMap['inventory'], 'permission_id' => $permMap[$slug]];
            }
        }
        DB::table('role_permission')->insert($inventoryPerms);

        // 5. Accountant permissions
        $accountantSlugs = ['inventory.view', 'cost_price.view', 'sales.view', 'customers.manage', 'purchases.manage', 'suppliers.manage', 'expenses.manage', 'accounting.manage', 'reports.view'];
        $accountantPerms = [];
        foreach ($accountantSlugs as $slug) {
            if (isset($permMap[$slug])) {
                $accountantPerms[] = ['role_id' => $roleMap['accountant'], 'permission_id' => $permMap[$slug]];
            }
        }
        DB::table('role_permission')->insert($accountantPerms);

        // Map existing users to their role_id
        foreach ($roleMap as $slug => $roleId) {
            DB::table('users')->where('role', $slug)->update(['role_id' => $roleId]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });

        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
