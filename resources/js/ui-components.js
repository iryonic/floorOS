/**
 * FloorOS Reusable Custom UI Components
 * - Custom Alert Dialog (window.showAlert)
 * - Custom Confirmation Dialog (window.showConfirm)
 * - Custom Select Component (FloorSelect)
 */

class FloorDialogManager {
    constructor() {
        this.container = null;
        this.init();
    }

    init() {
        if (document.getElementById('floor-dialog-container')) {
            this.container = document.getElementById('floor-dialog-container');
            return;
        }

        this.container = document.createElement('div');
        this.container.id = 'floor-dialog-container';
        this.container.className = 'fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 hidden opacity-0 pointer-events-none';
        this.container.innerHTML = `
            <div id="floor-dialog-box" class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 relative border border-slate-200 transform scale-95 transition-all duration-200">
                <div class="flex items-start gap-4">
                    <div id="floor-dialog-icon-wrapper" class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0">
                        <span id="floor-dialog-icon" class="w-6 h-6 flex items-center justify-center text-lg font-bold"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 id="floor-dialog-title" class="text-base font-bold text-slate-900 leading-snug"></h3>
                        <p id="floor-dialog-message" class="text-xs text-slate-600 mt-1.5 leading-relaxed whitespace-pre-line"></p>
                    </div>
                </div>
                <div id="floor-dialog-actions" class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" id="floor-dialog-cancel-btn" class="px-4 py-2 text-xs font-semibold rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 transition hidden">Cancel</button>
                    <button type="button" id="floor-dialog-confirm-btn" class="px-5 py-2 text-xs font-semibold rounded-xl text-white transition flex items-center justify-center gap-1.5 min-w-[80px]">OK</button>
                </div>
            </div>
        `;
        document.body.appendChild(this.container);
    }

    showAlert({ title = 'Notification', message = '', type = 'info', confirmText = 'OK' }) {
        return new Promise((resolve) => {
            this.init();
            const box = document.getElementById('floor-dialog-box');
            const iconWrap = document.getElementById('floor-dialog-icon-wrapper');
            const iconEl = document.getElementById('floor-dialog-icon');
            const titleEl = document.getElementById('floor-dialog-title');
            const msgEl = document.getElementById('floor-dialog-message');
            const cancelBtn = document.getElementById('floor-dialog-cancel-btn');
            const confirmBtn = document.getElementById('floor-dialog-confirm-btn');

            titleEl.textContent = title;
            msgEl.textContent = message;
            confirmBtn.textContent = confirmText;
            confirmBtn.disabled = false;
            cancelBtn.classList.add('hidden');

            // Visual Styling per type
            iconWrap.className = 'w-11 h-11 rounded-xl flex items-center justify-center shrink-0 ';
            confirmBtn.className = 'px-5 py-2 text-xs font-semibold rounded-xl text-white transition flex items-center justify-center gap-1.5 min-w-[80px] ';

            if (type === 'success') {
                iconWrap.classList.add('bg-emerald-50', 'text-emerald-600');
                iconEl.innerHTML = '✓';
                confirmBtn.classList.add('btn-brand');
            } else if (type === 'error') {
                iconWrap.classList.add('bg-rose-50', 'text-rose-600');
                iconEl.innerHTML = '✕';
                confirmBtn.classList.add('bg-rose-600', 'hover:bg-rose-700');
            } else if (type === 'warning') {
                iconWrap.classList.add('bg-amber-50', 'text-amber-600');
                iconEl.innerHTML = '⚠';
                confirmBtn.classList.add('btn-accent');
            } else {
                iconWrap.classList.add('bg-brand-light', 'text-brand');
                iconEl.innerHTML = 'ℹ';
                confirmBtn.classList.add('btn-brand');
            }

            const cleanup = () => {
                this.container.classList.add('opacity-0', 'pointer-events-none');
                box.classList.remove('scale-100');
                box.classList.add('scale-95');
                setTimeout(() => {
                    this.container.classList.add('hidden');
                    resolve();
                }, 150);
            };

            confirmBtn.onclick = () => cleanup();
            this.container.onclick = (e) => {
                if (e.target === this.container) cleanup();
            };

            // Show
            this.container.classList.remove('hidden');
            setTimeout(() => {
                this.container.classList.remove('opacity-0', 'pointer-events-none');
                box.classList.remove('scale-95');
                box.classList.add('scale-100');
                confirmBtn.focus();
            }, 10);
        });
    }

