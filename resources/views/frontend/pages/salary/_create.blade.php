@extends('frontend.layouts.app')
@section('content')
    <div class="row justify-content-center p-3">
        <div class="col">
            <div class="card p-4 shadow">
                <h2 class=" mb-3">Create Salary</h2>
                <form method="POST" action="{{ route('salary.store') }}">
                    @csrf
                    <div class="row">
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

                        <!-- Month Selection -->
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
                                @endphp
                                @foreach ($months as $key => $month)
                                    <option value="{{ date('Y') }}-{{ $key }}">
                                        {{ $month }} {{ date('Y') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Salary Fields -->
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
                            <input type="date" name="payment_date" class="form-control"
                                value="{{ old('payment_date', $salary->payment_date ?? date('Y-m-d')) }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Notes</label>
                            <textarea name="note" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <a href="{{ route('salary.index') }}" class="btn btn-secondary">Back</a>
                        </div>

                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- <script>
        const employeeSelect = document.getElementById('employeeSelect');
        const basicSalaryInput = document.getElementById('basicSalary');

        employeeSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            basicSalaryInput.value = selectedOption.dataset.salary || '';
        });
    </script>

    <script>
        document.getElementById('employeeSelect').addEventListener('change', function() {
            let employeeId = this.value;
            if (!employeeId) {
                document.getElementById('advanceInput').value = 0;
                return;
            }

            fetch(`/employee/${employeeId}/advance-sum`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('advanceInput').value = data.sum;
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('advanceInput').value = 0;
                });
        });
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

                        // If month is selected, fetch TA/DA data
                        if (monthSelect.value) {
                            fetchTaDaData();
                        } else {
                            calculateNetSalary();
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        document.getElementById('advanceInput').value = 0;
                        calculateNetSalary();
                    });
            });

            // Month selection for TA/DA
            monthSelect.addEventListener('change', function() {
                if (employeeSelect.value && this.value) {
                    fetchTaDaData();
                }
            });

            // Deduction input
            document.getElementById('deductionInput').addEventListener('input', calculateNetSalary);
        });

        // Allowance calculation function
        function fetchTaDaData() {
            const employeeId = document.getElementById('employeeSelect').value;
            const month = document.getElementById('monthSelect').value;

            if (!employeeId || !month) return;

            fetch(`/api/tada-calculation?employee_id=${employeeId}&month=${month}`)
                .then(response => response.json())
                .then(data => {
                    // Store TA/DA values for net salary calculation
                    document.getElementById('allowanceInput').setAttribute('data-advance', data.total_advance || 0);
                    document.getElementById('allowanceInput').setAttribute('data-claim', data.total_claim || 0);

                    calculateNetSalary();
                })
                .catch(error => {
                    console.error('Error fetching TA/DA data:', error);
                    document.getElementById('allowanceInput').setAttribute('data-advance', 0);
                    document.getElementById('allowanceInput').setAttribute('data-claim', 0);
                    calculateNetSalary();
                });
        }

        function calculateNetSalary() {
            const basicSalary = parseFloat(document.getElementById('basicSalary').value) || 0;
            const salaryAdvance = parseFloat(document.getElementById('advanceInput').value) || 0;
            const deduction = parseFloat(document.getElementById('deductionInput').value) || 0;

            // Get TA/DA values
            const taDaAdvance = parseFloat(document.getElementById('allowanceInput').getAttribute('data-advance')) || 0;
            const taDaClaim = parseFloat(document.getElementById('allowanceInput').getAttribute('data-claim')) || 0;

            // Formula: basic - advance - (remaining_amount from ta_da or + amount from ta_da) - deduction
            let netSalary = basicSalary - salaryAdvance - deduction;

            // Apply TA/DA logic
            if (taDaAdvance > 0) {
                // Deduct remaining_amount from advance
                netSalary -= taDaAdvance;
                document.getElementById('allowanceInput').value = -taDaAdvance;
            } else if (taDaClaim > 0) {
                // Add amount from claim
                netSalary += taDaClaim;
                document.getElementById('allowanceInput').value = taDaClaim;
            } else {
                document.getElementById('allowanceInput').value = 0;
            }

            document.getElementById('netSalary').value = Math.max(0, netSalary).toFixed(2);
        }

        function resetCalculations() {
            document.getElementById('allowanceInput').value = 0;
            document.getElementById('allowanceInput').setAttribute('data-advance', 0);
            document.getElementById('allowanceInput').setAttribute('data-claim', 0);
            document.getElementById('netSalary').value = 0;
            document.getElementById('deductionInput').value = 0;
        }
    </script>

    <script>
        // TA/DA Calculation
        function fetchTaDaData() {
            const employeeId = document.getElementById('employeeSelect').value;
            const month = document.getElementById('monthSelect').value;

            if (!employeeId || !month) return;

            fetch(`/api/tada-calculation?employee_id=${employeeId}&month=${month}`)
                .then(response => response.json())
                .then(data => {
                    console.log('TA/DA Data:', data); // Debug log
                    calculateNetSalaryWithTaDa(data.total_advance, data.total_claim);
                })
                .catch(error => {
                    console.error('Error:', error);
                    calculateNetSalaryWithTaDa(0, 0);
                });
        }

        function calculateNetSalaryWithTaDa(taDaAdvance, taDaClaim) {
            const basicSalary = parseFloat(document.getElementById('basicSalary').value) || 0;
            const salaryAdvance = parseFloat(document.getElementById('advanceInput').value) || 0;
            const deduction = parseFloat(document.getElementById('deductionInput').value) || 0;

            console.log('TA/DA Advance:', taDaAdvance, 'TA/DA Claim:', taDaClaim); // Debug log

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

            console.log('Final Allowance:', allowance, 'Net Salary:', netSalary); // Debug log
        }

        // Add event listeners
        document.getElementById('employeeSelect').addEventListener('change', function() {
            if (this.value && document.getElementById('monthSelect').value) {
                fetchTaDaData();
            }
        });

        document.getElementById('monthSelect').addEventListener('change', function() {
            if (document.getElementById('employeeSelect').value && this.value) {
                fetchTaDaData();
            }
        });

        document.getElementById('deductionInput').addEventListener('input', function() {
            if (document.getElementById('employeeSelect').value && document.getElementById('monthSelect').value) {
                fetchTaDaData();
            }
        });
    </script>
@endsection
