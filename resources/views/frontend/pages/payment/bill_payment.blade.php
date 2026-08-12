@extends('frontend.layouts.app')
@section('content')
    <div class="content container-fluid">
        <!-- Service/Sale Info Card -->
        @if ($bill)
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title mb-3">
                                @if ($payment_for == '1')
                                    <i class="fas fa-tools me-2"></i>Service Payment Details
                                @else
                                    <i class="fas fa-shopping-cart me-2"></i>Sale Payment Details
                                @endif
                            </h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <p class="mb-1"><strong>Customer:</strong> {{ $bill->name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-1"><strong>Total Bill:</strong> ${{ number_format($bill->bill ?? 0, 2) }}
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-1"><strong>Paid Amount:</strong>
                                        ${{ number_format($bill->paid_amount ?? 0, 2) }}</p>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-1"><strong>Due Amount:</strong>
                                        <span class="badge bg-{{ ($bill->due_amount ?? 0) > 0 ? 'warning' : 'success' }}">
                                            ${{ number_format($bill->due_amount ?? 0, 2) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            @if ($bill->remarks)
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <div class="alert alert-info mb-0">
                                            <strong><i class="fas fa-sticky-note me-1"></i>Service Remarks:</strong>
                                            {{ $bill->remarks }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Page Header -->
        <div class="page-header">
            <div class="content-page-header">
                <h5>Payment History</h5>

                <div class="list-btn">
                    <ul class="filter-list">
                        <li>
                            <a class="btn btn-primary" href="javascript:void(0)" data-bs-toggle="modal"
                                data-bs-target="#add-payment-modal">
                                <i class="fa fa-plus-circle me-2" aria-hidden="true"></i>Add Payments </a>
                        </li>
                    </ul>

                    <div id="add-payment-modal" class="modal fade" tabindex="-1" style="display: none;" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="text-center mt-2 mb-4">
                                        <div class="auth-logo">
                                            <a href="{{ route('index') }}" class="logo logo-dark">
                                                <span class="logo-lg">
                                                    <img src="{{ asset('assets/img/logo.png') }}" alt="Logo"
                                                        height="42">
                                                </span>
                                            </a>
                                        </div>
                                    </div>

                                    <form class="px-3" method="post" action="{{ route('add.payment') }}">
                                        @csrf
                                        <div class="mb-3">
                                            <input type="text" name="id" value="{{ $id }}" hidden>
                                            <input type="text" name="payment_for" value="{{ $payment_for }}" hidden>
                                            <label for="modalFullName" class="form-label">Payment Method <span
                                                    class="text-danger">*</span></label>
                                            <Select class="form-select" name="payment_method_id" required>
                                                <option value="">--Select--</option>
                                                @foreach (paymentMethods() as $key => $name)
                                                    <option value="{{ $key }}"
                                                        {{ old('payment_method_id') == $key ? 'selected' : '' }}>
                                                        {{ $name }}</option>
                                                @endforeach
                                            </Select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="modalPhoneNumber" class="form-label">Amount <span
                                                    class="text-danger">*</span></label>
                                            <input class="form-control" type="text" name="amount" value=""
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="paymentRemarks" class="form-label">Remarks</label>
                                            <textarea class="form-control" name="remarks" id="paymentRemarks" rows="3" placeholder="Add payment remarks..."></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <button type="submit" class="btn btn-primary w-100">Submit Payment</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- /Page Header -->
        <!-- Search Filter -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card-table">
                    <div class="card-body">
                        <div class="table-responsive">
                            <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                <table class="table table-center table-hover datatable dataTable no-footer"
                                    id="DataTables_Table_0" role="grid" aria-describedby="DataTables_Table_0_info">
                                    <thead class="thead-light">
                                        <tr role="row">
                                            <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0"
                                                rowspan="1" colspan="1" aria-sort="ascending"
                                                aria-label="#: activate to sort column descending">#</th>
                                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0"
                                                rowspan="1" colspan="1"
                                                aria-label="Name: activate to sort column ascending">Date</th>
                                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0"
                                                rowspan="1" colspan="1"
                                                aria-label="Name: activate to sort column ascending">Payment Method</th>
                                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0"
                                                rowspan="1" colspan="1"
                                                aria-label="Phone: activate to sort column ascending">Amount</th>
                                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0"
                                                rowspan="1" colspan="1"
                                                aria-label="Remarks: activate to sort column ascending">Remarks</th>
                                            <th class="no-sort sorting_disabled" rowspan="1" colspan="1"
                                                aria-label="Actions">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($payments as $payment)
                                            <tr role="row" class="odd">
                                                <td class="sorting_1">{{ $loop->index + 1 }}</td>
                                                <td>
                                                    <h2 class="table-avatar">
                                                        <span>{{ $payment->created_at->format('Y-m-d') }}</span>
                                                    </h2>
                                                </td>
                                                <td>
                                                    <h2 class="table-avatar">
                                                        <span>{{ getArrayData(paymentMethods(), $payment->payment_method) }}</span>
                                                    </h2>
                                                </td>
                                                <td>
                                                    <h2 class="table-avatar"> <span>{{ $payment->amount }}</span></h2>
                                                </td>
                                                <td>
                                                    <h2 class="table-avatar"> <span>{{ $payment->remarks ?? '-' }}</span>
                                                    </h2>
                                                </td>
                                                <td class="d-flex align-items-center">
                                                    <div class="dropdown dropdown-action">
                                                        <a href="#" class=" btn-action-icon "
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <ul>
                                                                <li>
                                                                    <a class="dropdown-item" href="javascript:void(0)"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#edit-payment-modal{{ $payment->id }}">
                                                                        <i class="far fa-edit me-2"></i>Edit </a>
                                                                </li>
                                                                <li>
                                                                    <a onclick="if (confirm('Are you sure to delete the payment?')) { document.getElementById('serviceDelete{{ $payment->id }}').submit(); }"
                                                                        class="dropdown-item" href="javascript:void(0)">
                                                                        <i class="far fa-edit me-2"></i>Delete </a>
                                                                    <form id="serviceDelete{{ $payment->id }}"
                                                                        action="{{ route('delete.payment', $payment->id) }}"
                                                                        method="post">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                    </form>
                                                                </li>
                                                            </ul>

                                                        </div>
                                                    </div>
                                                </td>
                                                <div id="edit-payment-modal{{ $payment->id }}" class="modal fade"
                                                    tabindex="-1" style="display: none;" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-body">
                                                                <div class="text-center mt-2 mb-4">
                                                                    <div class="auth-logo">
                                                                        <a href="{{ route('index') }}"
                                                                            class="logo logo-dark">
                                                                            <span class="logo-lg">
                                                                                <img src="{{ asset('assets/img/logo.png') }}"
                                                                                    alt="Logo" height="42">
                                                                            </span>
                                                                        </a>
                                                                    </div>
                                                                </div>

                                                                <form class="px-3" method="post"
                                                                    action="{{ route('update.payment', $payment->id) }}">
                                                                    @csrf
                                                                    <div class="mb-3">
                                                                        <label for="modalFullName"
                                                                            class="form-label">Payment Method <span
                                                                                class="text-danger">*</span></label>
                                                                        <Select class="form-select"
                                                                            name="payment_method_id" required>
                                                                            <option value="">--Select--</option>
                                                                            @foreach (paymentMethods() as $key => $name)
                                                                                <option value="{{ $key }}"
                                                                                    {{ $payment->payment_method == $key ? 'selected' : '' }}>
                                                                                    {{ $name }}</option>
                                                                            @endforeach
                                                                        </Select>
                                                                    </div>

                                                                    <div class="mb-3">
                                                                        <label for="modalPhoneNumber"
                                                                            class="form-label">Amount <span
                                                                                class="text-danger">*</span></label>
                                                                        <input class="form-control" type="text"
                                                                            name="amount" value="{{ $payment->amount }}"
                                                                            required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="editPaymentRemarks{{ $payment->id }}"
                                                                            class="form-label">Remarks</label>
                                                                        <textarea class="form-control" name="remarks" id="editPaymentRemarks{{ $payment->id }}" rows="2"
                                                                            placeholder="Add payment remarks...">{{ $payment->remarks }}</textarea>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <button type="submit"
                                                                            class="btn btn-primary">Submit</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div class="dataTables_length" id="DataTables_Table_0_length">
                                    <label>
                                        <select name="DataTables_Table_0_length" aria-controls="DataTables_Table_0"
                                            class="custom-select custom-select-sm form-control form-control-sm">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="dataTables_paginate paging_simple_numbers" id="DataTables_Table_0_paginate">
                                    <ul class="pagination">
                                        <li class="paginate_button page-item previous disabled"
                                            id="DataTables_Table_0_previous">
                                            <a href="#" aria-controls="DataTables_Table_0" data-dt-idx="0"
                                                tabindex="0" class="page-link">
                                                <i class="fa fa-angle-double-left me-2"></i> Previous </a>
                                        </li>
                                        <li class="paginate_button page-item active">
                                            <a href="#" aria-controls="DataTables_Table_0" data-dt-idx="1"
                                                tabindex="0" class="page-link">1</a>
                                        </li>
                                        <li class="paginate_button page-item next disabled" id="DataTables_Table_0_next">
                                            <a href="#" aria-controls="DataTables_Table_0" data-dt-idx="2"
                                                tabindex="0" class="page-link">Next <i
                                                    class=" fa fa-angle-double-right ms-2"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="dataTables_info" id="DataTables_Table_0_info" role="status"
                                    aria-live="polite">Showing 1 to 6 of 6 entries</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
