@extends('frontend.layouts.app')

@push('styles')
    <style>
        .btn-action-icon {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #dbe2ea !important;
            border-radius: 8px !important;
            background-color: #ffffff !important;
            color: #555e6d !important;
            padding: 0;
            transition: all 0.2s ease;
        }

        .btn-action-icon:hover {
            background-color: #7638ff !important;
            color: #ffffff !important;
            border-color: #7638ff !important;
        }

        .table-responsive {
            overflow: visible !important;
        }

        .account-row {
            transition: background-color 0.15s ease;
        }

        .account-row:hover {
            background-color: #f8fafc !important;
        }

        .account-level-1 {
            background-color: #f8fafc;
            font-weight: 700;
            border-top: 1px solid #e2e8f0;
        }

        .account-level-2 {
            background-color: #ffffff;
            font-weight: 600;
        }

        .account-level-3 {
            background-color: #ffffff;
            color: #475569;
        }

        .tree-toggle-btn {
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            background-color: rgba(118, 56, 255, 0.08);
            color: #7638ff;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid rgba(118, 56, 255, 0.18);
            user-select: none;
            flex-shrink: 0;
        }

        .tree-toggle-btn:hover {
            background-color: #7638ff;
            color: #ffffff;
        }

        .tree-toggle-icon {
            font-size: 11px;
            transition: transform 0.2s ease;
        }

        .tree-toggle-btn.collapsed .tree-toggle-icon {
            transform: rotate(-90deg);
        }

        .account-title-clickable {
            cursor: pointer;
            user-select: none;
        }

        .account-title-clickable:hover {
            color: #7638ff !important;
            text-decoration: underline;
        }

        .tree-spacer {
            width: 24px;
            display: inline-block;
            flex-shrink: 0;
        }

        .badge-subcount {
            background-color: #e2e8f0;
            color: #475569;
            font-size: 10px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 12px;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title font-weight-bold" style="color: #1e293b;">Chart of Accounts</h3>
                </div>
                <div class="col-auto d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm"
                        id="expandAllAccounts">
                        <i class="fas fa-expand-alt me-1"></i> Expand All
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm"
                        id="collapseAllAccounts">
                        <i class="fas fa-compress-alt me-1"></i> Collapse All
                    </button>
                    <a href="{{ route('chart-of-accounts.create') }}"
                        class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                        <i class="fas fa-plus me-1"></i> New Account
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('chart-of-accounts.index') }}" class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control form-control-sm"
                            placeholder="Search by Code or Title..." value="{{ $search }}">
                    </div>
                    <div class="col-md-3">
                        <select name="type" class="form-select form-select-sm">
                            <option value="">All Account Classes</option>
                            <option value="asset" {{ $type === 'asset' ? 'selected' : '' }}>Assets (1000)</option>
                            <option value="liability" {{ $type === 'liability' ? 'selected' : '' }}>Liabilities (2000)
                            </option>
                            <option value="equity" {{ $type === 'equity' ? 'selected' : '' }}>Equity (3000)</option>
                            <option value="revenue" {{ $type === 'revenue' ? 'selected' : '' }}>Revenue (4000)</option>
                            <option value="expense" {{ $type === 'expense' ? 'selected' : '' }}>Expenses (5000)</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fas fa-search me-1"></i>
                            Filter</button>
                    </div>
                    <div class="col-md-3 text-end">
                        <a href="{{ route('chart-of-accounts.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tree View Table -->
        <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: visible !important;">
            <div class="card-body p-0" style="overflow: visible !important;">
                <div class="table-responsive" style="overflow: visible !important; min-height: 220px;">
                    <table class="table table-hover align-middle mb-0" id="coaTable">
                        <thead
                            style="background-color: #1e293b; color: #ffffff; font-size: 11px; text-transform: uppercase;">
                            <tr>
                                <th style="width: 160px;" class="ps-3">Account Code</th>
                                <th>Account Name & Hierarchy (Click to Expand)</th>
                                <th style="width: 140px;">Class</th>
                                <th class="text-end" style="width: 150px;">Balance</th>
                                <th class="text-center" style="width: 100px;">Status</th>
                                <th class="text-end pe-4" style="width: 80px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($accounts as $acc)
                                @php
                                    $balance = $acc->calculateBalance();
                                    $indent = ($acc->level - 1) * 24;
                                    $childCount = $accounts->where('parent_id', $acc->id)->count();
                                    $hasChildren = $childCount > 0;
                                @endphp
                                <tr class="account-row account-level-{{ $acc->level }}" data-id="{{ $acc->id }}"
                                    data-parent-id="{{ $acc->parent_id ?? 0 }}" data-level="{{ $acc->level }}">
                                    <td class="fw-bold ps-3 font-monospace" style="color: #334155;">{{ $acc->account_code }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center" style="padding-left: {{ $indent }}px;">
                                            @if($hasChildren)
                                                <span class="tree-toggle-btn me-2" data-id="{{ $acc->id }}"
                                                    data-level="{{ $acc->level }}" title="Click to Expand / Collapse">
                                                    <i class="fas fa-chevron-down tree-toggle-icon"></i>
                                                </span>
                                            @else
                                                <span class="tree-spacer me-2 text-center text-muted">
                                                    @if($acc->level > 1)
                                                        <i class="fas fa-level-up-alt fa-rotate-90"
                                                            style="font-size: 10px; opacity: 0.5;"></i>
                                                    @endif
                                                </span>
                                            @endif

                                            <span
                                                class="{{ $hasChildren ? 'account-title-clickable' : '' }} {{ $acc->level == 1 ? 'fw-bold text-dark fs-6' : ($acc->level == 2 ? 'fw-semibold text-dark' : 'text-secondary') }}"
                                                @if($hasChildren) data-id="{{ $acc->id }}" title="Click to Expand / Collapse"
                                                @endif>
                                                {{ $acc->account_name }}
                                            </span>

                                            @if($hasChildren)
                                                <span class="badge badge-subcount ms-2"
                                                    title="{{ $childCount }} sub-accounts attached">
                                                    {{ $childCount }} sub
                                                </span>
                                            @endif

                                            @if($acc->is_system)
                                                <span class="badge bg-light text-muted border ms-2"
                                                    style="font-size: 9px;">SYSTEM</span>
                                            @endif

                                            @if($acc->bankDetail)
                                                <span class="badge bg-info-light text-info ms-2" style="font-size: 9px;">
                                                    <i class="fe fe-credit-card me-1"></i> Bank #{{ $acc->bank_detail_id }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge 
                                                {{ $acc->account_type === 'asset' ? 'bg-success' : '' }}
                                                {{ $acc->account_type === 'liability' ? 'bg-danger' : '' }}
                                                {{ $acc->account_type === 'equity' ? 'bg-primary' : '' }}
                                                {{ $acc->account_type === 'revenue' ? 'bg-info text-dark' : '' }}
                                                {{ $acc->account_type === 'expense' ? 'bg-warning text-dark' : '' }}
                                                text-uppercase" style="font-size: 10px;">
                                            {{ $acc->account_type }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold {{ $balance < 0 ? 'text-danger' : 'text-dark' }}">
                                        ৳{{ number_format($balance, 2) }}
                                    </td>
                                    <td class="text-center">
                                        @if($acc->is_active)
                                            <span class="badge bg-success-light text-success" style="font-size: 10px;">Active</span>
                                        @else
                                            <span class="badge bg-danger-light text-danger" style="font-size: 10px;">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="dropdown">
                                            <a href="javascript:void(0)" class="btn-action-icon shadow-none"
                                                data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}'
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                                <li>
                                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2"
                                                        href="{{ route('ledger.index', ['account_id' => $acc->id]) }}">
                                                        <i class="fe fe-book text-info"></i>
                                                        <span>General Ledger</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2"
                                                        href="{{ route('chart-of-accounts.create', ['parent_id' => $acc->id]) }}">
                                                        <i class="fe fe-plus text-success"></i>
                                                        <span>Add Sub-Account</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2"
                                                        href="{{ route('chart-of-accounts.edit', $acc->id) }}">
                                                        <i class="fe fe-edit text-primary"></i>
                                                        <span>Edit Account</span>
                                                    </a>
                                                </li>
                                                @if(!$acc->is_system)
                                                    <li>
                                                        <hr class="dropdown-divider opacity-50">
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger"
                                                            href="javascript:void(0)"
                                                            onclick="if(confirm('Are you sure you want to delete this account?')) { document.getElementById('accountDelete{{ $acc->id }}').submit(); }">
                                                            <i class="fe fe-trash-2 text-danger"></i>
                                                            <span>Delete Account</span>
                                                        </a>
                                                        <form id="accountDelete{{ $acc->id }}"
                                                            action="{{ route('chart-of-accounts.destroy', $acc->id) }}"
                                                            method="POST" class="d-none">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">No accounts match the selected criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Helper function to recursively collect all descendant row IDs
            function getDescendantIds(parentId) {
                let descendants = [];
                const children = document.querySelectorAll(`tr.account-row[data-parent-id="${parentId}"]`);
                children.forEach(child => {
                    const childId = child.getAttribute('data-id');
                    descendants.push(childId);
                    descendants = descendants.concat(getDescendantIds(childId));
                });
                return descendants;
            }

            // Toggle a single parent node and its subtree
            function toggleAccountNode(accountId, forceState = null) {
                const toggleBtn = document.querySelector(`.tree-toggle-btn[data-id="${accountId}"]`);
                const parentRow = document.querySelector(`tr.account-row[data-id="${accountId}"]`);
                if (!parentRow) return;

                const isCurrentlyCollapsed = toggleBtn ? toggleBtn.classList.contains('collapsed') : false;
                const shouldCollapse = forceState !== null ? (forceState === 'collapse') : !isCurrentlyCollapsed;

                if (shouldCollapse) {
                    if (toggleBtn) toggleBtn.classList.add('collapsed');
                    // Hide all descendants
                    const descendantIds = getDescendantIds(accountId);
                    descendantIds.forEach(id => {
                        const row = document.querySelector(`tr.account-row[data-id="${id}"]`);
                        if (row) {
                            row.style.display = 'none';
                        }
                    });
                } else {
                    if (toggleBtn) toggleBtn.classList.remove('collapsed');
                    // Show only direct children, and restore sub-children if their own toggle is expanded
                    showDirectChildren(accountId);
                }
            }

            function showDirectChildren(parentId) {
                const children = document.querySelectorAll(`tr.account-row[data-parent-id="${parentId}"]`);
                children.forEach(childRow => {
                    childRow.style.display = '';
                    const childId = childRow.getAttribute('data-id');
                    const childToggle = document.querySelector(`.tree-toggle-btn[data-id="${childId}"]`);
                    if (childToggle && !childToggle.classList.contains('collapsed')) {
                        showDirectChildren(childId);
                    }
                });
            }

            // Click handler on chevron toggle button
            document.querySelectorAll('.tree-toggle-btn').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const id = this.getAttribute('data-id');
                    toggleAccountNode(id);
                });
            });

            // Click handler on account name text
            document.querySelectorAll('.account-title-clickable').forEach(title => {
                title.addEventListener('click', function (e) {
                    const id = this.getAttribute('data-id');
                    toggleAccountNode(id);
                });
            });

            // Expand All button handler
            const expandAllBtn = document.getElementById('expandAllAccounts');
            if (expandAllBtn) {
                expandAllBtn.addEventListener('click', function () {
                    document.querySelectorAll('tr.account-row').forEach(row => row.style.display = '');
                    document.querySelectorAll('.tree-toggle-btn').forEach(btn => btn.classList.remove('collapsed'));
                });
            }

            // Collapse All button handler
            const collapseAllBtn = document.getElementById('collapseAllAccounts');
            if (collapseAllBtn) {
                collapseAllBtn.addEventListener('click', function () {
                    document.querySelectorAll('.tree-toggle-btn').forEach(btn => {
                        const level = btn.getAttribute('data-level');
                        if (level == 1) {
                            toggleAccountNode(btn.getAttribute('data-id'), 'collapse');
                        }
                    });
                });
            }
        });
    </script>
@endsection