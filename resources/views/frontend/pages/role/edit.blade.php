@extends('frontend.layouts.app')

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Edit Role</h4>
                <p class="text-muted small mb-0">Modify role title and update access permissions</p>
            </div>
            <div>
                <a href="{{ route('role.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm">
                    <i class="fa fa-arrow-left me-2"></i>Back to Roles
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->
                    <!-- end card header -->
                    <div class="card-body">
                      <div class="live-preview">
                        <div class="row gy-4">
                            <form action="{{ route('role.update',$role->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">                                
                                    <div class="col-12 col-md-4">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" class="form-control" value="{{ old('name',$role->name) }}" id="name" name="name" placeholder="Enter User name" >
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <h5>Parmissions</h5>
                                        <hr style="margin:0px;">
                                        <div class="row mt-2">
                                            @foreach ($permissions as $item)
                                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 shadow-lg">
                                                <div class="form-check form-switch" style="padding: 0px;">
                                                    <label for="permission_{{ $item->id }}">{{ $item->name }}</label><br>
                                                    <input class="form-check-input mb-2" style="margin-left: 0.5em !important;" type="checkbox" role="switch" name="permissions[]" id="permission_{{ $item->id }}" value="{{ $item->id }}" 
                                                    @if($roleHasPermissions->pluck('permission_id')->contains($item->id))
                                                        checked
                                                    @endif
                                                    />
                                                </div>
                                            </div>
                                        @endforeach
                                        </div>
                                    </div>                                                                                                       
                                    <div class="d-flex justify-content-end gap-2 mt-4">
                                        <a href="{{ route('role.index') }}" class="btn btn-outline-secondary px-4 rounded-3">Cancel</a>
                                        <button type="submit" class="btn btn-primary px-4 rounded-3">Update Role</button>
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
