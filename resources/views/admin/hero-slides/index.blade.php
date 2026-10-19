@extends('layouts.admin')

@section('title', 'Hero Banner Slides Management')
@section('page_title', 'Hero Sliding Banners')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#202925]">Hero Sliding Banners</h1>
            
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('home') }}" target="_blank" class="btn-secondary text-xs py-2 px-3">
                <i data-lucide="eye" class="w-3.5 h-3.5 text-brand"></i>
                <span>Preview Storefront</span>
            </a>
            <button type="button" onclick="openAddSlideModal()" class="btn-brand text-xs py-2 px-4 shadow-sm">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>Add New Slide</span>
            </button>
        </div>
    </div>

    <!-- Recommended Resolution Guidelines Card -->
    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs text-slate-600">
        <div class="flex items-start gap-2.5">
            <i data-lucide="info" class="w-4 h-4 text-brand shrink-0 mt-0.5"></i>
            <div class="space-y-1">
                <span class="font-bold text-slate-800">Responsive Resolution Standards:</span>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-[11px] text-slate-500">
                    <span><strong class="text-slate-700">Desktop (&ge;992px):</strong> 1350×620 px (~2.18:1)</span>
                    <span><strong class="text-slate-700">Tablet (577–991px):</strong> 1024×640 px (16:10)</span>
                    <span><strong class="text-slate-700">Mobile (&le;576px):</strong> 768×768 px (1:1 Square)</span>
                </div>
            </div>
        </div>
        <span class="text-[11px] font-medium text-brand bg-white px-2.5 py-1 rounded-md border border-slate-200/80 shadow-2xs self-start md:self-auto shrink-0">
            Drag rows to re-order live
        </span>
    </div>

    <!-- Slides List Table with Draggable Rows -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200 text-slate-500 font-bold uppercase text-[10px] tracking-wider">
                        <th class="py-3 px-3 w-10 text-center">Move</th>
                        <th class="py-3 px-3 w-12 text-center">Seq</th>
                        <th class="py-3 px-4">Desktop Banner</th>
                        <th class="py-3 px-3 text-center">Tablet</th>
                        <th class="py-3 px-3 text-center">Mobile</th>
                        <th class="py-3 px-4">Alt / Description</th>
                        <th class="py-3 px-4">Destination Link</th>
                        <th class="py-3 px-3 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="sortableSlidesList" class="divide-y divide-slate-100">
                    @forelse($slides as $slide)
                        <tr class="slide-row hover:bg-slate-50/60 transition group" 
                            data-id="{{ $slide->id }}"
                            id="slide-row-{{ $slide->id }}">
                            
                            <!-- Drag Handle (Only this cell initiates drag) -->
                            <td class="drag-handle py-3 px-3 text-center text-slate-400 group-hover:text-brand cursor-grab active:cursor-grabbing select-none" 
                                draggable="true" 
                                title="Drag to reorder sequence">
                                <i data-lucide="grip-vertical" class="w-4 h-4 mx-auto pointer-events-none"></i>
                            </td>

                            <!-- Sort Sequence Badge -->
                            <td class="py-3 px-3 text-center">
                                <span class="sort-badge inline-flex items-center justify-center w-6 h-6 rounded-md bg-slate-100 text-slate-700 font-bold font-mono text-[11px]">
                                    {{ $slide->sort_order }}
                                </span>
                            </td>

                            <!-- Desktop Thumbnail (Primary) -->
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-24 h-12 rounded-lg overflow-hidden border border-slate-200 bg-slate-100 shrink-0 shadow-2xs">
                                        <img src="{{ $slide->desktop_image }}" alt="{{ $slide->alt_text }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="min-w-0 max-w-[200px]">
                                        <span class="font-bold text-slate-900 block truncate" title="{{ $slide->alt_text ?: 'Banner #' . $slide->id }}">
                                            {{ $slide->alt_text ?: 'Banner #' . $slide->id }}
                                        </span>
                                        <span class="text-[10px] text-slate-400 block truncate font-mono">
                                            {{ $slide->desktop_image }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Tablet Thumbnail -->
                            <td class="py-3 px-3 text-center">
                                @if($slide->tablet_image)
                                    <div class="w-10 h-7 rounded border border-slate-200 overflow-hidden mx-auto bg-slate-50" title="Tablet banner configured">
                                        <img src="{{ $slide->tablet_image }}" alt="Tablet" class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <span class="text-[10px] text-slate-400 font-medium">Auto Desktop</span>
                                @endif
                            </td>

                            <!-- Mobile Thumbnail -->
                            <td class="py-3 px-3 text-center">
                                @if($slide->mobile_image)
                                    <div class="w-8 h-8 rounded border border-slate-200 overflow-hidden mx-auto bg-slate-50" title="Mobile 1:1 square banner configured">
                                        <img src="{{ $slide->mobile_image }}" alt="Mobile" class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <span class="text-[10px] text-slate-400 font-medium">Auto Desktop</span>
                                @endif
                            </td>

                            <!-- Alt Text -->
                            <td class="py-3 px-4 text-slate-600">
                                <span class="truncate block max-w-[180px]" title="{{ $slide->alt_text }}">
                                    {{ $slide->alt_text ?: '—' }}
                                </span>
                            </td>

                            <!-- Destination Link -->
                            <td class="py-3 px-4">
                                @if($slide->link_url)
                                    <a href="{{ $slide->link_url }}" target="_blank" class="inline-flex items-center gap-1 text-brand font-semibold hover:underline max-w-[160px] truncate" title="{{ $slide->link_url }}">
                                        <i data-lucide="external-link" class="w-3 h-3 shrink-0"></i>
                                        <span class="truncate">{{ $slide->link_url }}</span>
                                    </a>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">None (Display Only)</span>
                                @endif
                            </td>

                            <!-- Status Badge -->
                            <td class="py-3 px-3 text-center">
                                @if($slide->status === 'active')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Draft
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" 
                                            onclick='openEditSlideModal(@json($slide))' 
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-brand hover:bg-slate-100 transition" 
                                            title="Edit Slide">
                                        <i data-lucide="edit-3" class="w-4 h-4 pointer-events-none"></i>
                                    </button>

                                    <button type="button" 
                                            onclick="deleteSlide({{ $slide->id }})" 
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" 
                                            title="Delete Slide">
                                        <i data-lucide="trash-2" class="w-4 h-4 pointer-events-none"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400">
                                <i data-lucide="gallery-horizontal" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
                                <p class="font-bold text-slate-700 text-sm">No Hero Slides Configured</p>
                                <p class="text-xs text-slate-400 mt-0.5">Click "Add New Slide" above to create your first responsive sliding banner.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Standalone Global Form for Delete Action (Prevents inline form submission failures) -->
<form id="globalDeleteSlideForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<!-- ======================================================== -->
<!-- Add / Edit Hero Slide Modal (Matches Attached Design)     -->
<!-- ======================================================== -->
<div id="slideFormModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-xl w-full p-6 sm:p-7 relative border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
        
        <!-- Modal Close Button -->
        <button type="button" onclick="closeSlideModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-700 p-1.5 rounded-xl hover:bg-slate-50 transition">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>

        <!-- Modal Header -->
        <div class="mb-5">
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-brand-light text-brand text-[10px] font-bold uppercase tracking-wider mb-1.5">
                <i data-lucide="gallery-horizontal" class="w-3 h-3"></i>
                <span id="modalEyebrow">Hero Slide Configuration</span>
            </div>
            <h3 id="modalTitle" class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Add New Hero Banner</h3>
            <p class="text-xs text-slate-500 mt-0.5">Configure desktop, tablet, and mobile banner imagery with links.</p>
        </div>

        <form id="slideModalForm" action="{{ route('admin.hero-slides.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div id="methodContainer"></div>

            <!-- 1. Desktop Banner (1350x620 px recommended) * -->
            <div>
                <label class="block text-xs font-semibold text-slate-800 mb-1">
                    Desktop Banner (1350×620 px recommended) <span class="text-rose-500">*</span>
                </label>
                <div class="flex items-center gap-2">
                    <div class="relative flex-1">
                        <input type="text" 
                               id="desktopBannerInput" 
                               name="desktop_banner" 
                               required 
                               placeholder="assets/images/hero/banner-desktop.png" 
                               class="w-full input-custom text-xs py-2.5 bg-white">
                    </div>
                    <label class="btn-secondary text-xs py-2 px-3.5 cursor-pointer shrink-0 font-semibold flex items-center gap-1.5 text-slate-700 hover:text-brand">
                        <i data-lucide="upload" class="w-4 h-4"></i>
                        <span>Browse</span>
                        <input type="file" 
                               id="desktopBannerFileInput" 
                               name="desktop_banner_file" 
                               accept="image/*" 
                               class="hidden" 
                               onchange="handleImageFileSelect(this, 'desktopBannerInput', 'desktopPreview')">
                    </label>
                </div>
                <!-- Preview Thumbnail -->
                <div id="desktopPreviewContainer" class="mt-2 hidden">
                    <img id="desktopPreview" src="" alt="Desktop Preview" class="h-16 rounded-lg border border-slate-200 object-cover shadow-2xs">
                </div>
            </div>

            <!-- 2. Tablet Banner (1024x640 px / Optional) -->
            <div>
                <label class="block text-xs font-semibold text-slate-800 mb-1">
                    Tablet Banner (1024×640 px / Optional)
                </label>
                <div class="flex items-center gap-2">
                    <div class="relative flex-1">
                        <input type="text" 
                               id="tabletBannerInput" 
                               name="tablet_banner" 
                               placeholder="assets/images/hero/banner-tablet.png" 
                               class="w-full input-custom text-xs py-2.5 bg-white">
                    </div>
                    <label class="btn-secondary text-xs py-2 px-3.5 cursor-pointer shrink-0 font-semibold flex items-center gap-1.5 text-slate-700 hover:text-brand">
                        <i data-lucide="upload" class="w-4 h-4"></i>
                        <span>Browse</span>
                        <input type="file" 
                               id="tabletBannerFileInput" 
                               name="tablet_banner_file" 
                               accept="image/*" 
                               class="hidden" 
                               onchange="handleImageFileSelect(this, 'tabletBannerInput', 'tabletPreview')">
                    </label>
                </div>
                <!-- Preview Thumbnail -->
                <div id="tabletPreviewContainer" class="mt-2 hidden">
                    <img id="tabletPreview" src="" alt="Tablet Preview" class="h-14 rounded-lg border border-slate-200 object-cover shadow-2xs">
                </div>
            </div>

            <!-- 3. Mobile Banner (768x768 px Square / Optional) -->
            <div>
                <label class="block text-xs font-semibold text-slate-800 mb-1">
                    Mobile Banner (768×768 px Square / Optional)
                </label>
                <div class="flex items-center gap-2">
                    <div class="relative flex-1">
                        <input type="text" 
                               id="mobileBannerInput" 
                               name="mobile_banner" 
                               placeholder="assets/images/hero/banner-mobile.png" 
                               class="w-full input-custom text-xs py-2.5 bg-white">
                    </div>
                    <label class="btn-secondary text-xs py-2 px-3.5 cursor-pointer shrink-0 font-semibold flex items-center gap-1.5 text-slate-700 hover:text-brand">
                        <i data-lucide="upload" class="w-4 h-4"></i>
                        <span>Browse</span>
                        <input type="file" 
                               id="mobileBannerFileInput" 
                               name="mobile_banner_file" 
                               accept="image/*" 
                               class="hidden" 
                               onchange="handleImageFileSelect(this, 'mobileBannerInput', 'mobilePreview')">
                    </label>
                </div>
                <!-- Preview Thumbnail -->
                <div id="mobilePreviewContainer" class="mt-2 hidden">
                    <img id="mobilePreview" src="" alt="Mobile Preview" class="h-14 rounded-lg border border-slate-200 object-cover shadow-2xs">
                </div>
            </div>

            <!-- 4. Click Action / Link Destination -->
            <div>
                <label class="block text-xs font-semibold text-slate-800 mb-1">Click Action / Link Destination</label>
                <select id="linkDestinationSelect" name="link_destination" onchange="toggleCustomUrlInput()" class="w-full input-custom text-xs py-2.5 bg-white font-medium">
                    <option value="none">(None - Display Only)</option>
                    <option value="/catalog">Catalog — All Flooring Collections (/catalog)</option>
                    @foreach($categories as $cat)
                        <option value="/catalog?category={{ $cat->slug }}">Category — {{ $cat->name }} (/catalog?category={{ $cat->slug }})</option>
                    @endforeach
                    <option value="/contact">Showroom & Atelier Consultation (/contact)</option>
                    <option value="custom">Custom External / Internal URL</option>
                </select>

                <!-- Custom URL input (conditionally revealed) -->
                <div id="customUrlContainer" class="mt-2 hidden">
                    <input type="text" id="customUrlInput" name="custom_link_url" placeholder="https://... or /custom-page" class="w-full input-custom text-xs py-2 bg-white">
                </div>
            </div>

            <!-- 5. Alt Text & Image Description -->
            <div>
                <label class="block text-xs font-semibold text-slate-800 mb-1">Alt Text & Image Description</label>
                <input type="text" 
                       id="altTextInput" 
                       name="alt_text" 
                       placeholder="e.g. Kashmiri Almonds Harvest Banner" 
                       class="w-full input-custom text-xs py-2.5 bg-white">
            </div>

            <!-- 6 & 7: Sort Sequence and Status -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-800 mb-1">
                        Sort Sequence <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" 
                           id="sortOrderInput" 
                           name="sort_order" 
                           value="{{ $slides->count() + 1 }}" 
                           min="1" 
                           required 
                           class="w-full input-custom text-xs py-2.5 bg-white font-mono font-bold text-center">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-800 mb-1">
                        Status <span class="text-rose-500">*</span>
                    </label>
                    <select id="statusSelect" name="status" required class="w-full input-custom text-xs py-2.5 bg-white font-medium">
                        <option value="active">Active (Live)</option>
                        <option value="inactive">Inactive (Draft)</option>
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeSlideModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                    Cancel
                </button>
                <button type="submit" id="saveSlideBtn" class="btn-brand text-xs py-2.5 px-6 font-bold shadow-xs">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Save Slide</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Draggable Reorder & Modal Script -->
<script>
    // ----------------------------------------------------
    // Modal Management (Add & Edit)
    // ----------------------------------------------------
    function openAddSlideModal() {
        document.getElementById('slideModalForm').action = "{{ route('admin.hero-slides.store') }}";
        document.getElementById('methodContainer').innerHTML = '';
        document.getElementById('modalTitle').innerText = 'Add New Hero Banner';
        document.getElementById('modalEyebrow').innerText = 'New Banner';
        document.getElementById('saveSlideBtn').querySelector('span').innerText = 'Save Slide';

        // Clear values
        document.getElementById('desktopBannerInput').value = '';
        document.getElementById('desktopBannerFileInput').value = '';
        document.getElementById('desktopPreviewContainer').classList.add('hidden');

        document.getElementById('tabletBannerInput').value = '';
        document.getElementById('tabletBannerFileInput').value = '';
        document.getElementById('tabletPreviewContainer').classList.add('hidden');

        document.getElementById('mobileBannerInput').value = '';
        document.getElementById('mobileBannerFileInput').value = '';
        document.getElementById('mobilePreviewContainer').classList.add('hidden');

        document.getElementById('linkDestinationSelect').value = 'none';
        document.getElementById('customUrlContainer').classList.add('hidden');
        document.getElementById('customUrlInput').value = '';

        document.getElementById('altTextInput').value = '';
        document.getElementById('sortOrderInput').value = "{{ $slides->count() + 1 }}";
        document.getElementById('statusSelect').value = 'active';

        document.getElementById('slideFormModal').classList.remove('hidden');
    }

    function openEditSlideModal(slide) {
        document.getElementById('slideModalForm').action = `/admin/hero-slides/${slide.id}`;
        document.getElementById('methodContainer').innerHTML = '@method("PUT")';
        document.getElementById('modalTitle').innerText = 'Edit Hero Banner';
        document.getElementById('modalEyebrow').innerText = 'Edit Slide #' + slide.id;
        document.getElementById('saveSlideBtn').querySelector('span').innerText = 'Update Slide';

        // Fill desktop
        document.getElementById('desktopBannerInput').value = slide.desktop_image || '';
        if (slide.desktop_image) {
            document.getElementById('desktopPreview').src = slide.desktop_image;
            document.getElementById('desktopPreviewContainer').classList.remove('hidden');
        } else {
            document.getElementById('desktopPreviewContainer').classList.add('hidden');
        }

        // Fill tablet
        document.getElementById('tabletBannerInput').value = slide.tablet_image || '';
        if (slide.tablet_image) {
            document.getElementById('tabletPreview').src = slide.tablet_image;
            document.getElementById('tabletPreviewContainer').classList.remove('hidden');
        } else {
            document.getElementById('tabletPreviewContainer').classList.add('hidden');
        }

        // Fill mobile
        document.getElementById('mobileBannerInput').value = slide.mobile_image || '';
        if (slide.mobile_image) {
            document.getElementById('mobilePreview').src = slide.mobile_image;
            document.getElementById('mobilePreviewContainer').classList.remove('hidden');
        } else {
            document.getElementById('mobilePreviewContainer').classList.add('hidden');
        }

        // Link destination
        const select = document.getElementById('linkDestinationSelect');
        let matched = false;
        if (!slide.link_url || slide.link_type === 'none') {
            select.value = 'none';
        } else {
            for (let i = 0; i < select.options.length; i++) {
                if (select.options[i].value === slide.link_url) {
                    select.value = slide.link_url;
                    matched = true;
                    break;
                }
            }
            if (!matched) {
                select.value = 'custom';
                document.getElementById('customUrlInput').value = slide.link_url;
            }
        }
        toggleCustomUrlInput();

        document.getElementById('altTextInput').value = slide.alt_text || '';
        document.getElementById('sortOrderInput').value = slide.sort_order;
        document.getElementById('statusSelect').value = slide.status;

        document.getElementById('slideFormModal').classList.remove('hidden');
    }

    function closeSlideModal() {
        document.getElementById('slideFormModal').classList.add('hidden');
    }

    function toggleCustomUrlInput() {
        const val = document.getElementById('linkDestinationSelect').value;
        const container = document.getElementById('customUrlContainer');
        if (val === 'custom') {
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
    }

    function handleImageFileSelect(fileInput, textInputId, previewImgId) {
        if (fileInput.files && fileInput.files[0]) {
            const file = fileInput.files[0];
            document.getElementById(textInputId).value = file.name;

            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById(previewImgId);
                img.src = e.target.result;
                img.parentElement.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    }

    function deleteSlide(id) {
        if (!confirm('Are you sure you want to delete this hero slide banner?')) {
            return;
        }

        const deleteUrl = '/admin/hero-slides/' + id;

        // Try AJAX request first for instantaneous UI update without page reload
        fetch(deleteUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ _method: 'DELETE' })
        })
        .then(res => {
            if (res.ok) {
                return res.json();
            }
            throw new Error('HTTP ' + res.status);
        })
        .then(data => {
            const row = document.getElementById('slide-row-' + id);
            if (row) {
                row.style.transition = 'all 0.3s ease-out';
                row.style.opacity = '0';
                row.style.transform = 'translateX(20px)';
                setTimeout(() => {
                    row.remove();
                    // Recalculate remaining sequence badges
                    document.querySelectorAll('.slide-row .sort-badge').forEach((b, idx) => {
                        b.innerText = idx + 1;
                    });
                }, 300);
            }
            showToast(data.message || 'Hero banner slide deleted successfully!');
        })
        .catch(err => {
            // Direct Form Submit fallback
            const form = document.getElementById('globalDeleteSlideForm');
            form.action = deleteUrl;
            form.submit();
        });
    }

    // ----------------------------------------------------
    // Draggable Table Rows Sorting (Initiated via Drag Handle)
    // ----------------------------------------------------
    let draggedRow = null;

    const sortableList = document.getElementById('sortableSlidesList');
    if (sortableList) {
        const handles = sortableList.querySelectorAll('.drag-handle');
        const rows = sortableList.querySelectorAll('.slide-row');

        handles.forEach(handle => {
            handle.addEventListener('dragstart', (e) => {
                draggedRow = handle.closest('.slide-row');
                if (draggedRow) {
                    draggedRow.classList.add('opacity-40', 'bg-brand-light/20');
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/plain', draggedRow.getAttribute('data-id'));
                }
            });

            handle.addEventListener('dragend', () => {
                if (draggedRow) {
                    draggedRow.classList.remove('opacity-40', 'bg-brand-light/20');
                }
                rows.forEach(r => r.classList.remove('border-t-2', 'border-brand'));
            });
        });

        rows.forEach(row => {
            row.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                row.classList.add('border-t-2', 'border-brand');
            });

            row.addEventListener('dragleave', () => {
                row.classList.remove('border-t-2', 'border-brand');
            });

            row.addEventListener('drop', (e) => {
                e.preventDefault();
                row.classList.remove('border-t-2', 'border-brand');
                if (draggedRow && draggedRow !== row) {
                    const allRows = Array.from(sortableList.querySelectorAll('.slide-row'));
                    const draggedIndex = allRows.indexOf(draggedRow);
                    const targetIndex = allRows.indexOf(row);

                    if (draggedIndex < targetIndex) {
                        row.after(draggedRow);
                    } else {
                        row.before(draggedRow);
                    }

                    // Save new order
                    saveSlidesOrder();
                }
            });
        });
    }

    function saveSlidesOrder() {
        const updatedRows = sortableList.querySelectorAll('.slide-row');
        const orderIds = Array.from(updatedRows).map(r => r.getAttribute('data-id'));

        // Update UI badge numbers instantly
        updatedRows.forEach((r, idx) => {
            const badge = r.querySelector('.sort-badge');
            if (badge) badge.innerText = idx + 1;
        });

        // Send AJAX request
        fetch("{{ route('admin.hero-slides.reorder') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ order: orderIds })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Show clean toast
                showToast(data.message || 'Slides reordered successfully!');
            }
        })
        .catch(err => {
            console.error('Error reordering slides:', err);
        });
    }

    function showToast(msg) {
        let toast = document.getElementById('slideToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'slideToast';
            toast.className = 'fixed bottom-6 right-6 z-50 px-4 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-semibold shadow-xl flex items-center gap-2 transition-all duration-300 transform translate-y-4 opacity-0 pointer-events-none';
            toast.innerHTML = `<i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i><span id="toastMsg"></span>`;
            document.body.appendChild(toast);
        }
        document.getElementById('toastMsg').innerText = msg;
        toast.classList.remove('translate-y-4', 'opacity-0');
        if (window.createFloorIcons) window.createFloorIcons();
        setTimeout(() => {
            toast.classList.add('translate-y-4', 'opacity-0');
        }, 2500);
    }
</script>
@endsection
