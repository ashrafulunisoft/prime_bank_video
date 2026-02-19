<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prime Bank Limited - Visitor Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        :root {
            --bg-page: radial-gradient(circle at center, #1e293b 0%, #020617 100%);
            --bg-card: #0f172a;
            --accent-blue: #3b82f6;
            --neon-glow: 0 0 12px rgba(59, 130, 246, 0.8), 0 0 25px rgba(59, 130, 246, 0.4);
            --text-main: #ffffff;
            --text-muted: #94a3b8;
            --input-bg: #1e293b;
            --input-border: #334155;
        }

        body {
            background: var(--bg-page);
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            margin: 0;
        }

        /* Form Container with Deep Shadow */
        .registration-card {
            background-color: var(--bg-card);
            border-radius: 28px;
            width: 100%;
            max-width: 950px;
            padding: 50px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.9), 
                        0 0 40px rgba(59, 130, 246, 0.05);
        }

        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 45px;
        }

        .logo-v {
            background: white;
            color: #0f172a;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 24px;
            border-radius: 10px;
        }

        .brand-text h2 {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .brand-text span {
            font-size: 11px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .page-title {
            font-size: 36px;
            font-weight: 800;
            color: white;
            opacity: 0.95;
        }

        /* Section Header with Neon Effect and Vanishing Line */
        .section-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin: 45px 0 25px 0;
            color: var(--accent-blue);
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 1.8px;
            text-transform: uppercase;
            text-shadow: var(--neon-glow);
        }

        /* Vanishing line effect */
        .section-header::after {
            content: "";
            flex: 1;
            height: 1.5px;
            background: linear-gradient(90deg, var(--accent-blue) 0%, transparent 100%);
            opacity: 0.5;
        }

        .form-label {
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .input-wrapper {
            position: relative;
        }

        .form-control, .form-select {
            background-color: var(--input-bg);
            border: 1.5px solid var(--input-border);
            border-radius: 12px;
            color: white;
            padding: 14px 18px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            background-color: var(--input-bg);
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
            color: white;
            outline: none;
        }

        /* Neon Icon Effect for Inputs */
        .input-wrapper i {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--accent-blue);
            text-shadow: 0 0 8px var(--accent-blue);
            font-size: 14px;
            pointer-events: none;
        }

        /* Webcam Area */
        .webcam-card {
            background: #020617;
            border: 2px dashed var(--input-border);
            border-radius: 16px;
            height: 240px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.3s;
            overflow: hidden;
            position: relative;
        }

        .webcam-card:hover {
            border-color: var(--accent-blue);
            background: #111827;
        }

        /* Neon effect for camera icon */
        .neon-camera {
            font-size: 48px;
            color: var(--accent-blue);
            text-shadow: 0 0 20px var(--accent-blue), 0 0 40px var(--accent-blue);
            margin-bottom: 20px;
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .webcam-card:hover .neon-camera {
            transform: scale(1.15);
        }

        #video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }

        /* Action Buttons */
        .btn-group-custom {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 50px;
            padding-top: 35px;
            border-top: 1px solid rgba(255,255,255,0.05);
        }

        .btn-base {
            padding: 14px 32px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            border: none;
        }

        .btn-cancel {
            background: #334155;
            color: #cbd5e1;
        }

        .btn-cancel:hover {
            background: #475569;
            transform: translateY(-3px);
        }

        .btn-approve {
            background: linear-gradient(135deg, #4f46e5 0%, #2563eb 100%);
            color: white;
            box-shadow: 0 12px 24px -6px rgba(37, 99, 235, 0.5);
        }

        .btn-approve:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 32px -6px rgba(37, 99, 235, 0.6);
        }

        .form-check-input {
            background-color: var(--input-bg);
            border-color: var(--input-border);
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: var(--accent-blue);
            border-color: var(--accent-blue);
        }
    </style>
</head>
<body>

    <div class="registration-card">
        <!-- Header -->
        <div class="header-flex">
            <div class="d-flex align-items-center gap-3">
                <div class="logo-v">V</div>
                <div class="brand-text">
                    <h2>Prime Bank Limited</h2>
                    <span>VISITOR SYSTEM</span>
                </div>
            </div>
            <h1 class="page-title">Visitor Registration</h1>
        </div>

        <form id="registrationForm">
            <!-- Section 1 -->
            <div class="section-header">Personal Information</div>
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Full Name *</label>
                    <div class="input-wrapper">
                        <input type="text" id="fullName" class="form-control" placeholder="Enter your full name" required>
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email Address *</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" class="form-control" placeholder="name@email.com" required>
                        <i class="fas fa-envelope"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password *</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" class="form-control" placeholder="••••••••" required>
                        <i class="fas fa-lock"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone Number</label>
                    <div class="input-wrapper">
                        <input type="tel" id="phone" class="form-control" placeholder="+880 1XXX-XXXXXX">
                        <i class="fas fa-phone"></i>
                    </div>
                </div>
            </div>

            <!-- Section 2 -->
            <div class="section-header">Company & Visit Details</div>
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Company/Organization *</label>
                    <div class="input-wrapper">
                        <input type="text" id="company" class="form-control" placeholder="Enter company name" required>
                        <i class="fas fa-building"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Host Name *</label>
                    <div class="input-wrapper">
                        <input type="text" id="host" class="form-control" placeholder="Meeting with whom?" required>
                        <i class="fas fa-user-tie"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Purpose of Visit *</label>
                    <div class="input-wrapper">
                        <input type="text" id="purpose" class="form-control" placeholder="Nature of visit" required>
                        <i class="fas fa-briefcase"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Visit Date *</label>
                    <div class="input-wrapper">
                        <input type="date" id="visitDate" class="form-control" required>
                    </div>
                </div>
            </div>

            <!-- Section 3 -->
            <div class="section-header">Face Authentication</div>
            <div class="webcam-card" id="webcamBtn">
                <video id="video" autoplay playsinline></video>
                <div id="webcamPlaceholder" class="text-center">
                    <i class="fa-solid fa-camera neon-camera"></i>
                    <p class="m-0" style="color: var(--text-muted); font-size: 13px; font-weight: 500;">
                        Click to activate secure face authentication
                    </p>
                </div>
            </div>

            <!-- Terms -->
            <div class="form-check mt-4">
                <input class="form-check-input" type="checkbox" id="terms" required>
                <label class="form-check-label ms-2" for="terms" style="font-size: 13px; color: var(--text-muted);">
                    I agree to the <a href="#" style="color: var(--accent-blue); text-decoration: none;">visitor terms and conditions</a> and privacy policy.
                </label>
            </div>

            <!-- Actions -->
            <div class="btn-group-custom">
                <button type="button" class="btn-base btn-cancel" id="cancelBtn">Cancel</button>
                <button type="submit" class="btn-base btn-approve" id="registerBtn">
                    <i class="fas fa-check-circle"></i> Approve
                </button>
            </div>
        </form>
    </div>

    <script>
        // Set today as min date
        document.getElementById('visitDate').min = new Date().toISOString().split('T')[0];
        document.getElementById('visitDate').valueAsDate = new Date();

        // Webcam Logic
        const webcamBtn = document.getElementById('webcamBtn');
        const video = document.getElementById('video');
        const placeholder = document.getElementById('webcamPlaceholder');
        let stream = null;

        webcamBtn.addEventListener('click', async () => {
            if (!stream) {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({ video: true });
                    video.srcObject = stream;
                    video.style.display = 'block';
                    placeholder.style.display = 'none';
                    webcamBtn.style.borderStyle = 'solid';
                } catch (err) {
                    console.error("Camera access failed:", err);
                    alert("Please allow camera access for face authentication.");
                }
            }
        });

        // Form Submission logic (Keeping your logic from original code)
        const registrationForm = document.getElementById('registrationForm');
        registrationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!stream) {
                alert('Please enable webcam for face authentication before submitting.');
                return;
            }
            
            const btn = document.getElementById('registerBtn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            btn.disabled = true;

            setTimeout(() => {
                alert(`Visitor Registered Successfully!\nFace authentication verified.`);
                location.reload();
            }, 2000);
        });

        // Cancel
        document.getElementById('cancelBtn').addEventListener('click', function() {
            if (confirm('Are you sure you want to cancel?')) {
                location.reload();
            }
        });
    </script>
</body>
</html>
