@extends('frontend.layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card p-4 shadow">
            <h2 class="mb-3">Edit Expense Category</h2>
            <form method="POST" action="{{ route('expense-categories.update', $expenseCategory->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" id="name" value="{{ $expenseCategory->name }}" required>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select js-example-basic-single" name="status" id="status" required>
                        <option value="1" {{ $expenseCategory->status == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ $expenseCategory->status == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="text-left">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.js-example-basic-single').select2();
    });
</script>
@endsection
