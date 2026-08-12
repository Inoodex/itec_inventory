@extends('frontend.layouts.app')
@section('content')
    @if (session('taDaAdvance') !== null && session('taDaClaim') !== null)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Auto-calculate with session data
                calculateNetSalary({{ session('taDaAdvance') }}, {{ session('taDaClaim') }});

                // Auto-select the previous selections
                @if (session('selectedEmployee'))
                    document.getElementById('employeeSelect').value = '{{ session('selectedEmployee') }}';
                @endif

                @if (session('selectedMonth'))
                    document.getElementById('monthSelect').value = '{{ session('selectedMonth') }}';
                @endif
            });
        </script>
    @endif
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Process Employee Salary</h4>
                <p class="text-muted small mb-0">Calculate net salary, allowances, deductions, and payment status</p>
            </div>
            <div>
                <a href="{{ route('salary.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm">
                    <i class="fa fa-arrow-left me-2"></i>Back to Salary List
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('salary.store') }}" id="salaryForm">
                    @csrf
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label>Month <span class="text-danger">*</span></label>
                            <select name="month" class="form-control" id="monthSelect" required>
                                <option value="">Select Month</option>
                                @php
                                    $months = [
                                        '01' => 'January',
                                        '02' => 'February',
                                        '03' => 'March',
                                        '04' => 'April',
                                        '05' => 'May',
                                        '06' => 'June',
                                        '07' => 'July',
                                        '08' => 'August',
                                        '09' => 'September',
                                        '10' => 'October',
                                        '11' => 'November',
                                        '12' => 'December',
                                    ];
                                    $currentMonth = date('m');
                                    $currentYear = date('Y');
                                @endphp
                                @foreach ($months as $key => $month)
                                    <option value="{{ $currentYear }}-{{ $key }}"
                                        {{ $key == $currentMonth ? 'selected' : '' }}>
                                        {{ $month }} {{ $currentYear }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Employee <span class="text-danger">*</span></label>
                            <select name="employee_id" class="form-control" id="employeeSelect" required>
                                <option value="">Select Employee</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}" data-salary="{{ $emp->salary }}">
                                        {{ $emp->name }} ({{ $emp->employee_id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Basic Salary</label>
                            <input type="number" id="basicSalary" name="basic_salary" class="form-control" readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Advance (Salary)</label>
                            <input type="number" id="advanceInput" name="advance" class="form-control" readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Allowance (TA/DA)</label>
                            <input type="number" name="allowance" class="form-control" id="allowanceInput" readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Deduction</label>
                            <input type="number" name="deduction" class="form-control" id="deductionInput" value="0">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Net Salary</label>
                            <input type="number" name="net_salary" class="form-control fw-bold" id="netSalary" readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Status</label>
                            <select name="payment_status" class="form-control">
                                <option value="paid">Paid</option>
                                <option value="unpaid">Unpaid</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Payment Date</label>
                            <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Notes</label>
                            <textarea name="note" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-3 mt-3">
                            <a href="{{ route('salary.index') }}" class="btn btn-outline-secondary px-4 rounded-3">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 rounded-3">Save Salary</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const employeeSelect = document.getElementById('employeeSelect');
            const basicSalaryInput = document.getElementById('basicSalary');
            const monthSelect = document.getElementById('monthSelect');

            // Basic Salary from data attribute
            employeeSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                basicSalaryInput.value = selectedOption.dataset.salary || '';

                // Fetch advance
                let employeeId = this.value;
                if (!employeeId) {
                    document.getElementById('advanceInput').value = 0;
                    resetCalculations();
                    return;
                }

                fetch(`/employee/${employeeId}/advance-sum`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('advanceInput').value = data.sum || 0;

                        // If month is selected, fetch TA/DA data via AJAX
                        if (monthSelect.value) {
                            fetchTaDaDataViaAjax(employeeId, monthSelect.value);
                        } else {
                            // Calculate without TA/DA data
                            calculateNetSalary(0, 0);
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        document.getElementById('advanceInput').value = 0;
                        calculateNetSalary(0, 0);
                    });
            });

            // Month selection for TA/DA
            monthSelect.addEventListener('change', function() {
                const employeeId = document.getElementById('employeeSelect').value;
                if (employeeId && this.value) {
                    fetchTaDaDataViaAjax(employeeId, this.value);
                }
            });

            // Deduction input
            document.getElementById('deductionInput').addEventListener('input', function() {
                const employeeId = document.getElementById('employeeSelect').value;
                const month = document.getElementById('monthSelect').value;

                if (employeeId && month) {
                    // Re-fetch TA/DA data to recalculate
                    fetchTaDaDataViaAjax(employeeId, month);
                } else {
                    // Calculate without TA/DA data
                    calculateNetSalary(0, 0);
                }
            });
        });

        // Fetch TA/DA data using AJAX (no page refresh)
        function fetchTaDaDataViaAjax(employeeId, month) {
            console.log('Fetching TA/DA data for employee:', employeeId, 'month:', month);

            // Show loading state
            document.getElementById('allowanceInput').value = 'Loading...';

            fetch('/salary/get-tada-data-ajax', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        employee_id: employeeId,
                        month: month
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('TA/DA Data received:', data);
                    calculateNetSalary(data.total_advance || 0, data.total_claim || 0);
                })
                .catch(error => {
                    console.error('Error fetching TA/DA data:', error);
                    document.getElementById('allowanceInput').value = 0;
                    calculateNetSalary(0, 0);
                });
        }

        function calculateNetSalary(taDaAdvance = 0, taDaClaim = 0) {
            const basicSalary = parseFloat(document.getElementById('basicSalary').value) || 0;
            const salaryAdvance = parseFloat(document.getElementById('advanceInput').value) || 0;
            const deduction = parseFloat(document.getElementById('deductionInput').value) || 0;

            console.log('Calculating net salary with:', {
                basicSalary,
                salaryAdvance,
                deduction,
                taDaAdvance,
                taDaClaim
            });

            // Start with: basic_salary - salary_advance - deduction
            let netSalary = basicSalary - salaryAdvance - deduction;
            let allowance = 0;

            // Apply both Advance and Claim if they exist
            if (taDaAdvance > 0) {
                netSalary -= taDaAdvance; // Deduct remaining_amount from advance
                allowance -= taDaAdvance;
            }

            if (taDaClaim > 0) {
                netSalary += taDaClaim; // Add amount from claim
                allowance += taDaClaim;
            }

            document.getElementById('allowanceInput').value = allowance;
            document.getElementById('netSalary').value = Math.max(0, netSalary).toFixed(2);

            console.log('Final Calculation - Allowance:', allowance, 'Net Salary:', netSalary);
        }

        function resetCalculations() {
            document.getElementById('allowanceInput').value = 0;
            document.getElementById('netSalary').value = 0;
            document.getElementById('deductionInput').value = 0;
        }
    </script> --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const employeeSelect = document.getElementById('employeeSelect');
            const basicSalaryInput = document.getElementById('basicSalary');
            const monthSelect = document.getElementById('monthSelect');

            // Basic Salary from data attribute
            employeeSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                basicSalaryInput.value = selectedOption.dataset.salary || '';

                let employeeId = this.value;
                let month = monthSelect.value;

                if (!employeeId) {
                    document.getElementById('advanceInput').value = 0;
                    resetCalculations();
                    return;
                }

                fetchAdvanceByMonth(employeeId, month);
            });

            // Month selection for both advance and TA/DA
            monthSelect.addEventListener('change', function() {
                const employeeId = document.getElementById('employeeSelect').value;
                const month = this.value;

                if (employeeId && month) {
                    fetchAdvanceByMonth(employeeId, month);
                    fetchTaDaDataViaAjax(employeeId, month);
                }
            });

            // Deduction input
            document.getElementById('deductionInput').addEventListener('input', function() {
                calculateNetSalaryWithCurrentData();
            });
        });

        // Fetch advance from daily_expenses table with month filter
        function fetchAdvanceByMonth(employeeId, month) {
            if (!employeeId || !month) {
                document.getElementById('advanceInput').value = 0;
                return;
            }

            fetch(`/employee/${employeeId}/advance-sum-by-month?month=${month}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('advanceInput').value = data.sum || 0;

                    // Also fetch TA/DA data
                    fetchTaDaDataViaAjax(employeeId, month);
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('advanceInput').value = 0;
                    calculateNetSalary(0, 0);
                });
        }

        // Fetch TA/DA data
        function fetchTaDaDataViaAjax(employeeId, month) {
            console.log('Fetching TA/DA data for employee:', employeeId, 'month:', month);
            document.getElementById('allowanceInput').value = 'Loading...';

            fetch('/salary/get-tada-data-ajax', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        employee_id: employeeId,
                        month: month
                    })
                })
                .then(response => response.json())
                .then(data => {
                    calculateNetSalary(data.total_advance || 0, data.total_claim || 0);
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('allowanceInput').value = 0;
                    calculateNetSalary(0, 0);
                });
        }

        function calculateNetSalary(taDaAdvance = 0, taDaClaim = 0) {
            const basicSalary = parseFloat(document.getElementById('basicSalary').value) || 0;
            const salaryAdvance = parseFloat(document.getElementById('advanceInput').value) || 0;
            const deduction = parseFloat(document.getElementById('deductionInput').value) || 0;

            let netSalary = basicSalary - salaryAdvance - deduction;
            let allowance = 0;

            if (taDaAdvance > 0) {
                netSalary -= taDaAdvance;
                allowance -= taDaAdvance;
            }

            if (taDaClaim > 0) {
                netSalary += taDaClaim;
                allowance += taDaClaim;
            }

            document.getElementById('allowanceInput').value = allowance;
            document.getElementById('netSalary').value = Math.max(0, netSalary).toFixed(2);
        }

        function calculateNetSalaryWithCurrentData() {
            const basicSalary = parseFloat(document.getElementById('basicSalary').value) || 0;
            const salaryAdvance = parseFloat(document.getElementById('advanceInput').value) || 0;
            const allowance = parseFloat(document.getElementById('allowanceInput').value) || 0;
            const deduction = parseFloat(document.getElementById('deductionInput').value) || 0;

            const netSalary = basicSalary - salaryAdvance + allowance - deduction;
            document.getElementById('netSalary').value = Math.max(0, netSalary).toFixed(2);
        }

        function resetCalculations() {
            document.getElementById('allowanceInput').value = 0;
            document.getElementById('netSalary').value = 0;
            document.getElementById('deductionInput').value = 0;
        }
    </script>
@endsection
