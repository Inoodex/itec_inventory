<style>
    .flash-toast-wrapper {
        position: fixed;
        bottom: 24px;
        right: 24px;
        top: auto;
        z-index: 999999;
        display: flex;
        flex-direction: column-reverse;
        align-items: flex-end;
        pointer-events: none;
        max-width: calc(100vw - 48px);
    }
    
    .toast-card {
        pointer-events: auto;
        position: relative;
        overflow: hidden;
        min-width: 280px;
        max-width: 420px;
        width: auto !important;
        background: #1e293b;
        color: #f8fafc;
        border-radius: 12px;
        box-shadow: 0 14px 35px rgba(0, 0, 0, 0.25), 0 4px 12px rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.08);
        padding: 12px 16px;
        animation: toastEntrance 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        transition: all 0.25s ease;
    }

    .toast-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.3);
    }

    .toast-card-success { border-left: 4px solid #10b981; }
    .toast-card-error   { border-left: 4px solid #ef4444; }
    .toast-card-warning { border-left: 4px solid #f59e0b; }
    .toast-card-info    { border-left: 4px solid #3b82f6; }

    .toast-icon-badge {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .toast-card-success .toast-icon-badge { background: rgba(16, 185, 129, 0.15); color: #10b981; }
    .toast-card-error   .toast-icon-badge { background: rgba(239, 68, 68, 0.15);  color: #ef4444; }
    .toast-card-warning .toast-icon-badge { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
    .toast-card-info    .toast-icon-badge { background: rgba(59, 130, 246, 0.15);  color: #3b82f6; }

    .toast-close-btn {
        background: transparent;
        border: none;
        color: #94a3b8;
        font-size: 14px;
        cursor: pointer;
        padding: 4px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
        margin-left: 12px;
    }
    .toast-close-btn:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.1);
    }

    .toast-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        width: 100%;
        background: rgba(255, 255, 255, 0.1);
    }

    .toast-progress-bar {
        height: 100%;
        width: 100%;
        animation: toastProgress 4.5s linear forwards;
    }

    .toast-card-success .toast-progress-bar { background: #10b981; }
    .toast-card-error   .toast-progress-bar { background: #ef4444; }
    .toast-card-warning .toast-progress-bar { background: #f59e0b; }
    .toast-card-info    .toast-progress-bar { background: #3b82f6; }

    @keyframes toastEntrance {
        from {
            opacity: 0;
            transform: translateX(100%) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateX(0) scale(1);
        }
    }

    @keyframes toastExit {
        from {
            opacity: 1;
            transform: translateX(0) scale(1);
        }
        to {
            opacity: 0;
            transform: translateX(100%) scale(0.9);
        }
    }

    @keyframes toastProgress {
        from { width: 100%; }
        to { width: 0%; }
    }
</style>

<div class="flash-toast-wrapper" id="flashToastWrapper">
    @if (Session::has('success') || Session::has('error') || Session::has('warning') || Session::has('info') || $errors->any())
        <div class="d-flex flex-column align-items-end gap-2">
            @if ($message = Session::get('success'))
                <div class="toast-card toast-card-success d-flex align-items-center justify-content-between" role="alert">
                    <div class="d-flex align-items-center gap-3">
                        <div class="toast-icon-badge">
                            <i class="fe fe-check-circle"></i>
                        </div>
                        <div>
                            <span class="fw-semibold fs-7 d-block text-white" style="line-height: 1.3;">{{ $message }}</span>
                        </div>
                    </div>
                    <button type="button" class="toast-close-btn" onclick="dismissToast(this.closest('.toast-card'))">
                        <i class="fe fe-x"></i>
                    </button>
                    <div class="toast-progress"><div class="toast-progress-bar"></div></div>
                </div>
            @endif

            @if ($message = Session::get('error'))
                <div class="toast-card toast-card-error d-flex align-items-center justify-content-between" role="alert">
                    <div class="d-flex align-items-center gap-3">
                        <div class="toast-icon-badge">
                            <i class="fe fe-alert-triangle"></i>
                        </div>
                        <div>
                            <span class="fw-semibold fs-7 d-block text-white" style="line-height: 1.3;">{{ $message }}</span>
                        </div>
                    </div>
                    <button type="button" class="toast-close-btn" onclick="dismissToast(this.closest('.toast-card'))">
                        <i class="fe fe-x"></i>
                    </button>
                    <div class="toast-progress"><div class="toast-progress-bar"></div></div>
                </div>
            @endif

            @if ($message = Session::get('warning'))
                <div class="toast-card toast-card-warning d-flex align-items-center justify-content-between" role="alert">
                    <div class="d-flex align-items-center gap-3">
                        <div class="toast-icon-badge">
                            <i class="fe fe-alert-circle"></i>
                        </div>
                        <div>
                            <span class="fw-semibold fs-7 d-block text-white" style="line-height: 1.3;">{{ $message }}</span>
                        </div>
                    </div>
                    <button type="button" class="toast-close-btn" onclick="dismissToast(this.closest('.toast-card'))">
                        <i class="fe fe-x"></i>
                    </button>
                    <div class="toast-progress"><div class="toast-progress-bar"></div></div>
                </div>
            @endif

            @if ($message = Session::get('info'))
                <div class="toast-card toast-card-info d-flex align-items-center justify-content-between" role="alert">
                    <div class="d-flex align-items-center gap-3">
                        <div class="toast-icon-badge">
                            <i class="fe fe-info"></i>
                        </div>
                        <div>
                            <span class="fw-semibold fs-7 d-block text-white" style="line-height: 1.3;">{{ $message }}</span>
                        </div>
                    </div>
                    <button type="button" class="toast-close-btn" onclick="dismissToast(this.closest('.toast-card'))">
                        <i class="fe fe-x"></i>
                    </button>
                    <div class="toast-progress"><div class="toast-progress-bar"></div></div>
                </div>
            @endif

            @if ($errors->any())
                <div class="toast-card toast-card-error d-flex align-items-start justify-content-between" role="alert">
                    <div class="d-flex align-items-start gap-3">
                        <div class="toast-icon-badge mt-1">
                            <i class="fe fe-x-circle"></i>
                        </div>
                        <div>
                            <strong class="d-block text-white small mb-1">Please fix the following:</strong>
                            <ul class="mb-0 ps-3 fs-7 text-slate-300">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="toast-close-btn mt-1" onclick="dismissToast(this.closest('.toast-card'))">
                        <i class="fe fe-x"></i>
                    </button>
                    <div class="toast-progress"><div class="toast-progress-bar"></div></div>
                </div>
            @endif
        </div>
    @endif
</div>

<script>
function dismissToast(element) {
    if (!element) return;
    element.style.animation = 'toastExit 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards';
    setTimeout(function() {
        element.remove();
    }, 300);
}

document.addEventListener('DOMContentLoaded', function() {
    const toasts = document.querySelectorAll('#flashToastWrapper .toast-card');
    toasts.forEach(function(toast) {
        setTimeout(function() {
            if (toast && toast.parentNode) {
                dismissToast(toast);
            }
        }, 4500);
    });
});
</script>
