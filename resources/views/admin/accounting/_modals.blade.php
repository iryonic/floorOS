<!-- ======================================================== -->
<!-- MODAL: MONEY IN (CREDIT / DEPOSIT)                        -->
<!-- ======================================================== -->
<div id="creditModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <!-- Modal Header -->
        <div class="p-5 border-b border-[#E3E8E4] flex items-center justify-between bg-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-brand-light text-brand flex items-center justify-center shadow-2xs border border-brand/20">
                    <i data-lucide="plus" class="w-5 h-5 stroke-[3]"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-[#202925]">Money Received (+ Money In)</h3>
                    <p class="text-[11px] text-[#6B756F]">Credit / Deposit into business accounts (Cash, UPI, Bank)</p>
                </div>
            </div>
            <button type="button" onclick="closeCreditModal()" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Modal Form -->
        <form id="creditForm" action="{{ route('admin.accounting.credit.store') }}" method="POST" class="p-5 space-y-4" onsubmit="return validateAndSubmitFinancialForm(this, 'creditSubmitBtn')">
            @csrf
            <input type="hidden" name="return_url" value="{{ request()->fullUrl() }}">

            <!-- Amount Input -->
            <div>
                <label class="block text-xs font-bold text-[#202925] mb-1">Amount Received (₹) *</label>
                <div class="relative">
                    <input type="number" 
                           step="0.01" 
                           min="0.01" 
                           name="amount" 
                           id="creditAmountInput" 
                           required 
                           placeholder="    0.00" 
                           class="w-full input-custom text-sm font-bold text-[#202925] pl-[30px]">
                </div>
                <span class="text-[10px] text-[#6B756F] block mt-1">Must be greater than zero.</span>
            </div>

            <!-- Purpose Input -->
            <div>
                <label class="block text-xs font-bold text-[#202925] mb-1">Reason (What is this for?) *</label>
                <input type="text" 
                       name="purpose" 
                       id="creditPurposeInput" 
                       required 
                       placeholder="e.g. Customer payment, advance, scrap sale, bank deposit..." 
                       class="w-full input-custom text-xs">
                <span class="text-[10px] text-[#6B756F] block mt-1">Short note of why you received this money.</span>
            </div>

            <!-- Payment Method Input (Default Cash, Not strictly mandatory) -->
            <div>
                <label class="block text-xs font-bold text-[#202925] mb-1">
                    Payment Method <span class="text-[10px] text-[#6B756F] font-normal">(Default: Cash)</span>
                </label>
                <select name="payment_method" id="creditPaymentMethodInput" class="w-full input-custom text-xs bg-white">
                    <option value="cash" selected>Cash</option>
                    <option value="gpay">GPay (Google Pay)</option>
                    <option value="phonepe">PhonePe</option>
                    <option value="paytm">Paytm</option>
                    <option value="mpay">mPay</option>
                    <option value="bank_transfer">Bank Transfer / NEFT</option>
                    <option value="cheque">Cheque</option>
                </select>
                <span class="text-[10px] text-[#6B756F] block mt-1">Optional. Defaults to Cash if not selected.</span>
            </div>

            <!-- Optional Notes -->
            <div>
                <label class="block text-xs font-semibold text-[#202925] mb-1">Extra Details / Note (Optional)</label>
                <textarea name="notes" rows="2" placeholder="e.g. Bank UTR number, receipt reference, customer name..." class="w-full input-custom text-xs resize-none"></textarea>
            </div>

            <!-- Modal Action Buttons -->
            <div class="pt-2 border-t border-[#E3E8E4] flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeCreditModal()" class="btn-secondary text-xs py-2 px-4">
                    Cancel
                </button>
                <button type="submit" id="creditSubmitBtn" class="btn-brand text-xs py-2 px-5 shadow-xs flex items-center gap-1.5 font-bold">
                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                    <span>Save Money Received</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL: MONEY OUT (DEBIT / EXPENSE)                        -->
<!-- ======================================================== -->
<div id="debitModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <!-- Modal Header -->
        <div class="p-5 border-b border-[#E3E8E4] flex items-center justify-between bg-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 border border-rose-200/60 flex items-center justify-center shadow-2xs">
                    <i data-lucide="minus" class="w-5 h-5 stroke-[3]"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-[#202925]">Money Paid Out (- Money Out)</h3>
                    <p class="text-[11px] text-[#6B756F]">Debit / Expense from business accounts (Daily costs, bills)</p>
                </div>
            </div>
            <button type="button" onclick="closeDebitModal()" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Modal Form -->
        <form id="debitForm" action="{{ route('admin.accounting.debit.store') }}" method="POST" class="p-5 space-y-4" onsubmit="return validateAndSubmitFinancialForm(this, 'debitSubmitBtn')">
            @csrf
            <input type="hidden" name="return_url" value="{{ request()->fullUrl() }}">

            <!-- Amount Input -->
            <div>
                <label class="block text-xs font-bold text-[#202925] mb-1">Amount Paid (₹) *</label>
                <div class="relative">
                    <input type="number" 
                           step="0.01" 
                           min="0.01" 
                           name="amount" 
                           id="debitAmountInput" 
                           required 
                           placeholder="     0.00" 
                           class="w-full input-custom text-sm font-bold text-[#202925]  pl-[30px]">
                </div>
                <span class="text-[10px] text-[#6B756F] block mt-1">Must be greater than zero.</span>
            </div>

            <!-- Purpose Input -->
            <div>
                <label class="block text-xs font-bold text-[#202925] mb-1">Reason (What did you pay for?) *</label>
                <input type="text" 
                       name="purpose" 
                       id="debitPurposeInput" 
                       required 
                       placeholder="e.g. Tea & snacks, showroom cleaning, electrician, delivery boy..." 
                       class="w-full input-custom text-xs">
                <span class="text-[10px] text-[#6B756F] block mt-1">Short note of what this money was spent on.</span>
            </div>

            <!-- Payment Method Input (Default Cash, Not strictly mandatory) -->
            <div>
                <label class="block text-xs font-bold text-[#202925] mb-1">
                    Payment Method <span class="text-[10px] text-[#6B756F] font-normal">(Default: Cash)</span>
                </label>
                <select name="payment_method" id="debitPaymentMethodInput" class="w-full input-custom text-xs bg-white">
                    <option value="cash" selected>Cash</option>
                    <option value="gpay">GPay (Google Pay)</option>
                    <option value="phonepe">PhonePe</option>
                    <option value="paytm">Paytm</option>
                    <option value="mpay">mPay</option>
                    <option value="bank_transfer">Bank Transfer / NEFT</option>
                    <option value="cheque">Cheque</option>
                </select>
                <span class="text-[10px] text-[#6B756F] block mt-1">Optional. Defaults to Cash if not selected.</span>
            </div>

            <!-- Optional Notes -->
            <div>
                <label class="block text-xs font-semibold text-[#202925] mb-1">Extra Details / Paid To (Optional)</label>
                <textarea name="notes" rows="2" placeholder="e.g. Paid to Ramu, cash voucher number, bill copy..." class="w-full input-custom text-xs resize-none"></textarea>
            </div>

            <!-- Modal Action Buttons -->
            <div class="pt-2 border-t border-[#E3E8E4] flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeDebitModal()" class="btn-secondary text-xs py-2 px-4">
                    Cancel
                </button>
                <button type="submit" id="debitSubmitBtn" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-1.5 active:scale-95">
                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                    <span>Save Money Paid Out</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreditModal() {
        const modal = document.getElementById('creditModal');
        if (!modal) return;
        modal.classList.remove('hidden');
        const input = document.getElementById('creditAmountInput');
        if (input) {
            input.value = '';
            setTimeout(() => input.focus(), 100);
        }
        const purposeInput = document.getElementById('creditPurposeInput');
        if (purposeInput) purposeInput.value = '';
        const methodSelect = document.getElementById('creditPaymentMethodInput');
        if (methodSelect) methodSelect.value = 'cash';
        if (window.createFloorIcons) window.createFloorIcons();
    }

    function closeCreditModal() {
        const modal = document.getElementById('creditModal');
        if (modal) modal.classList.add('hidden');
    }

    function openDebitModal() {
        const modal = document.getElementById('debitModal');
        if (!modal) return;
        modal.classList.remove('hidden');
        const input = document.getElementById('debitAmountInput');
        if (input) {
            input.value = '';
            setTimeout(() => input.focus(), 100);
        }
        const purposeInput = document.getElementById('debitPurposeInput');
        if (purposeInput) purposeInput.value = '';
        const methodSelect = document.getElementById('debitPaymentMethodInput');
        if (methodSelect) methodSelect.value = 'cash';
        if (window.createFloorIcons) window.createFloorIcons();
    }

    function closeDebitModal() {
        const modal = document.getElementById('debitModal');
        if (modal) modal.classList.add('hidden');
    }

    function validateAndSubmitFinancialForm(form, btnId) {
        const amountInput = form.querySelector('input[name="amount"]');
        const purposeInput = form.querySelector('input[name="purpose"]');
        const btn = document.getElementById(btnId);

        const amount = parseFloat(amountInput.value);
        if (isNaN(amount) || amount <= 0) {
            alert('Please enter a valid amount greater than zero.');
            amountInput.focus();
            return false;
        }

        if (!purposeInput.value.trim()) {
            alert('Please specify the purpose for this transaction.');
            purposeInput.focus();
            return false;
        }

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-3.5 w-3.5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Saving...`;
        }

        return true;
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeCreditModal();
            closeDebitModal();
        }
    });
</script>
