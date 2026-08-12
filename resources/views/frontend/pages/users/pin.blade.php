@extends('frontend.layouts.app')

@section('content')

<style>
    
.customer-btn-save {
  color: #fff !important;

  border: 1px solid #fe3727 !important;

  background: #fe3727 !important;

  box-shadow: inset 0 0 0 0 #fff !important;

  border-radius: 6px !important;

  padding: 11px 22px !important;

}
</style>
<div class="content container-fluid">
<div class="page-header">
						<div class="content-page-header">
							<h5> Set PIN Number</h5>
						</div>	
					</div>

            <div class="row">
                <div class="col-lg-12">
                  <div class="card">
                    @if(session('sweet_alert'))
                        <script>
                            Swal.fire({
                                icon: '{{ session('sweet_alert.type') }}',
                                title: '{{ session('sweet_alert.title') }}',
                                text: '{{ session('sweet_alert.text') }}',
                            });
                        </script>
                    @endif

                    <div class="card-header align-items-center d-flex">
                      <h4 class="card-title mb-0 flex-grow-1"></h4>
                      <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">
                            
                        </div>
                      </div>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                      <div class="live-preview">
                      <div class="row gy-4">
                        <form action="{{ route('users.pin_store') }}" method="post">
                            @csrf
                            <div class="row">
                                @foreach ($extras as $extra)
                                    <div class="col-xxl-3 col-md-6 mb-3">
                                        <label for="{{$extra->name}}" class="form-label" style="text-transform: capitalize;">{{$extra->name}}</label>
                                        <input type="text" class="form-control" value="{{$extra->value}}" id="{{$extra->name}}" name="{{$extra->name}}" placeholder="Enter {{$extra->name}}" >
                                    </div>   
                                @endforeach
                                <div class="col-12">
                                    <button type="submit" class="btn customer-btn-save">Submit</button>
                                </div> 
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

@endsection
@endsection
