@extends('layouts.admin')

@section('title', "Edit {$category->name}")
@section('page_title', 'Edit Category')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-[#202925]">Edit Category: {{ $category->name }}</h1>
        <a href="{{ route('admin.categories.index') }}" class="btn-secondary text-xs py-2 px-3">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Back
        </a>
    </div>

    <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data" class="card-surface p-6 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-xs font-semibold text-[#202925] mb-1">Category Name *</label>
            <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="w-full input-custom text-sm">
        </div>

        <div>
            <label class="block text-xs font-semibold text-[#202925] mb-1">Description</label>
            <textarea name="description" rows="3" class="w-full input-custom text-sm">{{ old('description', $category->description) }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-[#202925] mb-1">Display Order</label>
                <input type="number" name="display_order" value="{{ old('display_order', $category->display_order) }}" class="w-full input-custom text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#202925] mb-1">Replace Image</label>
                <input type="file" name="image" accept="image/*" class="w-full text-xs file:mr-2 file:py-1.5 file:px-3 file:rounded file:border-0 file:bg-brand/10 file:text-brand font-medium">
            </div>
        </div>

        <div class="pt-2 flex items-center gap-6">
            <label class="flex items-center gap-2 text-xs font-medium text-[#202925] cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ $category->is_active ? 'checked' : '' }} class="rounded text-brand focus:ring-brand"> Active in Catalog
            </label>
            <label class="flex items-center gap-2 text-xs font-medium text-[#202925] cursor-pointer">
                <input type="checkbox" name="is_featured" value="1" {{ $category->is_featured ? 'checked' : '' }} class="rounded text-brand focus:ring-brand"> Feature on Homepage
            </label>
        </div>

        <div class="pt-4 flex justify-end gap-3">
            <a href="{{ route('admin.categories.index') }}" class="btn-secondary text-xs py-2 px-4">Cancel</a>
            <button type="submit" class="btn-brand text-xs py-2 px-6">Update Category</button>
        </div>
    </form>
</div>
@endsection
