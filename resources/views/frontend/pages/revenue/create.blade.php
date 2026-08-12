@extends('frontend.layouts.app')
@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="container">
                <h2>Revenue Management</h2>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="form-group">
                    <label for="amount">Amount *</label>
                    <input type="number" step="0.01" name="amount" id="amount" class="form-control"
                        value="{{ old('amount', $revenue->amount ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label for="date">Date *</label>
                    <input type="date" name="date" id="date" class="form-control"
                        value="{{ old('date', $revenue->date ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label for="source">Source *</label>
                    <input type="text" name="source" id="source" class="form-control"
                        value="{{ old('source', $revenue->source ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label for="category">Category *</label>
                    <input type="text" name="category" id="category" class="form-control"
                        value="{{ old('category', $revenue->category ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $revenue->description ?? '') }}</textarea>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </div>
    </div>
@endsection
