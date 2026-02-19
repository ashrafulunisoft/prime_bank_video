<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Home - UCB Bank VMS</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

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

    .main-container {
        max-width: 1200px;
        width: 100%;
        margin: 0 auto;
    }

    .glass-card-dark {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(35px);
        -webkit-backdrop-filter: blur(35px);
        border: 1px solid rgba(59, 130, 246, 0.2);
        border-radius: 40px;
        padding: 4rem;
        box-shadow: 0 30px 100px rgba(0, 0, 0, 0.6), inset 0 0 20px rgba(59, 130, 246, 0.05);
        position: relative;
    }

    .role-card {
        background: rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(59, 130, 246, 0.2);
        border-radius: 24px;
        padding: 3rem 2.5rem;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer;
        position: relative;
        overflow: hidden;
        height: 100%;
        text-decoration: none;
        color: #fff;
        display: block;
    }

    .role-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--accent-indigo), var(--accent-blue));
        transform: scaleX(0);
        transition: transform 0.4s ease;
    }

    .role-card:hover {
        transform: translateY(-10px);
        border-color: rgba(79, 70, 229, 0.8);
        box-shadow: 0 20px 60px rgba(59, 130, 246, 0.5), inset 0 0 40px rgba(59, 130, 246, 0.15), 0 0 80px rgba(59, 130, 246, 0.3);
        background: rgba(59, 130, 246, 0.08);
    }

    .role-card:hover::before {
        transform: scaleX(1);
    }

    .role-card:hover .role-icon {
        transform: scale(1.1);
        box-shadow: 0 15px 50px rgba(59, 130, 246, 0.6), 0 0 100px rgba(59, 130, 246, 0.3);
    }

    .role-card:hover .role-title {
        color: #3b82f6;
        text-shadow: 0 0 30px rgba(59, 130, 246, 0.5);
    }

    .role-card:hover .role-description {
        color: #ffffff;
    }

    .role-icon {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 2.5rem;
        transition: all 0.4s ease;
        position: relative;
    }

    .role-icon::after {
        content: '';
        position: absolute;
        inset: -4px;
        border-radius: 24px;
        background: linear-gradient(135deg, var(--accent-indigo), var(--accent-blue));
        z-index: -1;
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    .role-card:hover .role-icon::after {
        opacity: 1;
    }

    .role-icon.registration {
        background: linear-gradient(135deg, #4f46e5, #3b82f6);
        box-shadow: 0 10px 30px rgba(79, 70, 229, 0.4);
    }

    .role-icon.login {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.4);
    }

    .role-icon.dashboard {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        box-shadow: 0 10px 30px rgba(99, 102, 241, 0.4);
    }

    .role-title {
        font-size: 1.5rem;
        font-weight: 800;
        margin-bottom: 0.75rem;
        letter-spacing: -0.5px;
        text-align: center;
        color: #ffffff;
    }

    .role-description {
        color: #94a3b8;
        font-size: 0.9rem;
        line-height: 1.6;
        text-align: center;
        margin: 0;
    }

    .role-arrow {
        position: absolute;
        bottom: 1.5rem;
        right: 1.5rem;
        opacity: 0;
        transform: translateX(-10px);
        transition: all 0.4s ease;
        color: var(--accent-blue);
    }

    .role-card:hover .role-arrow {
        opacity: 1;
        transform: translateX(0);
    }

    .page-title {
        font-size: 2.5rem;
        font-weight: 800;
        text-align: center;
        margin-bottom: 1rem;
        letter-spacing: -1px;
        background: linear-gradient(135deg, #ffffff 0%, #94a3b8 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .page-subtitle {
        color: #ffffff;
        text-align: center;
        font-size: 1.1rem;
        margin-bottom: 3.5rem;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.7;
        opacity: 0.9;
    }

    .divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.3), transparent);
        margin: 0;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .glass-card-dark {
        animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .role-card {
        animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0;
    }

    .role-card:nth-child(1) { animation-delay: 0.2s; }
    .role-card:nth-child(2) { animation-delay: 0.4s; }
    .role-card:nth-child(3) { animation-delay: 0.6s; }

    @media (max-width: 991px) {
        .glass-card-dark {
            padding: 3rem 2rem;
        }
        .page-title {
            font-size: 2rem;
        }
        .role-card {
            margin-bottom: 1.5rem;
        }
    }

    @media (max-width: 576px) {
        body {
            padding: 1rem;
        }
        .glass-card-dark {
            padding: 2rem 1.5rem;
            border-radius: 24px;
        }
        .page-title {
            font-size: 1.75rem;
        }
        .page-subtitle {
            font-size: 1rem;
            margin-bottom: 2.5rem;
        }
        .role-card {
            padding: 2rem 1.5rem;
        }
        .role-icon {
            width: 60px;
            height: 60px;
            font-size: 2rem;
        }
        .role-title {
            font-size: 1.25rem;
        }
    }
</style>

    {{-- <style>
        :root {
            /* --bg-page: linear-gradient(135deg, #0bd696 0%, #0d5540 100%); */
            --bg-page: linear-gradient(135deg, #4ab59a 0%, #0d5540 100%);
            --sidebar-bg: #0a3d2a;
            --accent-green: #0bd696;
            --accent-dark-green: #0d5540;
            --glass-card: rgba(13, 85, 64, 0.7);
            --glass-border: rgba(255, 255, 255, 0.08);
            --neon-green: 0 0 20px rgba(11, 214, 150, 0.4), 0 0 40px rgba(11, 214, 150, 0.2);
            --text-muted: #a8e6cf;
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

        .main-container {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
        }

        .glass-card-dark {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(35px);
            -webkit-backdrop-filter: blur(35px);
            border: 1px solid rgba(11, 214, 150, 0.3);
            border-radius: 40px;
            padding: 4rem;
            box-shadow: 0 30px 100px rgba(0, 0, 0, 0.6), inset 0 0 20px rgba(11, 214, 150, 0.1);
            position: relative;
        }

        .role-card {
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(11, 214, 150, 0.3);
            border-radius: 24px;
            padding: 3rem 2.5rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            height: 100%;
            text-decoration: none;
            color: #fff;
            display: block;
        }

        .role-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-green), var(--accent-dark-green));
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .role-card:hover {
            transform: translateY(-10px);
            border-color: rgba(54, 117, 97, 0.8);
            box-shadow: 0 20px 60px rgba(11, 214, 150, 0.5), inset 0 0 40px rgba(11, 214, 150, 0.15), 0 0 80px rgba(11, 214, 150, 0.3);
            background: rgba(11, 214, 150, 0.08);
        }

        .role-card:hover::before {
            transform: scaleX(1);
        }

        .role-card:hover .role-icon {
            transform: scale(1.1);
            box-shadow: 0 15px 50px rgba(11, 214, 150, 0.6), 0 0 100px rgba(11, 214, 150, 0.3);
        }

        .role-card:hover .role-title {
            color: #0bd696;
            text-shadow: 0 0 30px rgba(11, 214, 150, 0.5);
        }

        .role-card:hover .role-description {
            color: #ffffff;
        }

        .role-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
            transition: all 0.4s ease;
            position: relative;
        }

        .role-icon::after {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 24px;
            background: linear-gradient(135deg, var(--accent-green), var(--accent-dark-green));
            z-index: -1;
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .role-card:hover .role-icon::after {
            opacity: 1;
        }

        .role-icon.registration {
            background: linear-gradient(135deg, #0bd696, #0d5540);
            box-shadow: 0 10px 30px rgba(11, 214, 150, 0.4);
        }

        .role-icon.login {
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.4);
        }

        .role-icon.dashboard {
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            box-shadow: 0 10px 30px rgba(20, 184, 166, 0.4);
        }

        .role-title {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
            letter-spacing: -0.5px;
            text-align: center;
            color: #ffffff;
        }

        .role-description {
            color: #a8e6cf;
            font-size: 0.9rem;
            line-height: 1.6;
            text-align: center;
            margin: 0;
        }

        .role-arrow {
            position: absolute;
            bottom: 1.5rem;
            right: 1.5rem;
            opacity: 0;
            transform: translateX(-10px);
            transition: all 0.4s ease;
            color: var(--accent-green);
        }

        .role-card:hover .role-arrow {
            opacity: 1;
            transform: translateX(0);
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 1rem;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #ffffff 0%, #a8e6cf 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .page-subtitle {
            color: #ffffff;
            text-align: center;
            font-size: 1.1rem;
            margin-bottom: 3.5rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.7;
            opacity: 0.9;
        }

        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(11, 214, 150, 0.3), transparent);
            margin: 0;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .glass-card-dark {
            animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .role-card {
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }

        .role-card:nth-child(1) { animation-delay: 0.2s; }
        .role-card:nth-child(2) { animation-delay: 0.4s; }
        .role-card:nth-child(3) { animation-delay: 0.6s; }

        @media (max-width: 991px) {
            .glass-card-dark {
                padding: 3rem 2rem;
            }
            .page-title {
                font-size: 2rem;
            }
            .role-card {
                margin-bottom: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            body {
                padding: 1rem;
            }
            .glass-card-dark {
                padding: 2rem 1.5rem;
                border-radius: 24px;
            }
            .page-title {
                font-size: 1.75rem;
            }
            .page-subtitle {
                font-size: 1rem;
                margin-bottom: 2.5rem;
            }
            .role-card {
                padding: 2rem 1.5rem;
            }
            .role-icon {
                width: 60px;
                height: 60px;
                font-size: 2rem;
            }
            .role-title {
                font-size: 1.25rem;
            }
        }
    </style> --}}

</head>
<body>

    <div class="main-container">
        <div class="glass-card-dark">
            <!-- Logo -->
            <div class="text-center gap-3 mb-3 ">
                <img class="bg-white " src="{{ asset('vms/logo/primebank_logo.png') }}" style="height: 70px; width: 250px; border-radius:10px;" alt="UCB Bank Logo">
            </div>

            <!-- Header -->
            <h1 class="page-title ">Prime Bank Limited</h1>
            <p class="page-subtitle text-white">
                Welcome to Prime Bank Limited. Please select a option to continue.
            </p>

            <hr class="divider mb-4">

            <!-- Role Cards -->
            <div class="row g-4">
                <!-- Registration Card -->
                <div class="col-lg-4 col-md-6">
                    <a href="{{ route('register') }}" class="role-card">
                        <div class="role-icon registration">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h3 class="role-title">Registration</h3>
                        <p class="role-description">
                            Create a new account to access Pragati Life Insurance
                        </p>
                        <div class="role-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                </div>

                <!-- Login Card -->
                <div class="col-lg-4 col-md-6">
                    <a href="{{ route('login') }}" class="role-card">
                        <div class="role-icon login">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <h3 class="role-title">Login</h3>
                        <p class="role-description">
                            Sign in to your existing account to manage visitors
                        </p>
                        <div class="role-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                </div>

                <!-- Live Dashboard Card -->
                <div class="col-lg-4 col-md-6">
                    <a href="{{ route('chatbot.index') }}" class="role-card">
                        <div class="role-icon dashboard">
                            <i class="fas fa-comment-dots"></i> 
                        </div>
                        <h3 class="role-title">AI Assistant</h3>
                        <p class="role-description">
                            Chat with our smart AI assistant for instant help, support, and answers
                        </p>
                        <div class="role-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                </div>

            </div>

            <!-- Footer Info -->
            <div class="text-center mt-5 pt-4">
                <p class="mb-0 text-dark" style="font-size: 0.85rem; color: #94a3b8;">
                    <i class="fas fa-shield-alt me-2"></i>
                    Secure &bull; Reliable &bull; Efficient
                </p>
            </div>
        </div>
    </div>

</body>
</html>
