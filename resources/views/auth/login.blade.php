<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Inoodex">
    <title>Sign In — Inoodex Inventory</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets') }}/img/logo.jpg">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/css/bootstrap.min.css">
    
    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --primary-light: #eef2ff;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8fafc;
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 1.5rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient subtle background decorative shapes */
        .ambient-bg {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
        }

        .ambient-shape-1 {
            position: absolute;
            top: -10%;
            left: -5%;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.07) 0%, rgba(255, 255, 255, 0) 70%);
            filter: blur(40px);
        }

        .ambient-shape-2 {
            position: absolute;
            bottom: -10%;
            right: -5%;
            width: 450px;
            height: 450px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.08) 0%, rgba(255, 255, 255, 0) 70%);
            filter: blur(50px);
        }

        .login-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border-radius: 20px;
            padding: 2.75rem 2.5rem;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.04), 0 20px 25px -5px rgba(0, 0, 0, 0.02);
            border: 1px solid rgba(226, 232, 240, 0.8);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .brand-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-logo {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.12);
            margin-bottom: 1.25rem;
        }

        .brand-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: var(--text-dark);
            margin-bottom: 0.35rem;
        }

        .brand-header p {
            font-size: 0.88rem;
            color: var(--text-muted);
            margin: 0;
        }

        .form-group {
            margin-bottom: 1.35rem;
        }

        .form-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.45rem;
            display: block;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.95rem;
            z-index: 5;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .form-control {
            height: 48px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            padding-left: 2.75rem;
            padding-right: 1rem;
            font-size: 0.92rem;
            font-weight: 500;
            color: var(--text-dark);
            background-color: #f8fafc;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            background-color: #ffffff;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
            outline: none;
        }

        .form-control:focus + .input-icon {
            color: var(--primary);
        }

        .pass-input-wrapper .form-control {
            padding-right: 2.75rem;
        }

        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            font-size: 0.95rem;
            padding: 4px;
            z-index: 5;
            transition: color 0.2s ease;
        }

        .toggle-password:hover {
            color: var(--text-dark);
        }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.75rem;
            font-size: 0.84rem;
        }

        .form-check-input {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            border: 1px solid #cbd5e1;
            margin-top: 0;
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .form-check-label {
            color: var(--text-muted);
            cursor: pointer;
            user-select: none;
        }

        .btn-submit {
            width: 100%;
            height: 48px;
            background-color: var(--primary);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 0.94rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-submit:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.28);
            color: #ffffff;
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .alert-custom {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            border-radius: 10px;
            padding: 0.65rem 0.9rem;
            font-size: 0.84rem;
            margin-bottom: 1.25rem;
        }

        .login-footer {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.78rem;
            color: #94a3b8;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 2rem 1.5rem;
                border-radius: 16px;
            }
        }
    </style>
</head>

<body>
    <div class="ambient-bg">
        <div class="ambient-shape-1"></div>
        <div class="ambient-shape-2"></div>
    </div>

    <div class="login-card">
        <div class="brand-header">
            <img src="{{ asset('assets') }}/img/logo.jpg" alt="Inoodex Logo" class="brand-logo">
            <h1>Inoodex Inventory</h1>
            <p>Enter your credentials to access dashboard</p>
        </div>

        @if ($errors->any())
            <div class="alert-custom d-flex align-items-center gap-2">
                <i class="fas fa-circle-exclamation"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="post" action="{{ route('login') }}" autocomplete="off">
            @csrf
            
            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <div class="input-wrapper">
                    <input type="email" name="email" id="email" class="form-control"
                        value="{{ old('email') }}" placeholder="name@company.com" required autofocus>
                    <i class="fas fa-envelope input-icon"></i>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-wrapper pass-input-wrapper">
                    <input type="password" name="password" id="password" class="form-control pass-input"
                        placeholder="••••••••" required>
                    <i class="fas fa-lock input-icon"></i>
                    <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                </div>
            </div>

            <div class="form-options">
                <div class="form-check d-flex align-items-center gap-2 m-0">
                    <input type="checkbox" class="form-check-input" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Keep me logged in</label>
                </div>
            </div>

            <button class="btn-submit" type="submit">
                <span>Sign In</span>
                <i class="fas fa-arrow-right fs-7"></i>
            </button>
        </form>

        <div class="login-footer">
            &copy; {{ date('Y') }} Inoodex Inventory. All rights reserved.
        </div>
    </div>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('assets') }}/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var toggle = document.getElementById('togglePassword');
            var input = document.getElementById('password');
            
            if (toggle && input) {
                toggle.addEventListener('click', function() {
                    var type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                });
            }
        });
    </script>
</body>

</html>