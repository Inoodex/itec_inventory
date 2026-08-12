@extends('frontend.layouts.app')

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Edit Permission</h4>
                <p class="text-muted small mb-0">Modify system permission key title</p>
            </div>
            <div>
                <a href="{{ route('permission.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm">
                    <i class="fa fa-arrow-left me-2"></i>Back to Permissions
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <form action="{{ route('permission.update', $permission->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3">
                            <div class="col-md-6 col-12">
                                <label class="form-label small text-secondary fw-semibold mb-1">Permission Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control border-light-subtle" value="{{ old('name', $permission->name) }}" id="name" name="name" placeholder="Enter Permission name" required>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('permission.index') }}" class="btn btn-outline-secondary px-4 rounded-3">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 rounded-3">Update Permission</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('script')
    <script>
        ClassicEditor
            .create(document.querySelector('#editor'))
            .catch(error => {
                console.error(error);
            });
    </script>
@endsection
@endsection
