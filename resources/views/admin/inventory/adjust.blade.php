@extends('layouts.admin')

@section('title', 'Record Inventory Stock Adjustment')
@section('page_title', 'Adjust Inventory')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#202925]">Record Stock Adjustment</h1>
            <p class="text-xs text-[#6B756F]">Correct inventory discrepancies, report transit damage, or log audit findings.</p>
        </div>
        <a href="{{ route('admin.inventory.index') }}" class="btn-secondary text-xs py-2 px-3">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Back
        </a>
    </div>

    <form action="{{ route('admin.inventory.storeAdjust') }}" method="POST" class="card-surface p-6 space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-[#202925] mb-1">Product Shade (SKU) *</label>
            <select name="product_shade_id" required class="w-full input-custom text-sm">
                <option value="">Select Shade / SKU to Adjust</option>
                @foreach($shades as $s)
                    <option value="{{ $s->id }}" {{ request('shade_id') == $s->id ? 'selected' : '' }}>
                        {{ $s->sku }} — {{ $s->product->name }} ({{ $s->shade_name }}) [Current: {{ number_format($s->stock_quantity, 1) }} {{ $s->unit }}]
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-[#202925] mb-1">Adjustment Type *</label>
                <select name="type" required class="w-full input-custom text-sm">
                    <option value="adjustment">Manual Correction</option>
                    <option value="audit">Physical Count / Audit</option>
                    <option value="damage">Damaged / Showroom Wastage</option>
                    <option value="opening">Opening Balance Correction</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#202925] mb-1">Direction *</label>
                <select name="direction" required class="w-full input-custom text-sm">
                    <option value="in">Add Stock (+ IN)</option>
                    <option value="out">Deduct Stock (- OUT)</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-[#202925] mb-1">Adjustment Quantity *</label>
            <input type="number" step="0.01" name="quantity" required placeholder="e.g. 50" class="w-full input-custom text-sm font-bold">
        </div>

        <div>
            <label class="block text-xs font-semibold text-[#202925] mb-1">Reason for Adjustment *</label>
            <input type="text" name="reason" required placeholder="e.g. Quarterly physical cycle count variance / water leakage damage" class="w-full input-custom text-sm">
        </div>

        <div>
            <label class="block text-xs font-semibold text-[#202925] mb-1">Additional Internal Notes</label>
            <textarea name="notes" rows="2" placeholder="Inspection findings or supervisor authorization notes..." class="w-full input-custom text-sm"></textarea>
        </div>

        <div class="pt-4 flex justify-end gap-3">
            <a href="{{ route('admin.inventory.index') }}" class="btn-secondary text-xs py-2 px-4">Cancel</a>
            <button type="submit" class="btn-brand text-xs py-2 px-6">Commit Stock Movement</button>
        </div>
    </form>
</div>
@endsection
