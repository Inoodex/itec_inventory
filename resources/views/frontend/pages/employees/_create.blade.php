@extends('frontend.layouts.app')
@section('content')
    <div class="content container-fluid">
        <div class="card mb-0">
            <div class="card-body">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="content-page-header">
                        <h5>Add Employee</h5>
                    </div>
                </div>
                <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label>Employee ID</label>
                            <input type="text" name="employee_id" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Designation</label>
                            <input type="text" name="designation" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Join Date</label>
                            <input type="date" name="join_date" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Salary (basic)</label>
                            <input type="number" step="0.01" name="salary" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Photo</label>
                            <input type="file" name="image" class="form-control">
                        </div>

                    </div>

                    <button class="btn btn-danger">Save Employee</button>
                </form>
            </div>
        </div>
    </div>
@endsection