    showConfirm({ title = 'Please Confirm', message = 'Are you sure you want to proceed?', confirmText = 'Confirm', cancelText = 'Cancel', isDestructive = false }) {
        return new Promise((resolve) => {
            this.init();
            const box = document.getElementById('floor-dialog-box');
            const iconWrap = document.getElementById('floor-dialog-icon-wrapper');
            const iconEl = document.getElementById('floor-dialog-icon');
            const titleEl = document.getElementById('floor-dialog-title');
            const msgEl = document.getElementById('floor-dialog-message');
            const cancelBtn = document.getElementById('floor-dialog-cancel-btn');
            const confirmBtn = document.getElementById('floor-dialog-confirm-btn');

            titleEl.textContent = title;
            msgEl.textContent = message;
            confirmBtn.textContent = confirmText;
            confirmBtn.disabled = false;
            cancelBtn.textContent = cancelText;
            cancelBtn.classList.remove('hidden');

            iconWrap.className = 'w-11 h-11 rounded-xl flex items-center justify-center shrink-0 ';
            confirmBtn.className = 'px-5 py-2 text-xs font-semibold rounded-xl text-white transition flex items-center justify-center gap-1.5 min-w-[80px] ';

            if (isDestructive) {
                iconWrap.classList.add('bg-rose-50', 'text-rose-600');
                iconEl.innerHTML = '🗑';
                confirmBtn.classList.add('bg-rose-600', 'hover:bg-rose-700');
            } else {
                iconWrap.classList.add('bg-brand-light', 'text-brand');
                iconEl.innerHTML = '?';
                confirmBtn.classList.add('btn-brand');
            }

            const close = (result) => {
                this.container.classList.add('opacity-0', 'pointer-events-none');
                box.classList.remove('scale-100');
                box.classList.add('scale-95');
                setTimeout(() => {
                    this.container.classList.add('hidden');
                    resolve(result);
                }, 150);
            };

            confirmBtn.onclick = () => {
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = '<span class="inline-block animate-spin mr-1">⌛</span> Working...';
                close(true);
            };

            cancelBtn.onclick = () => close(false);
            this.container.onclick = (e) => {
                if (e.target === this.container) close(false);
            };

            this.container.classList.remove('hidden');
            setTimeout(() => {
                this.container.classList.remove('opacity-0', 'pointer-events-none');
                box.classList.remove('scale-95');
                box.classList.add('scale-100');
                confirmBtn.focus();
            }, 10);
        });
    }
}

// 2. Custom Select Component
export class FloorSelect {
    constructor(selectElement) {
        if (!selectElement || selectElement._floorSelectInitialized) return;
        this.select = selectElement;
        this.select._floorSelectInitialized = true;
        this.wrapper = null;
        this.trigger = null;
        this.dropdown = null;
        this.searchInput = null;
        this.optionsList = null;
        this.isOpen = false;
        this.buildUI();
    }

