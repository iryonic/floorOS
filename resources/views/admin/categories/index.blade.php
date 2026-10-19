@extends('layouts.admin')

@section('title', 'Category Management')
@section('page_title', 'Flooring Categories')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#202925]"> Categories</h1>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn-brand text-xs py-2 px-4 shadow-xs">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Category
        </a>
    </div>

    <div class="card-surface overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left whitespace-nowrap">
                <thead class="bg-white text-slate-700 border-b-2 border-slate-200 uppercase text-[10px] tracking-wider font-bold">
                    <tr>
                        <th class="p-3.5 pl-5">Category Name</th>
                        <th class="p-3.5">Slug</th>
                        <th class="p-3.5 text-center">Order</th>
                        <th class="p-3.5 text-center">Products</th>
                        <th class="p-3.5 text-center">Status</th>
                        <th class="p-3.5 pr-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach($categories as $category)
                        <tr class="hover:bg-brand-light/30 transition">
                        <td class="p-3.5 pl-5 font-bold text-[#202925]">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-8 rounded bg-white overflow-hidden border border-slate-200">
                                    <img src="{{ $category->image ?: '/images/flooring/category-wood.svg' }}" alt="" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <span>{{ $category->name }}</span>
                                    <span class="block text-[11px] text-[#6B756F] font-normal truncate max-w-xs">{{ $category->description }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="p-3.5 font-mono text-neutral-500">{{ $category->slug }}</td>
                        <td class="p-3.5 text-center font-bold">{{ $category->display_order }}</td>
                        <td class="p-3.5 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-brand-light text-brand">
                                {{ $category->products_count }} {{ Str::plural('item', $category->products_count) }}
                            </span>
                        </td>
                        <td class="p-3.5 text-center">
                            @if($category->is_active)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700">Active</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white text-slate-600 border border-slate-300">Hidden</span>
                            @endif
                        </td>
                        <td class="p-3.5 pr-5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('catalog', ['category' => $category->slug]) }}" target="_blank" class="p-1 text-[#6B756F] hover:text-brand" title="View on Customer Catalog">
                                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                </a>
                                <a href="{{ route('admin.categories.edit', $category) }}" class="p-1 text-[#6B756F] hover:text-brand">
                                    <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                                </a>
                                @if($category->products_count === 0)
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" data-confirm="Delete this category?" data-confirm-title="Delete Category" data-confirm-destructive="true">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-[#6B756F] hover:text-rose-600">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection
