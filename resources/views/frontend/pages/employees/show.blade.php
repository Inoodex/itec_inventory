@extends('frontend.layouts.app')
@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="content-page-header d-flex justify-content-between align-items-center">
                <h5>Employee Details</h5>
                <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-sm">Back</a>
            </div>

            <div class="card p-4 shadow">
                <div class="row">

                    <div class="col-md-4 text-center">
                        @if ($employee->image)
                            <img src="{{ asset('uploads/employees/' . $employee->image) }}" class="rounded mb-3"
                                width="200">
                        @else
                            <img src="{{ asset('default.png') }}" class="rounded mb-3" width="200">
                        @endif
                    </div>

                    <div class="col-md-8">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width:200px;">Employee ID</th>
                                <td>{{ $employee->employee_id }}</td>
                            </tr>

                            <tr>
                                <th>Name</th>
                                <td>{{ $employee->name }}</td>
                            </tr>

                            <tr>
                                <th>Email</th>
                                <td>{{ $employee->email ?? '-' }}</td>
                            </tr>

                            <tr>
                                <th>Phone</th>
                                <td>{{ $employee->phone }}</td>
                            </tr>

                            <tr>
                                <th>Designation</th>
                                <td>{{ $employee->designation ?? '-' }}</td>
                            </tr>

                            <tr>
                                <th>Join Date</th>
                                <td>{{ $employee->join_date ? date('d M, Y', strtotime($employee->join_date)) : '-' }}</td>
                            </tr>

                            <tr>
                                <th>Salary (Basic)</th>
                                <td>{{ number_format($employee->salary, 2) }}</td>
                            </tr>

                            <tr>
                                <th>Status</th>
                                <td>
                                    @if ($employee->status == 'active')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
