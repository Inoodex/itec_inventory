@extends('frontend.layouts.app')

@section('title')
@yield('title', 'Error - Inoodex Inventory')
@endsection

@section('content')
<style>
    .error-card-container {
        min-height: calc(100vh - 140px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 30px 15px;
    }
    .error-card {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 16px;
        padding: 48px 36px;
        max-width: 500px;
        width: 100%;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
    }
    .error-code-text {
        font-size: 3.8rem;
        font-weight: 800;
        color: #7638ff;
        line-height: 1;
        margin-bottom: 8px;
        letter-spacing: -0.03em;
    }
    .error-tag-badge {
        display: inline-block;
        padding: 4px 14px;
        background: rgba(118, 56, 255, 0.08);
        color: #7638ff;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 20px;
    }
    .error-heading-text {
        font-size: 1.45rem;
        font-weight: 700;
        color: #2c3038;
        margin-bottom: 12px;
    }
    .error-message-text {
        font-size: 0.95rem;
        color: #6c757d;
        line-height: 1.5;
        margin-bottom: 28px;
    }
    .btn-error-primary {
        background-color: #7638ff;
        color: #ffffff !important;
        font-weight: 600;
        padding: 10px 22px;
        border-radius: 8px;
        border: none;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .btn-error-primary:hover {
        background-color: #6226e3;
        color: #ffffff !important;
    }
    .btn-error-secondary {
        background-color: #ffffff;
        color: #2c3038 !important;
        font-weight: 600;
        padding: 10px 22px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .btn-error-secondary:hover {
        background-color: #f8f9fa;
        color: #2c3038 !important;
    }
</style>

<div class="error-card-container">
    <div class="error-card">
        <div class="error-code-text">@yield('code', '500')</div>
        <div>
            <span class="error-tag-badge">@yield('badge', 'Error')</span>
        </div>

        <h2 class="error-heading-text">@yield('heading', 'Something went wrong')</h2>
        <p class="error-message-text">@yield('message', 'An unexpected error occurred. Please return to the dashboard.')</p>

        <div class="d-flex gap-2 justify-content-center flex-wrap">
            <a href="{{ url('/') }}" class="btn-error-primary">Back to Dashboard</a>
            <button onclick="window.history.back()" class="btn-error-secondary">Go Back</button>
        </div>
    </div>
</div>
@endsection
