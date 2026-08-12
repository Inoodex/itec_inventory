@extends('frontend.layouts.app')
@section('content')
    <div class="content container-fluid col-sm-10">
        <div class="page-header d-flex justify-content-between align-items-center">
            <h5>My TA/DA List</h5>
            {{-- <a href="{{ route('employee.tada.create') }}" class="btn btn-primary">+ New Request</a> --}}
        </div>

        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Payment Type</th>
                    <th>Amount</th>
                    <th>Used Amount</th>
                    <th>Remaining Amount</th>
                    <th>Purpose</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tadas as $key => $ta)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($ta->date)->format('d M Y') }}</td>
                        <td>{{ $ta->type }}</td>
                        <td>{{ ucfirst($ta->payment_type) }}</td>
                        <td>{{ number_format($ta->amount, 2) }}</td>
                        <td>{{ $ta->used_amount }}</td>
                        <td>{{ $ta->remaining_amount }}</td>
                        <td>{{ $ta->purpose }}</td>
                        <td class="d-flex align-items-center">
                            <div class="dropdown dropdown-action">
                                <a href="#" class="btn-action-icon" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <ul>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('employee.tada.edit', $ta->id) }}">
                                                <i class="far fa-edit me-2"></i>Submit TA/DA
                                            </a>
                                        </li>

                                        {{-- <li>
                                            <a onclick="if (confirm('Are you sure to delete the customer?')) { document.getElementById('delete{{ $ta->id }}').submit(); }"
                                                class="dropdown-item" href="javascript:void(0)">
                                                <i class="far fa-trash-alt me-2"></i>Delete
                                            </a>
                                            <form id="delete{{ $ta->id }}"
                                                action="{{ route('ta-da.destroy', $ta->id) }}" method="POST"
                                                style="display:none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </li> --}}
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No records found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
