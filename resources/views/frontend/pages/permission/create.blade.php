@extends('frontend.layouts.app')

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Create New Permission</h4>
                <p class="text-muted small mb-0">Define permission key for authorization rules</p>
            </div>
            <div>
                <a href="{{ route('permission.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm">
                    <i class="fa fa-arrow-left me-2"></i>Back to Permissions
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->
                    <!-- end card header -->
                    <div class="card-body">
                        <div class="live-preview">
                            <div class="row gy-4">
                                <form action="{{ route('permission.store') }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-xxl-3 col-md-6 mb-3">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" class="form-control" value="{{ old('name') }}"
                                                id="name" name="name" placeholder="Enter Permission name">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end gap-2 mt-4">
                                        <a href="{{ route('permission.index') }}" class="btn btn-outline-secondary px-4 rounded-3">Cancel</a>
                                        <button type="submit" class="btn btn-primary px-4 rounded-3">Create Permission</button>
                                    </div>
                                </form>

                            </div>
                            <!--end row-->
                        </div>
                    </div>
                </div>
            </div>
            <!--end col-->
        </div>
        <!-- container-fluid -->
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
