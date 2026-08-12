@extends('frontend.layouts.app')

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Create New Delivery Challan</h4>
                <p class="text-muted small mb-0">Generate sales delivery challans or project consignment notes with transport details</p>
            </div>
            <div>
                <a href="{{ route('challans.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm">
                    <i class="fa fa-arrow-left me-2"></i>Back to Challans
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <form id="challanForm" action="{{ route('challans.store') }}" method="POST">
        @csrf

        <!-- Section 1: Challan Basic Information -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-3"><i class="fe fe-truck me-2 text-primary"></i>Challan Information</h6>

                <div class="row g-3 mb-3">
                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Challan Type <span class="text-danger">*</span></label>
                        <select class="form-select border-light-subtle" name="type" id="challan_type" required>
                            <option value="">Select Challan Type</option>
                            <option value="sale">Sales Challan</option>
                            <option value="project">Project Challan</option>
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
                        <input type="text" class="form-control border-light-subtle" name="reference_number" value="CHL-{{ date('Ymd-His') }}" required>
                    </div>

                    <div class="col-lg-4 col-md-4 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Challan Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control border-light-subtle" name="challan_date" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="col-lg-4 col-md-4 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Work Order Reference</label>
                        <input type="text" class="form-control border-light-subtle" name="work_order_number" placeholder="Enter work order reference">
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Recipient & Transport Details -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-3"><i class="fe fe-user me-2 text-primary"></i>Recipient & Transport Details</h6>

                <div class="row g-3 mb-3">
                    <div class="col-lg-6 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Recipient Organization / Customer <span class="text-danger">*</span></label>
                        <input type="text" class="form-control border-light-subtle bg-light" id="recipient_organization" name="recipient_organization" placeholder="Auto-filled client or customer name" readonly>
                    </div>

                    <div class="col-lg-6 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Transport Vehicle / Delivery Method</label>
                        <input type="text" class="form-control border-light-subtle" name="transport_driver_name" placeholder="Driver name, Truck / Courier no...">
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Delivery Address <span class="text-danger">*</span></label>
                        <textarea class="form-control border-light-subtle bg-light" id="recipient_address" name="recipient_address" rows="2" placeholder="Auto-filled delivery address" readonly></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Auto-filled Order Overview Card -->
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
                        <span class="text-muted small d-block">Due Amount</span>
                        <strong class="text-danger" id="detail-due">-</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Challan Items Section -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="fe fe-box me-2 text-primary"></i>Challan Delivered Items</h6>
                    <span class="badge badge-soft-primary px-3 py-1 rounded-pill" id="items-count">0 items</span>
                </div>

                <div id="items-container">
                    <div class="text-center p-5 text-muted" id="no-items-message">
                        <i class="fe fe-truck fs-1 mb-2 d-block"></i>
                        <p class="mb-0">Select a sale order or project above to auto-fill delivery items</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden fields for selection -->
        <input type="hidden" name="selected_sale_id" id="selected_sale_id">
        <input type="hidden" name="selected_project_id" id="selected_project_id">

        <!-- Section 5: Remarks / Terms -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-3"><i class="fe fe-file-text me-2 text-primary"></i>Remarks & Notes</h6>
                <div class="mb-3">
                    <label class="form-label small text-secondary fw-semibold mb-1">Challan Notes / Shipping Instructions</label>
                    <textarea class="form-control border-light-subtle" name="notes" rows="3" placeholder="Enter optional delivery remarks or transport details..."></textarea>
                </div>
            </div>
        </div>

        <!-- Section 6: Company Signatory Details -->
        <div class="row g-3 mb-4">
            <div class="col-md-6 col-12">
                <div class="card border-0 shadow-sm rounded-3 h-100 mb-0">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark mb-3"><i class="fe fe-briefcase me-2 text-primary"></i>Company Signatory Details</h6>
                        <div class="mb-3">
                            <label class="form-label small text-secondary fw-semibold mb-1">Select Signatory Profile <span class="text-danger">*</span></label>
                            <select class="form-select border-light-subtle" id="company_signatory_select" required>
                                <option value="">Select Signatory Profile</option>
                                @foreach ($companyDetails as $company)
                                    <option value="{{ $company->id }}" 
                                        data-name="{{ $company->name }}"
                                        data-signatory="{{ $company->signatory_name }}"
                                        data-designation="{{ $company->signatory_designation }}"
                                        data-phone="{{ $company->phone }}"
                                        data-email="{{ $company->email }}"
                                        data-website="{{ $company->website }}"
                                        {{ $company->is_default ? 'selected' : '' }}>
                                        {{ $company->name }} — {{ $company->signatory_name }}
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

        <!-- Hidden fields for company data -->
        <input type="hidden" name="company_name" id="hidden_company_name">
        <input type="hidden" name="signatory_name" id="hidden_signatory_name">
        <input type="hidden" name="signatory_designation" id="hidden_signatory_designation">
        <input type="hidden" name="company_phone" id="hidden_company_phone">
        <input type="hidden" name="company_email" id="hidden_company_email">
        <input type="hidden" name="company_website" id="hidden_company_website">

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
                <a href="{{ route('challans.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3">Cancel</a>
                <button type="submit" class="btn btn-primary px-4 py-2 rounded-3">Generate Challan</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.challanFormLoaded) return;
    window.challanFormLoaded = true;

    const challanTypeSelect = document.getElementById('challan_type');
    const dynamicSelection = document.getElementById('dynamic-selection');
    const autoInfoSection = document.getElementById('auto-info-section');
    const itemsContainer = document.getElementById('items-container');
    const noItemsMessage = document.getElementById('no-items-message');
    const baseUrl = window.location.origin;

    // Company Signatory Selection Handler
    const companySignatorySelect = document.getElementById('company_signatory_select');
    if (companySignatorySelect) {
        function autoFillCompanySignatory() {
            const selectedOption = companySignatorySelect.options[companySignatorySelect.selectedIndex];
            if (selectedOption && selectedOption.value) {
                const compName = selectedOption.getAttribute('data-name') || '';
                const sigName = selectedOption.getAttribute('data-signatory') || '';
                const sigDesig = selectedOption.getAttribute('data-designation') || '';
                const compPhone = selectedOption.getAttribute('data-phone') || '';
                const compEmail = selectedOption.getAttribute('data-email') || '';
                const compWebsite = selectedOption.getAttribute('data-website') || '';

                document.getElementById('hidden_company_name').value = compName;
                document.getElementById('hidden_signatory_name').value = sigName;
                document.getElementById('hidden_signatory_designation').value = sigDesig;
                document.getElementById('hidden_company_phone').value = compPhone;
                document.getElementById('hidden_company_email').value = compEmail;
                document.getElementById('hidden_company_website').value = compWebsite;

                document.getElementById('preview-company-name').textContent = compName || '-';
                document.getElementById('preview-signatory-name').textContent = sigName || '-';
                document.getElementById('preview-signatory-designation').textContent = sigDesig || '-';
                document.getElementById('preview-company-phone').textContent = compPhone || '-';
            } else {
                document.getElementById('preview-company-name').textContent = '-';
                document.getElementById('preview-signatory-name').textContent = '-';
                document.getElementById('preview-signatory-designation').textContent = '-';
                document.getElementById('preview-company-phone').textContent = '-';
            }
        }
        companySignatorySelect.addEventListener('change', autoFillCompanySignatory);
        if (companySignatorySelect.value) {
            autoFillCompanySignatory();
        }
    }

    challanTypeSelect.addEventListener('change', function() {
        const type = this.value;
        dynamicSelection.innerHTML = '';
        autoInfoSection.style.display = 'none';
        itemsContainer.innerHTML = '';
        itemsContainer.appendChild(noItemsMessage);
        noItemsMessage.style.display = 'block';

        if (type === 'sale') {
            loadSalesSelection();
        } else if (type === 'project') {
            loadProjectsSelection();
        }
    });

    function loadSalesSelection() {
        dynamicSelection.innerHTML = '<label class="form-label small text-secondary fw-semibold mb-1">Select Sale Order <span class="text-danger">*</span></label><select class="form-select border-light-subtle" id="sale_select" required><option value="">Loading Sales...</option></select>';

        fetch(`${baseUrl}/get-sales`)
            .then(res => res.json())
            .then(data => {
                const salesList = Array.isArray(data) ? data : (data.sales || []);
                const select = document.getElementById('sale_select');
                select.innerHTML = '<option value="">Select Sale Order</option>';

                if (salesList.length === 0) {
                    select.innerHTML = '<option value="">No sales orders found</option>';
                    return;
                }

                salesList.forEach(sale => {
                    const name = sale.customer_name || 'Unknown Customer';
                    select.innerHTML += `<option value="${sale.id}">#${sale.order_no} - ${name}</option>`;
                });

                select.addEventListener('change', function() {
                    const saleId = this.value;
                    if (saleId) {
                        const sale = salesList.find(s => s.id == saleId);
                        autoFillSaleData(sale);
                    } else {
                        autoInfoSection.style.display = 'none';
                        clearItems();
                    }
                });
            })
            .catch(() => {
                document.getElementById('sale_select').innerHTML = '<option value="">Error loading sales</option>';
            });
    }

    function loadProjectsSelection() {
        dynamicSelection.innerHTML = '<label class="form-label small text-secondary fw-semibold mb-1">Select Project <span class="text-danger">*</span></label><select class="form-select border-light-subtle" id="project_select" required><option value="">Loading Projects...</option></select>';

        fetch(`${baseUrl}/get-projects`)
            .then(res => res.json())
            .then(data => {
                const projectsList = Array.isArray(data) ? data : (data.projects || []);
                const select = document.getElementById('project_select');
                select.innerHTML = '<option value="">Select Project</option>';

                if (projectsList.length === 0) {
                    select.innerHTML = '<option value="">No projects found</option>';
                    return;
                }

                projectsList.forEach(proj => {
                    const name = proj.name || 'Project #' + proj.id;
                    select.innerHTML += `<option value="${proj.id}">${name} - ${proj.client_name}</option>`;
                });

                select.addEventListener('change', function() {
                    const projId = this.value;
                    if (projId) {
                        const proj = projectsList.find(p => p.id == projId);
                        autoFillProjectData(proj);
                    } else {
                        autoInfoSection.style.display = 'none';
                        clearItems();
                    }
                });
            })
            .catch(() => {
                document.getElementById('project_select').innerHTML = '<option value="">Error loading projects</option>';
            });
    }

    function autoFillSaleData(sale) {
        if (!sale) return;
        document.getElementById('selected_sale_id').value = sale.id;
        document.getElementById('selected_project_id').value = '';

        document.getElementById('recipient_organization').value = sale.customer_name || '';
        document.getElementById('recipient_address').value = sale.customer_address || '';

        document.getElementById('detail-reference').textContent = '#' + sale.order_no;
        document.getElementById('detail-date').textContent = sale.created_at || sale.date;
        document.getElementById('detail-amount').textContent = '৳' + parseFloat(sale.total_amount || 0).toFixed(2);
        document.getElementById('detail-due').textContent = '৳' + parseFloat(sale.due_payment || 0).toFixed(2);
        autoInfoSection.style.display = 'block';

        renderItems(sale.items);
    }

    function autoFillProjectData(proj) {
        if (!proj) return;
        document.getElementById('selected_project_id').value = proj.id;
        document.getElementById('selected_sale_id').value = '';

        document.getElementById('recipient_organization').value = proj.client_name || '';
        document.getElementById('recipient_address').value = proj.client_address || '';

        document.getElementById('detail-reference').textContent = proj.name;
        document.getElementById('detail-date').textContent = proj.created_at || proj.date;
        document.getElementById('detail-amount').textContent = '৳' + parseFloat(proj.total_amount || 0).toFixed(2);
        document.getElementById('detail-due').textContent = '৳' + parseFloat(proj.due_payment || 0).toFixed(2);
        autoInfoSection.style.display = 'block';

        renderItems(proj.items);
    }

    function renderItems(items) {
        if (!items || items.length === 0) {
            clearItems();
            return;
        }

        document.getElementById('items-count').textContent = items.length + ' items';

        let html = `<div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="bg-light text-secondary fs-7 text-uppercase"><tr><th class="ps-3">#</th><th>Item Description</th><th style="width: 140px;">Delivered Qty</th><th style="width: 120px;">Unit</th></tr></thead><tbody>`;

        items.forEach((item, index) => {
            html += `<tr>
                <td class="ps-3 text-muted">${index + 1}</td>
                <td><input type="text" name="items[${index}][description]" class="form-control border-light-subtle" value="${item.description}" required></td>
                <td><input type="number" name="items[${index}][quantity]" class="form-control border-light-subtle" value="${item.quantity}" min="1" required style="width: 100px;"></td>
                <td><input type="text" name="items[${index}][unit]" class="form-control border-light-subtle" value="${item.unit || 'Pcs'}" style="width: 90px;"></td>
            </tr>`;
        });

        html += `</tbody></table></div>`;
        itemsContainer.innerHTML = html;
    }

    function clearItems() {
        itemsContainer.innerHTML = '';
        itemsContainer.appendChild(noItemsMessage);
        noItemsMessage.style.display = 'block';
        document.getElementById('items-count').textContent = '0 items';
    }
});
</script>
@endpush
