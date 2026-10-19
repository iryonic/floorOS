<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Enquiry;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductShade;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\User;
use App\Services\AccountingService;
use App\Services\InventoryService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $accounting = new AccountingService();
        $accounting->initializeChartOfAccounts();

        $owner = User::where('role', 'owner')->first();
        $salesUser = User::where('role', 'sales')->first();

        $c1 = Customer::where('phone', '+91 98860 12345')->first(); // Rajesh
        $c2 = Customer::where('phone', '+91 98441 55667')->first(); // Studio Arc
        $c3 = Customer::where('phone', '+91 99000 88776')->first(); // Prestige Living

        $s1 = Supplier::where('name', 'like', '%Mikasa%')->first();
        $s2 = Supplier::where('name', 'like', '%ShieldCore%')->first();

        $shadeOakNat = ProductShade::where('sku', 'ROH-NAT-01')->first();
        $shadeOakHoney = ProductShade::where('sku', 'ROH-HNY-02')->first();
        $shadeSpcCal = ProductShade::where('sku', 'SPC-CAL-01')->first();
        $shadeLamBav = ProductShade::where('sku', 'LAM-BAV-01')->first();

        // 1. Confirmed Purchase from Mikasa (Restocking Royal Oak Heritage)
        $pur1 = Purchase::firstOrCreate(
            ['purchase_number' => 'PUR-202610-001'],
            [
                'supplier_id' => $s1->id,
                'supplier_invoice_number' => 'MIK-BLR-8921',
                'purchase_date' => now()->subDays(20)->toDateString(),
                'subtotal' => 160000.00,
                'discount_amount' => 0.00,
                'tax_rate' => 18.00,
                'tax_amount' => 28800.00,
                'other_charges' => 2200.00,
                'total_amount' => 191000.00,
                'paid_amount' => 100000.00,
                'due_amount' => 91000.00,
                'payment_status' => 'partially_paid',
                'status' => 'received',
                'payment_method' => 'bank_transfer',
                'notes' => 'Bulk shipment of 500 sq ft Royal Oak Natural',
                'created_by' => $owner->id,
            ]
        );

        PurchaseItem::firstOrCreate(
            ['purchase_id' => $pur1->id, 'product_shade_id' => $shadeOakNat->id],
            [
                'product_id' => $shadeOakNat->product_id,
                'description' => 'Royal Oak Heritage - Natural Oak (500 sq ft)',
                'sku' => $shadeOakNat->sku,
                'quantity' => 500.00,
                'unit' => 'sq ft',
                'unit_cost' => 320.00,
                'tax_amount' => 28800.00,
                'total' => 188800.00,
            ]
        );

        SupplierPayment::firstOrCreate(
            ['payment_number' => 'SPAY-202610-001'],
            [
                'supplier_id' => $s1->id,
                'purchase_id' => $pur1->id,
                'payment_date' => now()->subDays(18)->toDateString(),
                'amount' => 100000.00,
                'payment_method' => 'bank_transfer',
                'reference_number' => 'HDFC-NEFT-99128301',
                'notes' => 'Advance payment upon container unloading',
                'created_by' => $owner->id,
            ]
        );

        $accounting->postPurchase($pur1);
        $s1->recalculateBalance();

        // 2. Confirmed Sale 1 (Rajesh Sharma - Full Paid in Cash & UPI)
        $sale1 = Sale::firstOrCreate(
            ['invoice_number' => 'INV-202610-001'],
            [
                'customer_id' => $c1->id,
                'date' => now()->subDays(12)->toDateString(),
                'due_date' => now()->subDays(12)->toDateString(),
                'subtotal' => 174600.00,
                'discount_type' => 'fixed',
                'discount_rate' => 0,
                'discount_amount' => 4600.00,
                'tax_rate' => 18.00,
                'tax_amount' => 30600.00,
                'total_amount' => 200600.00,
                'paid_amount' => 200600.00,
                'due_amount' => 0.00,
                'payment_status' => 'paid',
                'status' => 'confirmed',
                'payment_method' => 'split',
                'notes' => 'Whitefield Villa living room & master bedroom. Client took delivery directly.',
                'created_by' => $salesUser->id,
            ]
        );

        SaleItem::firstOrCreate(
            ['sale_id' => $sale1->id, 'product_shade_id' => $shadeOakNat->id],
            [
                'product_id' => $shadeOakNat->product_id,
                'description' => 'Royal Oak Heritage - Natural Oak',
                'sku' => $shadeOakNat->sku,
                'quantity' => 360.00,
                'unit' => 'sq ft',
                'unit_price' => 485.00,
                'cost_price_at_sale' => 320.00,
                'discount_amount' => 4600.00,
                'tax_amount' => 30600.00,
                'total' => 200600.00,
            ]
        );

        Payment::firstOrCreate(
            ['receipt_number' => 'RCP-202610-001'],
            [
                'customer_id' => $c1->id,
                'sale_id' => $sale1->id,
                'payment_date' => now()->subDays(12)->toDateString(),
                'amount' => 50600.00,
                'payment_method' => 'cash',
                'reference_number' => 'CASH-REC-01',
                'notes' => 'Advance booking cash',
                'created_by' => $salesUser->id,
            ]
        );

        Payment::firstOrCreate(
            ['receipt_number' => 'RCP-202610-002'],
            [
                'customer_id' => $c1->id,
                'sale_id' => $sale1->id,
                'payment_date' => now()->subDays(12)->toDateString(),
                'amount' => 150000.00,
                'payment_method' => 'upi',
                'reference_number' => 'UPI-ICICI-88392019',
                'notes' => 'Final payment via Google Pay UPI',
                'created_by' => $salesUser->id,
            ]
        );

        StockMovement::firstOrCreate(
            ['reference_number' => 'MOV-SALE-INV-202610-001'],
            [
                'product_shade_id' => $shadeOakNat->id,
                'sku' => $shadeOakNat->sku,
                'type' => 'sale',
                'direction' => 'out',
                'quantity' => 360.00,
                'unit' => 'sq ft',
                'unit_cost' => 320.00,
                'previous_stock' => 1610.00,
                'new_stock' => 1250.00,
                'related_type' => Sale::class,
                'related_id' => $sale1->id,
                'reason' => 'Fulfilled for invoice #INV-202610-001',
                'user_id' => $salesUser->id,
                'occurred_at' => now()->subDays(12),
            ]
        );

        $accounting->postSale($sale1);
        $c1->recalculateBalance();

        // 3. Confirmed Sale 2 (Studio Arc - Partially Paid on Credit)
        $sale2 = Sale::firstOrCreate(
            ['invoice_number' => 'INV-202610-002'],
            [
                'customer_id' => $c2->id,
                'date' => now()->subDays(5)->toDateString(),
                'due_date' => now()->addDays(10)->toDateString(),
                'subtotal' => 129500.00,
                'discount_type' => 'fixed',
                'discount_rate' => 0,
                'discount_amount' => 4500.00,
                'tax_rate' => 18.00,
                'tax_amount' => 22500.00,
                'total_amount' => 147500.00,
                'paid_amount' => 60000.00,
                'due_amount' => 87500.00,
                'payment_status' => 'partially_paid',
                'status' => 'confirmed',
                'payment_method' => 'bank_transfer',
                'notes' => 'Indiranagar commercial studio renovation. Client requested 15-day payment credit.',
                'created_by' => $salesUser->id,
            ]
        );

        SaleItem::firstOrCreate(
            ['sale_id' => $sale2->id, 'product_shade_id' => $shadeSpcCal->id],
            [
                'product_id' => $shadeSpcCal->product_id,
                'description' => 'AquaShield Pro Stone-Core SPC - Calacatta Marble (700 sq ft)',
                'sku' => $shadeSpcCal->sku,
                'quantity' => 700.00,
                'unit' => 'sq ft',
                'unit_price' => 185.00,
                'cost_price_at_sale' => 110.00,
                'discount_amount' => 4500.00,
                'tax_amount' => 22500.00,
                'total' => 147500.00,
            ]
        );

        Payment::firstOrCreate(
            ['receipt_number' => 'RCP-202610-003'],
            [
                'customer_id' => $c2->id,
                'sale_id' => $sale2->id,
                'payment_date' => now()->subDays(5)->toDateString(),
                'amount' => 60000.00,
                'payment_method' => 'bank_transfer',
                'reference_number' => 'AXIS-IMPS-77665544',
                'notes' => 'Part payment on order dispatch',
                'created_by' => $salesUser->id,
            ]
        );

        StockMovement::firstOrCreate(
            ['reference_number' => 'MOV-SALE-INV-202610-002'],
            [
                'product_shade_id' => $shadeSpcCal->id,
                'sku' => $shadeSpcCal->sku,
                'type' => 'sale',
                'direction' => 'out',
                'quantity' => 700.00,
                'unit' => 'sq ft',
                'unit_cost' => 110.00,
                'previous_stock' => 3100.00,
                'new_stock' => 2400.00,
                'related_type' => Sale::class,
                'related_id' => $sale2->id,
                'reason' => 'Fulfilled for invoice #INV-202610-002',
                'user_id' => $salesUser->id,
                'occurred_at' => now()->subDays(5),
            ]
        );

        $accounting->postSale($sale2);
        $c2->recalculateBalance();

        // 4. Sale 3 (Prestige Living - Today's sale, pending payment)
        $sale3 = Sale::firstOrCreate(
            ['invoice_number' => 'INV-202610-003'],
            [
                'customer_id' => $c3->id,
                'date' => now()->toDateString(),
                'due_date' => now()->addDays(7)->toDateString(),
                'subtotal' => 50000.00,
                'discount_type' => 'fixed',
                'discount_rate' => 0,
                'discount_amount' => 0.00,
                'tax_rate' => 18.00,
                'tax_amount' => 9000.00,
                'total_amount' => 59000.00,
                'paid_amount' => 0.00,
                'due_amount' => 59000.00,
                'payment_status' => 'unpaid',
                'status' => 'confirmed',
                'payment_method' => 'cheque',
                'notes' => 'Koramangala project clubhouse foyer flooring sample batch.',
                'created_by' => $salesUser->id,
            ]
        );

        SaleItem::firstOrCreate(
            ['sale_id' => $sale3->id, 'product_shade_id' => $shadeLamBav->id],
            [
                'product_id' => $shadeLamBav->product_id,
                'description' => 'Titan Commercial AC5 Laminate - Bavarian Oak (400 sq ft)',
                'sku' => $shadeLamBav->sku,
                'quantity' => 400.00,
                'unit' => 'sq ft',
                'unit_price' => 125.00,
                'cost_price_at_sale' => 70.00,
                'discount_amount' => 0.00,
                'tax_amount' => 9000.00,
                'total' => 59000.00,
            ]
        );

        $accounting->postSale($sale3);
        $c3->recalculateBalance();

        // 5. Quotation for Architect Meera Nambiar
        $c4 = Customer::where('phone', '+91 97312 99881')->first();
        $q1 = Quotation::firstOrCreate(
            ['quotation_number' => 'EST-202610-001'],
            [
                'customer_id' => $c4->id,
                'date' => now()->subDays(2)->toDateString(),
                'valid_until' => now()->addDays(20)->toDateString(),
                'subtotal' => 372600.00,
                'discount_type' => 'percent',
                'discount_rate' => 5.00,
                'discount_amount' => 18630.00,
                'tax_rate' => 18.00,
                'tax_amount' => 63714.60,
                'total_amount' => 417684.60,
                'status' => 'sent',
                'notes' => 'Quotation for Lavelle Road luxury bungalow ground floor & master bedroom.',
                'terms' => 'Price valid for 20 days. 50% advance upon order confirmation, balance on delivery.',
                'created_by' => $salesUser->id,
            ]
        );

        QuotationItem::firstOrCreate(
            ['quotation_id' => $q1->id, 'sku' => 'CHV-CHM-01'],
            [
                'product_id' => Product::where('slug', 'grandeur-french-chevron-parquet')->value('id'),
                'product_shade_id' => ProductShade::where('sku', 'CHV-CHM-01')->value('id'),
                'description' => 'Grandeur French Chevron Parquet - Champagne Oak',
                'sku' => 'CHV-CHM-01',
                'quantity' => 540.00,
                'unit' => 'sq ft',
                'unit_price' => 690.00,
                'discount_amount' => 18630.00,
                'total' => 353970.00,
            ]
        );

        // 6. Showroom Operational Expenses
        $rentCat = ExpenseCategory::where('name', 'like', '%Rent%')->first();
        $utilCat = ExpenseCategory::where('name', 'like', '%Electricity%')->first();
        $freightCat = ExpenseCategory::where('name', 'like', '%Freight%')->first();

        $exp1 = Expense::firstOrCreate(
            ['reference_number' => 'EXP-202610-001'],
            [
                'expense_category_id' => $rentCat->id,
                'date' => now()->startOfMonth()->toDateString(),
                'title' => 'Monthly Gallery Lease Rent - October 2026',
                'description' => 'Showroom premises rent paid to Indiranagar Commercial Realty',
                'amount' => 75000.00,
                'payment_method' => 'bank_transfer',
                'payee' => 'Indiranagar Commercial Properties',
                'created_by' => $owner->id,
            ]
        );
        $accounting->postExpense($exp1);

        $exp2 = Expense::firstOrCreate(
            ['reference_number' => 'EXP-202610-002'],
            [
                'expense_category_id' => $utilCat->id,
                'date' => now()->subDays(8)->toDateString(),
                'title' => 'BESCOM Showroom Power & High-CRI Lighting Bill',
                'description' => 'Electricity charges for gallery spotlighting & display tracks',
                'amount' => 14250.00,
                'payment_method' => 'upi',
                'payee' => 'BESCOM Bangalore',
                'created_by' => $owner->id,
            ]
        );
        $accounting->postExpense($exp2);

        $exp3 = Expense::firstOrCreate(
            ['reference_number' => 'EXP-202610-003'],
            [
                'expense_category_id' => $freightCat->id,
                'date' => now()->subDays(4)->toDateString(),
                'title' => 'Local Freight & Unloading Haulage',
                'description' => 'Tata Ace tempo delivery of 360 sq ft hardwood to Whitefield Villa',
                'amount' => 3800.00,
                'payment_method' => 'cash',
                'payee' => 'Kaveri City Logistics',
                'created_by' => $salesUser->id,
            ]
        );
        $accounting->postExpense($exp3);

        // 7. Customer Enquiries (from Portal B)
        $enquiries = [
            [
                'enquiry_number' => 'ENQ-202610-001',
                'name' => 'Dr. Vikramaditya Rao',
                'phone' => '+91 98200 44332',
                'email' => 'dr.vikram@apollo.org',
                'product_id' => Product::where('slug', 'royal-oak-heritage-engineered-hardwood')->value('id'),
                'product_shade_id' => $shadeOakHoney->id,
                'area_sqft' => 1200.00,
                'preferred_contact' => 'whatsapp',
                'message' => 'Looking for Smoked Honey oak flooring for our penthouse living and family lounge in Sadashivanagar. Can we visit the showroom this Saturday for tactile samples?',
                'status' => 'new',
                'internal_notes' => 'High priority inquiry. Saturday afternoon appointment requested.',
            ],
            [
                'enquiry_number' => 'ENQ-202610-002',
                'name' => 'Pooja Hegde (Interior Consultant)',
                'phone' => '+91 97400 11998',
                'email' => 'pooja.interiors@gmail.com',
                'product_id' => Product::where('slug', 'aquashield-pro-waterproof-spc-flooring')->value('id'),
                'product_shade_id' => $shadeSpcCal->id,
                'area_sqft' => 850.00,
                'preferred_contact' => 'call',
                'message' => 'Need pricing and box coverage for Calacatta SPC flooring for a modern dental clinic. Does it support heavy dental chair load without denting?',
                'status' => 'contacted',
                'internal_notes' => 'Called client. Confirmed SPC stone core withstands 500kg point loads. Sample swatch kit sent via courier.',
            ],
            [
                'enquiry_number' => 'ENQ-202610-003',
                'name' => 'Sanjay Agarwal',
                'phone' => '+91 99801 77665',
                'email' => 'sanjay@agarwalproperties.in',
                'product_id' => Product::where('slug', 'grandeur-french-chevron-parquet')->value('id'),
                'product_shade_id' => ProductShade::where('sku', 'CHV-CHM-01')->value('id'),
                'area_sqft' => 2200.00,
                'preferred_contact' => 'whatsapp',
                'message' => 'Inquiring for a heritage villa renovation in Mysore. Interested in French Chevron Champagne Oak. Please provide installation quote with moisture underlay.',
                'status' => 'quotation_sent',
                'internal_notes' => 'Quotation prepared and shared over WhatsApp. Follow-up scheduled for Tuesday.',
            ],
        ];

        foreach ($enquiries as $eq) {
            Enquiry::updateOrCreate(['enquiry_number' => $eq['enquiry_number']], $eq);
        }
    }
}
