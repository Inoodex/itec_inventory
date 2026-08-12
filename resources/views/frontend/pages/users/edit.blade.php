@extends('frontend.layouts.app')

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Edit User</h4>
                <p class="text-muted small mb-0">Update system user details, credentials, and assigned role</p>
            </div>
            <div>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm">
                    <i class="fa fa-arrow-left me-2"></i>Back to Users
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->
                    <!-- end card header -->
                    <div class="card-body">
                        <div class="live-preview">
                            <div class="row gy-4">
                                <form action="{{ route('users.update', $user->id) }}" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <!-- <div class="col-xxl-3 col-md-6 mb-3">
                                            <label for="name" class="form-label">Type</label>
                                            <select class="form-select mb-3" name="role_id">
                                                @foreach (userTypes() as $key => $value)
    <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
                                            </select>
                                        </div> -->
                                        <div class="col-xxl-3 col-md-6 mb-3">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" class="form-control" value="{{ old('name', $user->name) }}"
                                                id="name" name="name" placeholder="Enter User name">
                                        </div>
                                        <div class="col-xxl-3 col-md-6 mb-3">
                                            <label for="name" class="form-label">Email</label>
                                            <input type="text" class="form-control"
                                                value="{{ old('email', $user->email) }}" id="email" name="email"
                                                placeholder="Enter User email">
                                        </div>
                                        <div class="col-xxl-3 col-md-6 mb-3">
                                            <label for="name" class="form-label">Phone</label>
                                            <input type="text" class="form-control"
                                                value="{{ old('phone', $user->phone) }}" id="phone" name="phone"
                                                placeholder="Enter User phone">
                                        </div>
                                        <div class="col-xxl-3 col-md-6 mb-3">
                                            <label for="image" class="form-label">Image(366x366)</label>
                                            <input type="file" multiple class="form-control" id="image"
                                                name="images">
                                        </div>
                                        <div class="col-xxl-3 col-md-6 mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select mb-3" name="status">
                                                <option {{ $user->status == '1' ? 'selected' : '' }} value="1">Actve
                                                </option>
                                                <option {{ $user->status == '0' ? 'selected' : '' }} value="0">InActve
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-xxl-3 col-md-6 mb-3">
                                            <label for="name" class="form-label">Role</label>
                                            <select class="form-select mb-3" name="user_role" required>
                                                @foreach ($roles as $role)
                                                    <option {{ $user->role_id == $role->id ? 'selected' : '' }}
                                                        value="{{ $role->id }}">{{ $role->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    <div class="d-flex justify-content-end gap-2 mt-4">
                                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary px-4 rounded-3">Cancel</a>
                                        <button type="submit" class="btn btn-primary px-4 rounded-3">Update User</button>
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

    </div>
    <!-- container-fluid -->

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
