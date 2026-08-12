@extends('frontend.layouts.app')

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Create New Bill</h4>
                <p class="text-muted small mb-0">Generate sales bills or project billing statements with custom terms and bank details</p>
            </div>
            <div>
                <a href="{{ route('bills.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm">
                    <i class="fa fa-arrow-left me-2"></i>Back to Bills
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <form id="billForm" action="{{ route('bills.store') }}" method="POST">
        @csrf

        <!-- Section 1: Bill Basic Information -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-3"><i class="fe fe-file-text me-2 text-primary"></i>Bill Information</h6>

                <div class="row g-3 mb-3">
                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Bill Type <span class="text-danger">*</span></label>
                        <select class="form-select border-light-subtle" name="bill_type" id="bill_type" required>
                            <option value="">Select Bill Type</option>
                            <option value="sale">Sales Bill</option>
                            <option value="project">Project Bill</option>
                        </select>
                    </div>

                    <!-- Dynamic Selection Area -->
                    <div class="col-md-6 col-12" id="dynamic-selection">
                        <!-- Populated by JavaScript -->
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-lg-4 col-md-4 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Reference Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control border-light-subtle" name="reference_number" value="BIL-{{ date('Ymd-His') }}" required>
                    </div>

                    <div class="col-lg-4 col-md-4 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Bill Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control border-light-subtle" name="bill_date" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="col-lg-4 col-md-4 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Work Order Number</label>
                        <input type="text" class="form-control border-light-subtle" name="work_order_number" placeholder="Enter work order reference">
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Client Information Section -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-3"><i class="fe fe-user me-2 text-primary"></i>Client Information</h6>

                <div class="row g-3 mb-3">
                    <div class="col-lg-4 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Client/Company Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control border-light-subtle bg-light" id="client_name" name="client_name" placeholder="Auto-filled from sale/project" readonly>
                    </div>

                    <div class="col-lg-4 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Attention To <span class="text-danger">*</span></label>
                        <input type="text" class="form-control border-light-subtle" id="attention_to" name="attention_to" placeholder="Enter contact person name" required>
                    </div>

                    <div class="col-lg-4 col-md-12 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Designation <span class="text-danger">*</span></label>
                        <input type="text" class="form-control border-light-subtle" id="designation" name="designation" placeholder="Enter contact person designation" required>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Client Address <span class="text-danger">*</span></label>
                        <textarea class="form-control border-light-subtle bg-light" id="client_address" name="client_address" rows="2" placeholder="Auto-filled client address" readonly></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Auto-filled Information Section -->
        <div class="card border-0 shadow-sm rounded-3 mb-4" id="auto-info-section" style="display: none;">
            <div class="card-body p-4">
                <h6 class="fw-bold text-success mb-3"><i class="fe fe-check-circle me-2"></i>Selected Order Overview</h6>
                <div class="row g-3">
                    <div class="col-md-3 col-6">
                        <span class="text-muted small d-block">Reference No</span>
                        <strong class="text-dark" id="detail-reference">-</strong>
                    </div>
                    <div class="col-md-3 col-6">
                        <span class="text-muted small d-block">Order Date</span>
                        <strong class="text-dark" id="detail-date">-</strong>
                    </div>
                    <div class="col-md-3 col-6">
                        <span class="text-muted small d-block">Total Amount</span>
                        <strong class="text-primary" id="detail-amount">-</strong>
                    </div>
                    <div class="col-md-3 col-6">
                        <span class="text-muted small d-block">Due Payment</span>
                        <strong class="text-danger" id="detail-due">-</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Bill Items Section -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="fe fe-box me-2 text-primary"></i>Bill Items</h6>
                    <span class="badge badge-soft-primary px-3 py-1 rounded-pill" id="items-count">0 items</span>
                </div>

                <div id="items-container">
                    <div class="text-center p-5 text-muted" id="no-items-message">
                        <i class="fe fe-shopping-cart fs-1 mb-2 d-block"></i>
                        <p class="mb-0">Select a sale order or project above to auto-fill bill items</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Totals & Hidden Fields -->
        <input type="hidden" id="total_amount" name="total_amount" value="0">
        <input type="hidden" id="subtotal" name="subtotal" value="0">
        <input type="hidden" name="selected_sale_id" id="selected_sale_id">
        <input type="hidden" name="selected_project_id" id="selected_project_id">

        <!-- Section 5: Subject & Terms Section -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-3"><i class="fe fe-list me-2 text-primary"></i>Subject & Terms</h6>

                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Bill Subject <span class="text-danger">*</span></label>
                        <input type="text" class="form-control border-light-subtle" name="subject" value="Bill for Supplying of Products/Services" required placeholder="Enter bill subject">
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small text-secondary fw-semibold mb-0">Custom Terms & Conditions <span class="text-danger">*</span></label>
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" id="use_default_terms">
                                <label class="form-check-label small text-muted" for="use_default_terms">Use default terms</label>
                            </div>
                        </div>
                        <textarea class="form-control border-light-subtle" name="terms_conditions" id="terms_conditions" rows="4" placeholder="Enter terms and conditions..." required></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 6: Bank & Company Details Section -->
        <div class="row g-4 mb-4">
            <div class="col-lg-6 col-12">
                <div class="card border-0 shadow-sm rounded-3 h-100 mb-0">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark mb-3"><i class="fe fe-credit-card me-2 text-primary"></i>Bank Account Information</h6>
                        <div class="mb-3">
                            <label class="form-label small text-secondary fw-semibold mb-1">Select Bank Account <span class="text-danger">*</span></label>
                            <select class="form-select border-light-subtle" name="bank_detail_id" id="bank_detail_id" required>
                                <option value="">Select Bank Account</option>
                                @foreach ($bankDetails as $bank)
                                    <option value="{{ $bank->id }}" {{ $bank->is_default ? 'selected' : '' }}>
                                        {{ $bank->bank_name }} - {{ $bank->account_name }} ({{ $bank->account_number }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="p-3 bg-light rounded-3 border small" id="bank-details-preview">
                            <div class="mb-1"><strong>Account Name:</strong> <span id="preview-account-name">-</span></div>
                            <div class="mb-1"><strong>Bank Name:</strong> <span id="preview-bank-name">-</span></div>
                            <div class="mb-1"><strong>Branch:</strong> <span id="preview-branch">-</span></div>
                            <div class="mb-1"><strong>Account No:</strong> <span id="preview-account-number">-</span></div>
                            <div><strong>Account Type:</strong> <span id="preview-account-type">-</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-12">
                <div class="card border-0 shadow-sm rounded-3 h-100 mb-0">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark mb-3"><i class="fe fe-briefcase me-2 text-primary"></i>Company Signatory Details</h6>
                        <div class="mb-3">
                            <label class="form-label small text-secondary fw-semibold mb-1">Select Company <span class="text-danger">*</span></label>
                            <select class="form-select border-light-subtle" name="company_detail_id" id="company_detail_id" required>
                                <option value="">Select Company</option>
                                @foreach ($companyDetails as $company)
                                    <option value="{{ $company->id }}" {{ $company->is_default ? 'selected' : '' }}>
                                        {{ $company->name }} - {{ $company->signatory_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="p-3 bg-light rounded-3 border small" id="company-details-preview">
                            <div class="mb-1"><strong>Company:</strong> <span id="preview-company-name">-</span></div>
                            <div class="mb-1"><strong>Signatory:</strong> <span id="preview-signatory-name">-</span></div>
                            <div class="mb-1"><strong>Designation:</strong> <span id="preview-signatory-designation">-</span></div>
                            <div><strong>Phone:</strong> <span id="preview-company-phone">-</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PDF Print Options -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-3"><i class="fe fe-printer me-2 text-primary"></i>PDF Print &amp; Display Options</h6>
                <div class="d-flex flex-wrap gap-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="show_signature" id="show_signature" value="1" {{ old('show_signature', true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-dark" for="show_signature">
                            Include Authorized Signature
                        </label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="show_seal" id="show_seal" value="1" {{ old('show_seal', true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-dark" for="show_seal">
                            Include Company Seal (Sill)
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Action Buttons -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4 d-flex justify-content-end gap-2">
                <a href="{{ route('bills.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3">Cancel</a>
                <button type="submit" class="btn btn-primary px-4 py-2 rounded-3">Generate Bill</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.billFormHandlerLoaded) return;
    window.billFormHandlerLoaded = true;

    const bankDetails = @json($bankDetails);
    const companyDetails = @json($companyDetails);

    const billTypeSelect = document.getElementById('bill_type');
    const dynamicSelection = document.getElementById('dynamic-selection');
    const autoInfoSection = document.getElementById('auto-info-section');
    const itemsContainer = document.getElementById('items-container');
    const noItemsMessage = document.getElementById('no-items-message');
    const useDefaultTerms = document.getElementById('use_default_terms');
    const termsTextarea = document.getElementById('terms_conditions');

    const bankSelect = document.getElementById('bank_detail_id');
    const companySelect = document.getElementById('company_detail_id');

    const defaultTerms = `The products come with a 1-year limited warranty. Please note that the warranty does not cover physical damage or burn cases.
The delivered products & accessories will not be changeable after use.
The party will pay by Cash / Account Payee Cheque / DD / Pay Order in favor of our company with work order.
Govt. VAT & TAX: Prices are inclusive of all kinds of TAX & VAT as per government rule.`;

    termsTextarea.value = defaultTerms;
    const baseUrl = window.location.origin;

    let loadedSalesData = null;
    let loadedProjectsData = null;

    updateBankPreview();
    updateCompanyPreview();

    bankSelect.addEventListener('change', updateBankPreview);
    companySelect.addEventListener('change', updateCompanyPreview);

    useDefaultTerms.addEventListener('change', function() {
        if (this.checked) {
            termsTextarea.value = defaultTerms;
            termsTextarea.readOnly = true;
        } else {
            termsTextarea.readOnly = false;
        }
    });

    billTypeSelect.addEventListener('change', function() {
        const type = this.value;
        dynamicSelection.innerHTML = '';
        autoInfoSection.style.display = 'none';
        itemsContainer.innerHTML = '';
        itemsContainer.appendChild(noItemsMessage);
        noItemsMessage.style.display = 'block';

        if (type === 'sale') {
            loadSalesDropdown();
        } else if (type === 'project') {
            loadProjectsDropdown();
        }
    });

    function loadSalesDropdown() {
        dynamicSelection.innerHTML = '<label class="form-label small text-secondary fw-semibold mb-1">Select Sale Order <span class="text-danger">*</span></label><select class="form-select border-light-subtle" id="sale_id" required><option value="">Loading Sales...</option></select>';

        fetch(`${baseUrl}/bills/get-sales`)
            .then(res => res.json())
            .then(data => {
                const salesList = Array.isArray(data) ? data : (data.sales || []);
                loadedSalesData = salesList;
                const saleSelect = document.getElementById('sale_id');
                saleSelect.innerHTML = '<option value="">Select Sale Order</option>';
                if (salesList.length === 0) {
                    saleSelect.innerHTML = '<option value="">No sales orders found</option>';
                    return;
                }
                salesList.forEach(sale => {
                    saleSelect.innerHTML += `<option value="${sale.id}">#${sale.order_no} - ${sale.customer_name} (৳${sale.payble})</option>`;
                });

                saleSelect.addEventListener('change', function() {
                    const saleId = this.value;
                    document.getElementById('selected_sale_id').value = saleId;
                    document.getElementById('selected_project_id').value = '';
                    if (saleId) populateSaleData(saleId);
                });
            });
    }

    function loadProjectsDropdown() {
        dynamicSelection.innerHTML = '<label class="form-label small text-secondary fw-semibold mb-1">Select Project <span class="text-danger">*</span></label><select class="form-select border-light-subtle" id="project_id" required><option value="">Loading Projects...</option></select>';

        fetch(`${baseUrl}/bills/get-projects`)
            .then(res => res.json())
            .then(data => {
                const projectsList = Array.isArray(data) ? data : (data.projects || []);
                loadedProjectsData = projectsList;
                const projSelect = document.getElementById('project_id');
                projSelect.innerHTML = '<option value="">Select Project</option>';
                if (projectsList.length === 0) {
                    projSelect.innerHTML = '<option value="">No projects found</option>';
                    return;
                }
                projectsList.forEach(proj => {
                    projSelect.innerHTML += `<option value="${proj.id}">${proj.name} - ${proj.client_name}</option>`;
                });

                projSelect.addEventListener('change', function() {
                    const projId = this.value;
                    document.getElementById('selected_project_id').value = projId;
                    document.getElementById('selected_sale_id').value = '';
                    if (projId) populateProjectData(projId);
                });
            });
    }

    function populateSaleData(saleId) {
        const sale = loadedSalesData.find(s => s.id == saleId);
        if (!sale) return;

        document.getElementById('client_name').value = sale.customer_name || '';
        document.getElementById('client_address').value = sale.customer_address || '';

        document.getElementById('detail-reference').textContent = '#' + sale.order_no;
        document.getElementById('detail-date').textContent = sale.created_at;
        document.getElementById('detail-amount').textContent = '৳' + parseFloat(sale.payble).toFixed(2);
        document.getElementById('detail-due').textContent = '৳' + parseFloat(sale.due_payment || 0).toFixed(2);
        autoInfoSection.style.display = 'block';

        renderItemsTable(sale.items);
    }

    function populateProjectData(projId) {
        const proj = loadedProjectsData.find(p => p.id == projId);
        if (!proj) return;

        document.getElementById('client_name').value = proj.client_name || '';
        document.getElementById('client_address').value = proj.client_address || '';

        document.getElementById('detail-reference').textContent = proj.name;
        document.getElementById('detail-date').textContent = proj.created_at;
        document.getElementById('detail-amount').textContent = '৳' + parseFloat(proj.budget).toFixed(2);
        document.getElementById('detail-due').textContent = '৳' + parseFloat(proj.due_payment || 0).toFixed(2);
        autoInfoSection.style.display = 'block';

        renderItemsTable(proj.items);
    }

    function renderItemsTable(items) {
        if (!items || items.length === 0) {
            itemsContainer.innerHTML = '<div class="text-center p-4 text-muted">No items found for this selection</div>';
            return;
        }

        document.getElementById('items-count').textContent = items.length + ' items';

        let html = `<div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="bg-light text-secondary fs-7 text-uppercase"><tr><th class="ps-3">#</th><th>Description</th><th style="width: 120px;">Qty</th><th style="width: 130px;">Unit Price</th><th class="text-end pe-3" style="width: 140px;">Total</th></tr></thead><tbody>`;

        let subtotal = 0;
        items.forEach((item, index) => {
            const lineTotal = item.quantity * item.unit_price;
            subtotal += lineTotal;
            html += `<tr>
                <td class="ps-3 text-muted">${index + 1}</td>
                <td>
                    <input type="text" name="items[${index}][description]" class="form-control border-light-subtle" value="${item.description}" required>
                    <input type="hidden" name="items[${index}][unit]" value="${item.unit || 'Pcs'}">
                    <input type="hidden" name="items[${index}][total]" class="line-total-input" value="${lineTotal}">
                </td>
                <td><input type="number" name="items[${index}][quantity]" class="form-control border-light-subtle qty-calc" value="${item.quantity}" min="1" required style="width: 100px;"></td>
                <td><input type="number" step="0.01" name="items[${index}][unit_price]" class="form-control border-light-subtle price-calc" value="${item.unit_price}" required style="width: 120px;"></td>
                <td class="text-end pe-3 fw-bold text-dark">৳<span class="line-total">${lineTotal.toFixed(2)}</span></td>
            </tr>`;
        });

        html += `</tbody></table></div>`;
        itemsContainer.innerHTML = html;

        document.getElementById('subtotal').value = subtotal.toFixed(2);
        document.getElementById('total_amount').value = subtotal.toFixed(2);
    }

    function updateBankPreview() {
        const selectedId = bankSelect.value;
        const bank = bankDetails.find(b => b.id == selectedId);
        if (bank) {
            document.getElementById('preview-account-name').textContent = bank.account_name || '-';
            document.getElementById('preview-bank-name').textContent = bank.bank_name || '-';
            document.getElementById('preview-branch').textContent = bank.branch || '-';
            document.getElementById('preview-account-number').textContent = bank.account_number || '-';
            document.getElementById('preview-account-type').textContent = bank.account_type || '-';
        }
    }

    function updateCompanyPreview() {
        const selectedId = companySelect.value;
        const comp = companyDetails.find(c => c.id == selectedId);
        if (comp) {
            document.getElementById('preview-company-name').textContent = comp.name || '-';
            document.getElementById('preview-signatory-name').textContent = comp.signatory_name || '-';
            document.getElementById('preview-signatory-designation').textContent = comp.signatory_designation || '-';
            document.getElementById('preview-company-phone').textContent = comp.phone || '-';
        }
    }
});
</script>
@endpush
