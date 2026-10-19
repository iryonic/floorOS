<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FinancialTransaction;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductShade;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\User;
use App\Services\AccountingService;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FloorOSTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed core accounts for double-entry testing
        Account::firstOrCreate(['code' => '1010'], ['name' => 'Cash in Hand', 'type' => 'asset']);
        Account::firstOrCreate(['code' => '1020'], ['name' => 'Bank & UPI Account', 'type' => 'asset']);
        Account::firstOrCreate(['code' => '1030'], ['name' => 'Customer Receivables (AR)', 'type' => 'asset']);
        Account::firstOrCreate(['code' => '1040'], ['name' => 'Inventory Asset', 'type' => 'asset']);
        Account::firstOrCreate(['code' => '2010'], ['name' => 'Supplier Payables (AP)', 'type' => 'liability']);
        Account::firstOrCreate(['code' => '4010'], ['name' => 'Flooring Sales Revenue', 'type' => 'revenue']);
        Account::firstOrCreate(['code' => '5010'], ['name' => 'Cost of Goods Sold (COGS)', 'type' => 'expense']);
        Account::firstOrCreate(['code' => '6010'], ['name' => 'General Operating Expenses', 'type' => 'expense']);
    }

    public function test_unauthenticated_users_are_redirected_to_login(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/admin/login');
    }

    public function test_authenticated_owner_can_access_admin_dashboard(): void
    {
        $owner = User::create([
            'name' => 'Test Owner',
            'email' => 'owner@test.com',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'status' => 'active',
        ]);

        $response = $this->actingAs($owner)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    public function test_public_catalog_shows_published_products_and_hides_drafts(): void
    {
        $category = Category::create([
            'name' => 'Hardwood',
            'slug' => 'hardwood',
            'is_active' => true,
        ]);

        $published = Product::create([
            'name' => 'Royal Teak Plank',
            'slug' => 'royal-teak-plank',
            'category_id' => $category->id,
            'status' => 'published',
            'is_public' => true,
        ]);

        $draft = Product::create([
            'name' => 'Secret Draft Flooring',
            'slug' => 'secret-draft-flooring',
            'category_id' => $category->id,
            'status' => 'draft',
            'is_public' => false,
        ]);

        $response = $this->get('/catalog');
        $response->assertStatus(200);
        $response->assertSee('Royal Teak Plank');
        $response->assertDontSee('Secret Draft Flooring');
    }

    public function test_confirmed_purchase_increases_stock_while_draft_does_not(): void
    {
        $supplier = Supplier::create([
            'name' => 'Test Timber Mill',
            'phone' => '9988776655',
        ]);

        $category = Category::create(['name' => 'Oak', 'slug' => 'oak', 'is_active' => true]);
        $product = Product::create([
            'name' => 'Engineered Oak',
            'slug' => 'engineered-oak',
            'category_id' => $category->id,
            'status' => 'published',
            'is_public' => true,
        ]);

        $shade = ProductShade::create([
            'product_id' => $product->id,
            'shade_name' => 'Natural Oak',
            'sku' => 'OAK-NAT-01',
            'stock_quantity' => 10.00,
            'cost_price' => 150.00,
            'selling_price' => 250.00,
            'unit' => 'sq ft',
        ]);

        $user = User::create([
            'name' => 'Warehouse Staff',
            'email' => 'wh@test.com',
            'password' => Hash::make('password'),
            'role' => 'inventory',
            'status' => 'active',
        ]);

        $inventoryService = app(InventoryService::class);

        // 1. Inward confirmed purchase (+20 sq ft)
        $purchase = Purchase::create([
            'purchase_number' => 'PUR-TEST-001',
            'supplier_id' => $supplier->id,
            'purchase_date' => now()->toDateString(),
            'subtotal' => 3000,
            'total_amount' => 3000,
            'paid_amount' => 0,
            'due_amount' => 3000,
            'status' => 'received',
            'payment_status' => 'unpaid',
            'payment_method' => 'bank_transfer',
        ]);

        $inventoryService->recordMovement(
            shade: $shade,
            type: 'purchase',
            quantity: 20,
            direction: 'in',
            unitCost: 150,
            reason: 'Purchase receipt test',
            related: $purchase,
            user: $user
        );

        $shade->refresh();
        $this->assertEquals(30.00, $shade->stock_quantity);

        // 2. Draft purchase does NOT call recordMovement, stock remains 30.00
        $draftPurchase = Purchase::create([
            'purchase_number' => 'PUR-DRAFT-001',
            'supplier_id' => $supplier->id,
            'purchase_date' => now()->toDateString(),
            'subtotal' => 1500,
            'total_amount' => 1500,
            'paid_amount' => 0,
            'due_amount' => 1500,
            'status' => 'draft',
            'payment_status' => 'unpaid',
            'payment_method' => 'bank_transfer',
        ]);

        $shade->refresh();
        $this->assertEquals(30.00, $shade->stock_quantity);
    }

    public function test_confirmed_sale_deducts_stock_and_creates_balanced_journal_entry(): void
    {
        $customer = Customer::create([
            'name' => 'Anil Kapoor',
            'phone' => '9820098200',
            'customer_type' => 'individual',
        ]);

        $category = Category::create(['name' => 'Teak', 'slug' => 'teak', 'is_active' => true]);
        $product = Product::create([
            'name' => 'Burma Teak',
            'slug' => 'burma-teak',
            'category_id' => $category->id,
            'status' => 'published',
            'is_public' => true,
        ]);

        $shade = ProductShade::create([
            'product_id' => $product->id,
            'shade_name' => 'Golden Teak',
            'sku' => 'TEAK-GLD-01',
            'stock_quantity' => 50.00,
            'cost_price' => 200.00,
            'selling_price' => 350.00,
            'unit' => 'sq ft',
        ]);

        $user = User::create([
            'name' => 'Sales Guy',
            'email' => 'sales@test.com',
            'password' => Hash::make('password'),
            'role' => 'sales',
            'status' => 'active',
        ]);

        $inventoryService = app(InventoryService::class);
        $accountingService = app(AccountingService::class);

        // Deduct 15 sq ft on confirmed sale
        $sale = Sale::create([
            'invoice_number' => 'INV-TEST-001',
            'customer_id' => $customer->id,
            'date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'subtotal' => 5250,
            'discount_type' => 'fixed',
            'discount_amount' => 0,
            'tax_rate' => 18,
            'tax_amount' => 945,
            'total_amount' => 6195,
            'paid_amount' => 2000,
            'due_amount' => 4195,
            'payment_status' => 'partially_paid',
            'status' => 'confirmed',
            'payment_method' => 'upi',
            'created_by' => $user->id,
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'product_shade_id' => $shade->id,
            'description' => 'Burma Teak — Golden Teak',
            'sku' => 'TEAK-GLD-01',
            'quantity' => 15,
            'unit' => 'sq ft',
            'unit_price' => 350,
            'cost_price_at_sale' => 200,
            'discount_amount' => 0,
            'total' => 5250,
        ]);

        $inventoryService->recordMovement(
            shade: $shade,
            type: 'sale',
            quantity: 15,
            direction: 'out',
            unitCost: 200,
            reason: "Sale #{$sale->invoice_number}",
            related: $sale,
            user: $user
        );

        $shade->refresh();
        $this->assertEquals(35.00, $shade->stock_quantity);

        // Post accounting journal
        $accountingService->postSale($sale);

        // Verify Journal Entries are strictly balanced (Debits == Credits)
        $sale->refresh();
        $journalEntry = \App\Models\JournalEntry::where('reference_type', Sale::class)
            ->where('reference_id', $sale->id)
            ->with('lines')
            ->first();

        $this->assertNotNull($journalEntry);
        $totalDebit = round($journalEntry->lines->sum('debit'), 2);
        $totalCredit = round($journalEntry->lines->sum('credit'), 2);
        $this->assertEquals($totalDebit, $totalCredit);
        $this->assertGreaterThan(0, $totalDebit);
    }

    public function test_theme_css_variables_injected_into_views(): void
    {
        Setting::set('theme_primary', '#2EAA1F', 'theme');
        Setting::set('theme_radius', '12px', 'theme');

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('--color-primary: #2EAA1F', false);
        $response->assertSee('--radius-base: 12px', false);
    }

    public function test_rbac_management_accessible_by_owner(): void
    {
        $owner = User::create([
            'name' => 'RBAC Owner',
            'email' => 'rbac_owner@test.com',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'status' => 'active',
        ]);

        $response = $this->actingAs($owner)->get('/admin/rbac');
        $response->assertStatus(200);
        $response->assertSee('Role-Based Access Control');
        $response->assertSee('Role & Permission Authorization Matrix', false);
    }

    public function test_public_enquiry_submits_without_approximate_area(): void
    {
        $response = $this->post('/enquiry', [
            'name' => 'Alice Customer',
            'phone' => '9876543210',
            'preferred_contact' => 'whatsapp',
            'message' => 'Interested in herringbone oak for living room.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('enquiries', [
            'name' => 'Alice Customer',
            'phone' => '9876543210',
        ]);
    }

    public function test_product_optional_fields_visibility_setting(): void
    {
        $defaults = Setting::getProductFieldToggles();
        $this->assertIsArray($defaults);
        $this->assertTrue(Setting::isProductFieldVisible('finish'));

        // Toggle off finish
        $defaults['finish'] = false;
        Setting::set('product_form_optional_fields', json_encode($defaults), 'catalog');

        $this->assertFalse(Setting::isProductFieldVisible('finish'));
        $this->assertTrue(Setting::isProductFieldVisible('brand'));
    }

    public function test_sales_routes_are_decommissioned_and_dashboard_renders(): void
    {
        $owner = User::create([
            'name' => 'Store Owner',
            'email' => 'owner2@test.com',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'status' => 'active',
        ]);

        // Verify sales routes return 404
        $salesResponse = $this->actingAs($owner)->get('/admin/sales');
        $salesResponse->assertStatus(404);

        // Verify dashboard renders cleanly with catalog and cash flow metrics
        $dashResponse = $this->actingAs($owner)->get('/admin');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Business Dashboard');
        $dashResponse->assertSee('Financial Cash Flow');
    }

    public function test_rbac_permission_toggle(): void
    {
        $owner = User::create([
            'name' => 'Store Owner',
            'email' => 'owner3@test.com',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'status' => 'active',
        ]);

        $role = \App\Models\Role::create([
            'name' => 'Sales Representative',
            'slug' => 'sales_rep',
        ]);
        $permission = \App\Models\Permission::create([
            'name' => 'Manage Inventory',
            'slug' => 'manage_inventory',
            'group' => 'Inventory',
        ]);

        $response = $this->actingAs($owner)->postJson("/admin/rbac/{$role->id}/toggle-permission", [
            'permission_id' => $permission->id,
            'enable' => true,
        ]);
        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'is_enabled' => true]);
        $this->assertTrue($role->fresh()->permissions->contains($permission->id));

        // Toggle again to remove
        $response2 = $this->actingAs($owner)->postJson("/admin/rbac/{$role->id}/toggle-permission", [
            'permission_id' => $permission->id,
            'enable' => false,
        ]);
        $response2->assertStatus(200);
        $response2->assertJson(['success' => true, 'is_enabled' => false]);
        $this->assertFalse($role->fresh()->permissions->contains($permission->id));
    }

    public function test_invoice_settings_defaults_and_override(): void
    {
        $settings = Setting::getInvoiceSettings();
        $this->assertArrayHasKey('invoice_title', $settings);
        $this->assertSame('TAX INVOICE', $settings['invoice_title']);

        Setting::set('invoice_title', 'OFFICIAL BILL OF SUPPLY', 'invoice');
        $updated = Setting::getInvoiceSettings();
        $this->assertSame('OFFICIAL BILL OF SUPPLY', $updated['invoice_title']);
    }

    public function test_admin_products_index_renders_grid_view_by_default(): void
    {
        $owner = User::create([
            'name' => 'Store Owner',
            'email' => 'owner_prod@test.com',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'status' => 'active',
        ]);

        $category = Category::create([
            'name' => 'Hardwood Flooring',
            'slug' => 'hardwood-flooring',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Versailles Chevron Parquet',
            'slug' => 'versailles-chevron-parquet',
            'unit_of_measure' => 'sq ft',
            'base_price' => 550,
            'status' => 'published',
            'is_public' => true,
        ]);

        $shade = ProductShade::create([
            'product_id' => $product->id,
            'shade_name' => 'Cognac Oak',
            'sku' => 'VCP-COG-01',
            'selling_price' => 550,
            'cost_price' => 380,
            'stock_quantity' => 1200,
            'reorder_threshold' => 100,
            'is_active' => true,
            'is_public' => true,
        ]);

        $response = $this->actingAs($owner)->get('/admin/products');
        $response->assertStatus(200);
        $response->assertSee('id="productsGridView"', false);
        $response->assertSee('Versailles Chevron Parquet');
        $response->assertSee('Cognac Oak');
        $response->assertSee('Add Shade');
    }

    public function test_admin_can_add_new_shade_to_product(): void
    {
        $owner = User::create([
            'name' => 'Store Owner',
            'email' => 'owner_shade@test.com',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'status' => 'active',
        ]);

        $category = Category::create([
            'name' => 'Engineered Wood',
            'slug' => 'engineered-wood',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Nordic Pine Plank',
            'slug' => 'nordic-pine-plank',
            'unit_of_measure' => 'sq ft',
            'base_price' => 420,
            'status' => 'published',
            'is_public' => true,
        ]);

        $response = $this->actingAs($owner)->postJson("/admin/products/{$product->id}/shades", [
            'shade_name' => 'Arctic White Pine',
            'sku' => 'NPP-WHT-01',
            'shade_code' => 'PINE-01',
            'selling_price' => 450,
            'cost_price' => 300,
            'stock_quantity' => 500,
            'reorder_threshold' => 50,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('product_shades', [
            'product_id' => $product->id,
            'shade_name' => 'Arctic White Pine',
            'sku' => 'NPP-WHT-01',
            'stock_quantity' => 500,
        ]);
    }

    public function test_admin_dashboard_renders_successfully_even_with_soft_deleted_products(): void
    {
        $owner = User::create([
            'name' => 'Dashboard Owner',
            'email' => 'dashowner@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Laminate Floors',
            'slug' => 'laminate-floors-' . time(),
            'is_active' => true,
        ]);

        // Create a product that will be soft-deleted
        $archivedProduct = Product::create([
            'category_id' => $category->id,
            'name' => 'Legacy Archived Plank',
            'slug' => 'legacy-archived-plank-' . time(),
            'unit_of_measure' => 'sq ft',
            'base_price' => 350,
            'status' => 'archived',
            'is_public' => false,
        ]);

        $archivedShade = ProductShade::create([
            'product_id' => $archivedProduct->id,
            'shade_name' => 'Old Oak',
            'sku' => 'LEG-OAK-' . rand(1000, 9999),
            'selling_price' => 350,
            'cost_price' => 200,
            'stock_quantity' => 5, // low stock
            'reorder_threshold' => 20,
            'unit' => 'sq ft',
            'is_active' => true,
        ]);

        // Soft delete the product
        $archivedProduct->delete();

        // Dashboard should render with 200 OK without "Attempt to read property 'name' on null"
        $response = $this->actingAs($owner)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Low Stock Alerts');
    }

    public function test_admin_can_create_product_with_shades(): void
    {
        $owner = User::create([
            'name' => 'Product Creator',
            'email' => 'creator@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Vinyl Flooring',
            'slug' => 'vinyl-flooring-' . time(),
            'is_active' => true,
        ]);

        $sku1 = 'VNL-OAK-' . rand(1000, 9999);
        $sku2 = 'VNL-WHT-' . rand(1000, 9999);

        $response = $this->actingAs($owner)->post('/admin/products', [
            'name' => 'Royal Luxury Vinyl Tiles',
            'category_id' => $category->id,
            'unit_of_measure' => 'sq ft',
            'base_price' => 290,
            'status' => 'published',
            'is_public' => 1,
            'shades' => [
                [
                    'shade_name' => 'Natural Honey Oak',
                    'sku' => $sku1,
                    'shade_code' => 'OAK-01',
                    'selling_price' => 290,
                    'cost_price' => 180,
                    'stock_quantity' => 200,
                    'reorder_threshold' => 30,
                ],
                [
                    'shade_name' => 'Alabaster White',
                    'sku' => $sku2,
                    'shade_code' => 'WHT-02',
                    'selling_price' => 310,
                    'cost_price' => 195,
                    'stock_quantity' => 150,
                    'reorder_threshold' => 25,
                ],
            ],
        ]);

        $response->assertRedirect('/admin/products');
        $this->assertDatabaseHas('product_shades', ['sku' => $sku1]);
        $this->assertDatabaseHas('product_shades', ['sku' => $sku2]);
    }

    public function test_admin_can_view_simplified_accounting_dashboard(): void
    {
        $owner = User::create([
            'name' => 'Accounting Admin',
            'email' => 'acc_admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($owner)->get('/admin/accounting');
        $response->assertStatus(200);
        $response->assertSee('Accounting & Financial Flow', false);
        $response->assertSee('Credit / Deposit');
        $response->assertSee('Debit / Expense');
        $response->assertSee('Total Credit');
        $response->assertSee('Total Debit');
        $response->assertSee('Net Cash Flow');
        $response->assertSee('Sales Revenue');
        $response->assertSee('Inventory Value');
    }

    public function test_admin_can_record_manual_credit_and_debit_transactions(): void
    {
        $owner = User::create([
            'name' => 'Accounting Admin 2',
            'email' => 'acc_admin2@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // 1. Record Credit
        $creditResp = $this->actingAs($owner)->post('/admin/accounting/credit', [
            'amount' => 50000.00,
            'purpose' => 'Director Capital Infusion',
            'notes' => 'Cheque deposit into Axis bank',
        ]);
        $creditResp->assertRedirect();

        $this->assertDatabaseHas('financial_transactions', [
            'type' => 'credit',
            'amount' => 50000.00,
            'purpose' => 'Director Capital Infusion',
            'source' => 'manual',
        ]);

        // 2. Record Debit
        $debitResp = $this->actingAs($owner)->post('/admin/accounting/debit', [
            'amount' => 12500.00,
            'purpose' => 'Showroom Paint & Touchup Work',
            'notes' => 'Cash voucher #401',
        ]);
        $debitResp->assertRedirect();

        $this->assertDatabaseHas('financial_transactions', [
            'type' => 'debit',
            'amount' => 12500.00,
            'purpose' => 'Showroom Paint & Touchup Work',
            'source' => 'manual',
        ]);

        // 3. Verify dashboard displays the entries
        $dash = $this->actingAs($owner)->get('/admin/accounting?period=all');
        $dash->assertStatus(200);
        $dash->assertSee('Director Capital Infusion');
        $dash->assertSee('Showroom Paint &amp; Touchup Work', false);
    }

    public function test_customer_payment_and_expense_sync_with_financial_transactions(): void
    {
        $owner = User::create([
            'name' => 'Sync Admin',
            'email' => 'sync_admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $customer = Customer::create([
            'name' => 'Priya Nair',
            'customer_type' => 'individual',
            'phone' => '9845012345',
            'city' => 'Bengaluru',
            'is_active' => true,
        ]);

        // Create Payment -> automatically syncs FinancialTransaction Credit
        $payment = Payment::create([
            'receipt_number' => 'RCP-TEST-' . rand(1000, 9999),
            'customer_id' => $customer->id,
            'payment_date' => now()->toDateString(),
            'amount' => 35000.00,
            'payment_method' => 'upi',
            'notes' => 'Advance booking token',
            'created_by' => $owner->id,
        ]);

        $this->assertDatabaseHas('financial_transactions', [
            'type' => 'credit',
            'amount' => 35000.00,
            'source' => 'sale_payment',
            'reference_type' => Payment::class,
            'reference_id' => $payment->id,
        ]);

        // Create Expense Category & Expense -> automatically syncs FinancialTransaction Debit
        $expCategory = ExpenseCategory::firstOrCreate(['name' => 'Facility Cleaning'], ['is_active' => true]);
        $expense = Expense::create([
            'reference_number' => 'EXP-TEST-' . rand(1000, 9999),
            'expense_category_id' => $expCategory->id,
            'date' => now()->toDateString(),
            'title' => 'Deep Cleaning Services',
            'amount' => 4500.00,
            'payment_method' => 'cash',
            'created_by' => $owner->id,
        ]);

        $this->assertDatabaseHas('financial_transactions', [
            'type' => 'debit',
            'amount' => 4500.00,
            'source' => 'expense',
            'reference_type' => Expense::class,
            'reference_id' => $expense->id,
        ]);
    }

    public function test_admin_can_delete_manual_financial_transaction_only(): void
    {
        $owner = User::create([
            'name' => 'Delete Admin',
            'email' => 'del_admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // 1. Manual transaction can be deleted
        $manualTxn = FinancialTransaction::create([
            'type' => 'credit',
            'amount' => 1000.00,
            'purpose' => 'Temporary test deposit',
            'transaction_date' => now(),
            'source' => 'manual',
            'created_by' => $owner->id,
        ]);

        $resp = $this->actingAs($owner)->delete("/admin/accounting/transactions/{$manualTxn->id}");
        $resp->assertRedirect();
        $this->assertSoftDeleted('financial_transactions', ['id' => $manualTxn->id]);

        // 2. Auto transaction cannot be deleted directly
        $autoTxn = FinancialTransaction::create([
            'type' => 'credit',
            'amount' => 5000.00,
            'purpose' => 'Auto customer payment',
            'transaction_date' => now(),
            'source' => 'sale_payment',
            'reference_type' => Payment::class,
            'reference_id' => 999,
        ]);

        $resp2 = $this->actingAs($owner)->delete("/admin/accounting/transactions/{$autoTxn->id}");
        $resp2->assertSessionHas('error');
        $this->assertDatabaseHas('financial_transactions', [
            'id' => $autoTxn->id,
            'deleted_at' => null,
        ]);
    }

    public function test_admin_dashboard_displays_integrated_accounting_cash_flow_and_modals(): void
    {
        $owner = User::create([
            'name' => 'Dash Admin',
            'email' => 'dash_admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        FinancialTransaction::create([
            'type' => 'credit',
            'amount' => 12500.00,
            'purpose' => 'Capital Infusion',
            'transaction_date' => now(),
            'source' => 'manual',
            'created_by' => $owner->id,
        ]);

        FinancialTransaction::create([
            'type' => 'debit',
            'amount' => 3200.00,
            'purpose' => 'Store Utilities',
            'transaction_date' => now(),
            'source' => 'manual',
            'created_by' => $owner->id,
        ]);

        $response = $this->actingAs($owner)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Financial Cash Flow & Ledger', false);
        $response->assertSee('+ Record Money In');
        $response->assertSee('- Record Money Out');
        $response->assertSee('12,500.00');
        $response->assertSee('3,200.00');
        $response->assertSee('Capital Infusion');
        $response->assertSee('creditModal');
        $response->assertSee('debitModal');
    }

    public function test_credit_and_debit_support_payment_method_with_default_cash(): void
    {
        $owner = User::create([
            'name' => 'Payment Method Admin',
            'email' => 'pm_admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // 1. Credit without payment method defaults to cash
        $resp1 = $this->actingAs($owner)->post('/admin/accounting/credit', [
            'amount' => 5000.00,
            'purpose' => 'Token cash payment',
        ]);
        $resp1->assertRedirect();

        $this->assertDatabaseHas('financial_transactions', [
            'type' => 'credit',
            'amount' => 5000.00,
            'purpose' => 'Token cash payment',
            'payment_method' => 'cash',
        ]);

        // 2. Credit with specified payment method (e.g. GPay)
        $resp2 = $this->actingAs($owner)->post('/admin/accounting/credit', [
            'amount' => 15000.00,
            'purpose' => 'Client online transfer',
            'payment_method' => 'gpay',
        ]);
        $resp2->assertRedirect();

        $this->assertDatabaseHas('financial_transactions', [
            'type' => 'credit',
            'amount' => 15000.00,
            'purpose' => 'Client online transfer',
            'payment_method' => 'gpay',
        ]);

        // 3. Debit with specified payment method (e.g. mPay)
        $resp3 = $this->actingAs($owner)->post('/admin/accounting/debit', [
            'amount' => 1200.00,
            'purpose' => 'Plumber repair fees',
            'payment_method' => 'mpay',
        ]);
        $resp3->assertRedirect();

        $this->assertDatabaseHas('financial_transactions', [
            'type' => 'debit',
            'amount' => 1200.00,
            'purpose' => 'Plumber repair fees',
            'payment_method' => 'mpay',
        ]);

        // 4. Debit without payment method defaults to cash
        $resp4 = $this->actingAs($owner)->post('/admin/accounting/debit', [
            'amount' => 450.00,
            'purpose' => 'Office tea and coffee',
        ]);
        $resp4->assertRedirect();

        $this->assertDatabaseHas('financial_transactions', [
            'type' => 'debit',
            'amount' => 450.00,
            'purpose' => 'Office tea and coffee',
            'payment_method' => 'cash',
        ]);

        // 5. Verify modal rendered contains the select dropdown and options
        $viewResp = $this->actingAs($owner)->get('/admin/accounting');
        $viewResp->assertStatus(200);
        $viewResp->assertSee('creditPaymentMethodInput');
        $viewResp->assertSee('debitPaymentMethodInput');
        $viewResp->assertSee('mPay');
        $viewResp->assertSee('GPay (Google Pay)');
        $viewResp->assertSee('Paytm');
        $viewResp->assertSee('PhonePe');
        $viewResp->assertSee('Bank Transfer');
        $viewResp->assertSee('Cheque');

        // 6. Verify mobile bottom nav bar includes + Money In and - Money Out buttons and modal triggers
        $viewResp->assertSee('openCreditModal()');
        $viewResp->assertSee('openDebitModal()');
        $viewResp->assertSee('+ Money In');
        $viewResp->assertSee('- Money Out');
    }

    public function test_credit_and_debit_submission_keeps_user_on_same_page(): void
    {
        $owner = User::create([
            'name' => 'Redirect Tester',
            'email' => 'redirect_test@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // 1. Submit from dashboard with return_url keeps on dashboard
        $resp1 = $this->actingAs($owner)->post('/admin/accounting/credit', [
            'amount' => 1000.00,
            'purpose' => 'Cash in',
            'return_url' => 'http://localhost:8000/admin',
        ]);
        $resp1->assertRedirect('http://localhost:8000/admin');

        // 2. Submit from products page via referer keeps on products page
        $resp2 = $this->actingAs($owner)->from('/admin/products')->post('/admin/accounting/debit', [
            'amount' => 500.00,
            'purpose' => 'Office supply',
        ]);
        $resp2->assertRedirect('/admin/products');
    }

    public function test_password_visibility_toggles_and_auth_features(): void
    {
        // 1. Login view has password visibility toggle
        $loginResp = $this->get('/admin/login');
        $loginResp->assertStatus(200);
        $loginResp->assertSee('togglePasswordVisibility');
        $loginResp->assertSee('passwordToggleIcon');
        $loginResp->assertSee('Keep me signed in (up to 1 week)');

        $owner = User::create([
            'name' => 'Security Admin',
            'email' => 'secadmin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'is_active' => true,
        ]);

        // 2. User create view has password visibility toggle
        $createResp = $this->actingAs($owner)->get('/admin/users/create');
        $createResp->assertStatus(200);
        $createResp->assertSee('createPasswordIcon');

        // 3. User edit view has password visibility toggle
        $editResp = $this->actingAs($owner)->get("/admin/users/{$owner->id}/edit");
        $editResp->assertStatus(200);
        $editResp->assertSee('editPasswordIcon');

        // 4. Session lifetime can be configured up to 1 week (10080 minutes)
        $this->actingAs($owner)->post('/admin/settings', [
            'session_lifetime' => '10080',
            'session_expire_on_close' => '0',
            'max_login_attempts' => '5',
        ]);

        $this->assertEquals('10080', \App\Models\Setting::get('session_lifetime'));
        $this->assertEquals('0', \App\Models\Setting::get('session_expire_on_close'));
    }
}