    buildUI() {
        // Hide native select visually
        this.select.style.display = 'none';

        // Wrapper
        this.wrapper = document.createElement('div');
        this.wrapper.className = 'relative inline-block w-full text-left font-sans';
        this.select.parentNode.insertBefore(this.wrapper, this.select);
        this.wrapper.appendChild(this.select);

        // Trigger Button
        this.trigger = document.createElement('button');
        this.trigger.type = 'button';
        this.trigger.className = 'w-full flex items-center justify-between px-3 py-2 text-xs bg-white border border-[#E3E8E4] rounded-lg shadow-2xs hover:border-[var(--color-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition text-[#202925]';
        this.trigger.innerHTML = `
            <span class="truncate font-medium">${this.getSelectedText() || 'Select option...'}</span>
            <svg class="w-4 h-4 ml-2 text-neutral-400 shrink-0 transition-transform duration-150" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
        `;
        this.wrapper.appendChild(this.trigger);

        // Dropdown Panel
        this.dropdown = document.createElement('div');
        this.dropdown.className = 'absolute left-0 mt-1.5 w-full bg-white border border-[#E3E8E4] rounded-xl shadow-xl z-50 hidden py-1.5 animate-in fade-in zoom-in-95 duration-100';
        
        // Search if > 5 options
        const optionCount = this.select.options.length;
        if (optionCount >= 5) {
            const searchContainer = document.createElement('div');
            searchContainer.className = 'px-2 pb-1.5 pt-0.5 border-b border-[#E3E8E4]';
            this.searchInput = document.createElement('input');
            this.searchInput.type = 'text';
            this.searchInput.placeholder = 'Search options...';
            this.searchInput.className = 'w-full px-2.5 py-1 text-xs bg-slate-50 border border-slate-200 rounded-md focus:outline-none focus:border-[var(--color-primary)] focus:bg-white';
            searchContainer.appendChild(this.searchInput);
            this.dropdown.appendChild(searchContainer);

            this.searchInput.addEventListener('input', (e) => {
                this.filterOptions(e.target.value.toLowerCase());
            });
        }

        // Options List
        this.optionsList = document.createElement('ul');
        this.optionsList.className = 'max-h-56 overflow-y-auto no-scrollbar py-1 text-xs divide-y divide-slate-50';
        this.renderOptions();
        this.dropdown.appendChild(this.optionsList);

        this.wrapper.appendChild(this.dropdown);

        // Events
        this.trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggle();
        });

        document.addEventListener('click', (e) => {
            if (!this.wrapper.contains(e.target)) {
                this.close();
            }
        });

        // Keyboard navigation
        this.trigger.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowDown' || e.key === 'Enter') {
                e.preventDefault();
                this.open();
            }
        });

        // Listen for external change
        this.select.addEventListener('change', () => {
            this.updateTriggerText();
            this.renderOptions();
        });
    }

    getSelectedText() {
        const selected = this.select.options[this.select.selectedIndex];
        return selected ? selected.text : '';
    }

    updateTriggerText() {
        const textSpan = this.trigger.querySelector('span');
        if (textSpan) {
            textSpan.textContent = this.getSelectedText() || 'Select option...';
        }
    }

    renderOptions() {
        this.optionsList.innerHTML = '';
        Array.from(this.select.options).forEach((opt, idx) => {
            const li = document.createElement('li');
            li.className = 'px-3 py-2 cursor-pointer flex items-center justify-between text-xs hover:bg-[var(--color-primary-light,#EDFBE8)] hover:text-[var(--color-primary,#2EAA1F)] transition';
            if (opt.selected) {
                li.classList.add('bg-[var(--color-primary-light,#EDFBE8)]', 'text-[var(--color-primary,#2EAA1F)]', 'font-semibold');
            } else {
                li.classList.add('text-slate-700');
            }
            li.innerHTML = `
                <span class="truncate">${opt.text}</span>
                ${opt.selected ? '<span class="text-xs">✓</span>' : ''}
            `;
            li.addEventListener('click', () => {
                this.select.selectedIndex = idx;
                this.select.dispatchEvent(new Event('change', { bubbles: true }));
                this.close();
                this.trigger.focus();
            });
            this.optionsList.appendChild(li);
        });
    }

    filterOptions(term) {
        const items = this.optionsList.querySelectorAll('li');
        Array.from(this.select.options).forEach((opt, idx) => {
            const match = opt.text.toLowerCase().includes(term);
            if (items[idx]) {
                items[idx].style.display = match ? 'flex' : 'none';
            }
        });
    }

    toggle() {
        if (this.isOpen) this.close();
        else this.open();
    }

    open() {
        this.dropdown.classList.remove('hidden');
        this.trigger.querySelector('svg')?.classList.add('rotate-180');
        this.isOpen = true;
        if (this.searchInput) {
            setTimeout(() => this.searchInput.focus(), 50);
        }
    }

    close() {
        this.dropdown.classList.add('hidden');
        this.trigger.querySelector('svg')?.classList.remove('rotate-180');
        this.isOpen = false;
        if (this.searchInput) {
            this.searchInput.value = '';
            this.filterOptions('');
        }
    }

    static initAll(selector = 'select.floor-select, select[data-custom-select]') {
        document.querySelectorAll(selector).forEach((el) => {
            new FloorSelect(el);
        });
    }
}

// Global Dialog instance
const dialogManager = new FloorDialogManager();

window.showAlert = (options) => dialogManager.showAlert(typeof options === 'string' ? { message: options } : options);
window.showConfirm = (options) => dialogManager.showConfirm(typeof options === 'string' ? { message: options } : options);

// Replace native alert
window.alert = (msg) => window.showAlert({ title: 'Alert', message: msg, type: 'info' });

// Global form confirmation listener
document.addEventListener('submit', (e) => {
    const form = e.target;
    if (form.dataset && form.dataset.confirm && !form.dataset.confirmed) {
        e.preventDefault();
        window.showConfirm({
            title: form.dataset.confirmTitle || 'Please Confirm',
            message: form.dataset.confirm,
            confirmText: form.dataset.confirmButton || 'Yes, Proceed',
            cancelText: form.dataset.cancelButton || 'Cancel',
            isDestructive: form.dataset.confirmDestructive === 'true'
        }).then((confirmed) => {
            if (confirmed) {
                form.dataset.confirmed = 'true';
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="inline-block animate-spin mr-1">⌛</span> Processing...';
                }
                form.submit();
            }
        });
    }
});

// Interactive Shade Selector on Product Cards (Requirement 10)
window.selectCardShade = function(btn, imgTargetId, newImgUrl, shadeName) {
    if (!btn || !imgTargetId) return;
    const cardImg = document.getElementById(imgTargetId);
    if (cardImg && newImgUrl) {
        cardImg.src = newImgUrl;
    }
    const container = btn.parentElement;
    if (container) {
        container.querySelectorAll('.shade-swatch-btn').forEach(el => {
            el.classList.remove('ring-2', 'ring-brand', 'scale-110');
            el.classList.add('ring-1', 'ring-slate-300');
            el.setAttribute('aria-selected', 'false');
        });
    }
    btn.classList.remove('ring-1', 'ring-slate-300');
    btn.classList.add('ring-2', 'ring-brand', 'scale-110');
    btn.setAttribute('aria-selected', 'true');
};

document.addEventListener('DOMContentLoaded', () => {
    FloorSelect.initAll();
});
