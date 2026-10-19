<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\FinancialTransaction;
use App\Models\Product;
use App\Models\ProductShade;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->input('q', ''));

        if (empty($q)) {
            return redirect()->route('admin.dashboard');
        }

        $products = Product::where('name', 'like', "%{$q}%")
            ->orWhere('brand', 'like', "%{$q}%")
            ->orWhere('collection_name', 'like', "%{$q}%")
            ->take(5)
            ->get();

        $shades = ProductShade::with('product')
            ->where('sku', 'like', "%{$q}%")
            ->orWhere('shade_name', 'like', "%{$q}%")
            ->take(5)
            ->get();

        $enquiries = Enquiry::where('enquiry_number', 'like', "%{$q}%")
            ->orWhere('name', 'like', "%{$q}%")
            ->orWhere('phone', 'like', "%{$q}%")
            ->take(5)
            ->get();

        $transactions = FinancialTransaction::where('description', 'like', "%{$q}%")
            ->orWhere('reference_number', 'like', "%{$q}%")
            ->latest('transaction_date')
            ->take(5)
            ->get();

        return view('admin.search.index', compact(
            'q', 'products', 'shades', 'enquiries', 'transactions'
        ));
    }
}
