<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Pragati Life Insurance</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @php
        // dd("login"); 
    @endphp

    <style>
        :root {
            --bg-page: radial-gradient(circle at center, #101d42 0%, #060b1d 100%);
            --sidebar-bg: #111a30;
            --accent-indigo: #4f46e5;
            --accent-blue: #3b82f6;
            --accent-pink: #db2777;
            --glass-card: rgba(15, 23, 42, 0.7);
            --glass-border: rgba(255, 255, 255, 0.08);
            --neon-blue: 0 0 20px rgba(59, 130, 246, 0.4), 0 0 40px rgba(59, 130, 246, 0.2);
            --neon-indigo: 0 8px 25px rgba(79, 70, 229, 0.4);
            --text-muted: #94a3b8;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-page);
            background-attachment: fixed;
            min-height: 100vh;
            color: #fff;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .glass-card-dark {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(35px);
            -webkit-backdrop-filter: blur(35px);
            border: 1px solid rgba(11, 214, 150, 0.3);
            border-radius: 40px;
            padding: 3.5rem;
            box-shadow: 0 30px 100px rgba(0, 0, 0, 0.6), inset 0 0 20px rgba(11, 214, 150, 0.1);
            position: relative;
            max-width: 500px;
            width: 100%;
        }

        .input-dark {
            width: 100%;
            background: rgba(0, 0, 0, 0.3) !important;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 14px 20px;
            color: #fff !important;
            font-size: 0.95rem;
            transition: all 0.3s;
            outline: none;
        }

        .input-dark:focus {
            border-color: #0bd696 !important;
            box-shadow: 0 0 20px rgba(11, 214, 150, 0.6), 0 0 40px rgba(11, 214, 150, 0.3) !important;
            background: rgba(0, 0, 0, 0.45) !important;
            transform: translateY(-2px);
        }

        .is-invalid {
            border-color: #ef4444 !important;
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.6), 0 0 40px rgba(239, 68, 68, 0.3) !important;
        }

        .form-label {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #ffffff;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .input-icon {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--accent-green);
            opacity: 0.6;
            font-size: 0.9rem;
            pointer-events: none;
        }

        .btn-gradient {
            background: linear-gradient(135deg, #4f46e5, #3b82f6);
            border: none;
            border-radius: 100px;
            padding: 16px;
            color: white;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.85rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.6), 0 0 40px rgba(59, 130, 246, 0.3);
            width: 100%;
        }

        .btn-gradient:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(59, 130, 246, 0.5);
            filter: brightness(1.1);
        }

        .logo-vms {
            background: linear-gradient(135deg, var(--accent-green), var(--accent-dark-green));
            color: #fff;
            width: 50px;
            height: 50px;
            border-radius: 12px;
            font-weight: 900;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 30px rgba(11, 214, 150, 0.4);
        }

        .logo-responsive {
            width: 100%;
            max-width: 200px;
            height: auto;
            border-radius: 10px;
        }

        @media (max-width: 768px) {
            .logo-responsive {
                max-width: 180px;
            }
        }

        @media (max-width: 576px) {
            .logo-responsive {
                max-width: 150px;
            }
        }

        .alert-danger-custom {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 16px;
            padding: 1rem 1.2rem;
            color: #f87171;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(10px);
        }

        .alert-success-custom {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            border-radius: 16px;
            padding: 1rem 1.2rem;
            color: #4ade80;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(10px);
        }

        .alert-danger-custom ul {
            margin: 0;
            padding-left: 1.2rem;
        }

        .alert-danger-custom li {
            margin-bottom: 0.5rem;
        }

        .alert-danger-custom li:last-child {
            margin-bottom: 0;
        }

        .invalid-feedback-custom {
            color: #ef4444;
            font-size: 0.8rem;
            margin-top: 0.5rem;
        }

        .form-check-custom {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 12px 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        .form-check-custom:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(11, 214, 150, 0.3);
        }

        .form-check-input-custom {
            width: 20px;
            height: 20px;
            accent-color: #0bd696;
            cursor: pointer;
        }

        .form-check-label-custom {
            color: #ffffff;
            font-size: 0.85rem;
            font-weight: 500;
            margin: 0;
            cursor: pointer;
            user-select: none;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .glass-card-dark {
            animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .text-white-50 {
            color: rgba(255, 255, 255, 0.5) !important;
        }

        @media (max-width: 576px) {
            body {
                padding: 1rem;
            }
            .glass-card-dark {
                padding: 2rem;
                border-radius: 24px;
            }
            .logo-vms {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>

    <div class="glass-card-dark">
        <!-- Logo -->
        {{-- <div class="logo-vms">
            <i class="fas fa-university"></i>
        </div> --}}
        <div class="text-center gap-3 mb-3">
            <img class="bg-white logo-responsive" src="{{ asset('vms/logo/primebank_logo.png') }}" alt="Prime Bank Logo">
        </div>


        <!-- Header -->
        <div class="text-center mb-4">
            <h2 class="fw-800 text-white mb-2" style="letter-spacing: -1px;">Please Sign In</h2>
            <p class="text-white-50 mb-0" style="font-size: 0.9rem;">Welcome back to Pragati Life Insurance</p>
        </div>

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="alert-danger-custom">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Session Status -->
        @if (session('status'))
            <div class="alert-success-custom">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="mb-3 position-relative">
                <label class="form-label">
                    <i class="fas fa-envelope"></i> Email Address
                </label>
                <div class="position-relative">
                    <input
                        type="email"
                        class="input-dark @error('email') is-invalid @enderror"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="Enter your email"
                    >
                    <i class="fas fa-envelope input-icon"></i>
                </div>
                @error('email')
                    <div class="invalid-feedback-custom">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3 position-relative">
                <label class="form-label">
                    <i class="fas fa-lock"></i> Password
                </label>
                <div class="position-relative">
                    <input
                        type="password"
                        class="input-dark @error('password') is-invalid @enderror"
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Enter your password"
                    >
                    <i class="fas fa-lock input-icon"></i>
                </div>
                @error('password')
                    <div class="invalid-feedback-custom">{{ $message }}</div>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="mb-3">
                <label class="form-check-custom">
                    <input
                        class="form-check-input-custom"
                        type="checkbox"
                        id="remember_me"
                        name="remember"
                    >
                    <span class="form-check-label-custom">Remember me</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button class="btn-gradient" type="submit">
                <i class="fas fa-sign-in-alt me-2"></i> Log In
            </button>

            <!-- Forgot Password Link -->
            <div class="text-center mt-4">
                <p class="text-white-50 mb-0" style="font-size: 0.85rem;">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-white fw-800" style="text-decoration: none;">
                            Forgot password?
                        </a>
                    @endif
                </p>
            </div>
        </form>
    </div>

</body>
</html>
