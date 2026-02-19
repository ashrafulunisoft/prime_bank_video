@extends('layouts.receptionist')

@section('content')
<div class="role-container" style="max-width: 950px;">
    <div class="glass-card glass-card-dark">
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 1.5rem;">
            <div class="d-flex align-items-center gap-3">
                <div class="logo-vms" style="width: 44px; height: 44px; font-size: 1.2rem;">V</div>
                <div>
                    <h6 class="fw-800 mb-0 text-white" style="font-size: 1.1rem;">Prime Bank Limited</h6>
                    <span class="permission-title" style="font-size: 0.7rem; margin: 0;">VISITOR SYSTEM</span>
                </div>
            </div>
            <h2 class="fw-800 mb-0 text-white letter-spacing-1" style="font-size: 2rem;">Register Visitor</h2>
        </div>

        <form action="{{ route('visitor.store') }}" method="POST" id="registrationForm">
            @csrf

            <!-- Section 1: Personal Information -->
            <div class="permission-title">Personal Information</div>
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <label class="form-label">Full Name *</label>
                    <div class="position-relative">
                        <input type="text" name="name" class="input-dark input-custom" placeholder="Enter your full name" value="{{ old('name') }}" required>
                        <i class="fas fa-user input-icon"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email Address *</label>
                    <div class="position-relative">
                        <input type="email" name="email" class="input-dark input-custom" placeholder="name@email.com" value="{{ old('email') }}" required>
                        <i class="fas fa-envelope input-icon"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone Number</label>
                    <div class="position-relative">
                        <input type="tel" name="phone" class="input-dark input-custom" placeholder="+880 1XXX-XXXXXX" value="{{ old('phone') }}">
                        <i class="fas fa-phone input-icon"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Company/Organization</label>
                    <div class="position-relative">
                        <input type="text" name="company" class="input-dark input-custom" placeholder="Enter company name" value="{{ old('company') }}">
                        <i class="fas fa-building input-icon"></i>
                    </div>
                </div>
            </div>

            <!-- Section 2: Visit Details -->
            <div class="permission-title">Visit Details</div>
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <label class="form-label">Host Name *</label>
                    <div class="position-relative">
                        <input type="text"
                               name="host_name"
                               id="host_name"
                               class="input-dark input-custom"
                               placeholder="Meeting with whom?"
                               value="{{ old('host_name') }}"
                               required
                               autocomplete="off">
                        <i class="fas fa-user-tie input-icon"></i>
                        <div id="host-suggestions" class="host-suggestions"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Visit Type *</label>
                    <select name="visit_type_id" class="input-dark input-custom" required>
                        <option value="" disabled selected>Select visit type</option>
                        @foreach($visitTypes as $type)
                        <option value="{{ $type->id }}" {{ old('visit_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Purpose of Visit *</label>
                    <div class="position-relative">
                        <input type="text" name="purpose" class="input-dark input-custom" placeholder="Nature of visit" value="{{ old('purpose') }}" required>
                        <i class="fas fa-briefcase input-icon"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Visit Date *</label>
                    <div class="position-relative">
                        <input type="date" name="visit_date" class="input-dark input-custom" id="visitDate" value="{{ old('visit_date') ?? date('Y-m-d') }}" required>
                        <i class="fas fa-calendar-alt input-icon"></i>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-end gap-3 mt-5 pt-4" style="border-top: 1px solid rgba(255,255,255,0.05);">
                <a href="{{ route('visitor.index') }}" class="btn-outline btn-reset" style="text-decoration: none;">
                    Cancel
                </a>
                <button type="submit" class="btn-gradient btn-create" id="registerBtn">
                    <i class="fas fa-check-circle"></i> Approve
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <style>
        #webcamBtn:hover { border-color: var(--accent-blue) !important; background: #111827; }
        #webcamBtn:hover #webcamPlaceholder i { transform: scale(1.15); }
        .form-check-input { background-color: rgba(0, 0, 0, 0.3); border:1px solid rgba(255,255,255,0.1); cursor: pointer; }
        .form-check-input:checked { background-color: var(--accent-blue); border-color: var(--accent-blue); }

        /* Host Suggestions Dropdown */
        .host-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: 12px;
            margin-top: 8px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5), 0 0 20px rgba(59, 130, 246, 0.2);
        }

        .host-suggestions.show {
            display: block;
        }

        .host-suggestion-item {
            padding: 12px 20px;
            color: #fff;
            cursor: pointer;
            transition: 0.2s;
            font-size: 0.9rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .host-suggestion-item:last-child {
            border-bottom: none;
        }

        .host-suggestion-item:hover {
            background: rgba(59, 130, 246, 0.2);
            padding-left: 25px;
        }

        .host-suggestion-item.active {
            background: rgba(59, 130, 246, 0.3);
            color: var(--accent-blue);
        }

        .host-suggestions::-webkit-scrollbar {
            width: 6px;
        }

        .host-suggestions::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }

        .host-suggestions::-webkit-scrollbar-thumb {
            background: var(--accent-blue);
            border-radius: 10px;
        }
    </style>

    <script>
        // Set today as min date
        document.getElementById('visitDate').min = new Date().toISOString().split('T')[0];
        document.getElementById('visitDate').valueAsDate = new Date();

        // Email Auto-fill for existing visitors
        const emailInput = document.querySelector('input[name="email"]');
        const nameInput = document.querySelector('input[name="name"]');
        const phoneInput = document.querySelector('input[name="phone"]');
        const companyInput = document.querySelector('input[name="company"]');
        let emailDebounceTimer;

        // Check visitor and auto-fill by email
        const checkVisitorByEmail = debounce(async (email) => {
            console.log('=== Email Check Started ===');
            console.log('Email value:', email);

            if (!email || email.length < 3) return;

            try {
                const url = `{{ route('visitor.check-email') }}?email=${encodeURIComponent(email)}`;
                console.log('Fetching URL:', url);

                const response = await fetch(url);
                console.log('Response status:', response.status);

                const data = await response.json();
                console.log('Response data:', data);

                if (data.success && data.visitor) {
                    // Auto-fill visitor data
                    nameInput.value = data.visitor.name;
                    phoneInput.value = data.visitor.phone || '';
                    companyInput.value = data.visitor.company || '';

                    // Show notification
                    Swal.fire({
                        title: 'Visitor Found!',
                        text: 'Visitor information auto-filled from database',
                        icon: 'info',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3b82f6',
                        timer: 2000,
                        timerProgressBar: true,
                        toast: true,
                        position: 'top-end'
                    });
                }
            } catch (error) {
                console.error('Error checking visitor:', error);
            }
        }, 500);

        // Check visitor and auto-fill by phone
        const checkVisitorByPhone = debounce(async (phone) => {
            console.log('=== Phone Check Started ===');
            console.log('Phone value:', phone);

            if (!phone || phone.length < 3) return;

            try {
                const url = `{{ route('visitor.check-phone') }}?phone=${encodeURIComponent(phone)}`;
                console.log('Fetching URL:', url);

                const response = await fetch(url);
                console.log('Response status:', response.status);

                const data = await response.json();
                console.log('Response data:', data);

                if (data.success && data.visitor) {
                    // Auto-fill visitor data
                    nameInput.value = data.visitor.name;
                    emailInput.value = data.visitor.email || '';
                    companyInput.value = data.visitor.company || '';

                    // Show notification
                    Swal.fire({
                        title: 'Visitor Found!',
                        text: 'Visitor information auto-filled from database',
                        icon: 'info',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3b82f6',
                        timer: 2000,
                        timerProgressBar: true,
                        toast: true,
                        position: 'top-end'
                    });
                }
            } catch (error) {
                console.error('Error checking visitor by phone:', error);
            }
        }, 500);

        // Email input event listener with debounce
        emailInput.addEventListener('input', (e) => {
            checkVisitorByEmail(e.target.value);
        });

        // Phone input event listener with debounce
        phoneInput.addEventListener('input', (e) => {
            checkVisitorByPhone(e.target.value);
        });

        // Host Name Autocomplete
        const hostInput = document.getElementById('host_name');
        const suggestionsBox = document.getElementById('host-suggestions');
        let debounceTimer;

        // Debounce function to limit API calls
        function debounce(func, delay) {
            return function(...args) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => func.apply(this, args), delay);
            };
        }

        // Search hosts with debounce
        const searchHosts = debounce(async (query) => {
            console.log('=== Host Search Started ===');
            console.log('Query:', query);

            if (query.length < 2) {
                suggestionsBox.classList.remove('show');
                return;
            }

            try {
                const url = `{{ route('visitor.search-host') }}?q=${encodeURIComponent(query)}`;
                console.log('Fetching URL:', url);

                const response = await fetch(url);
                console.log('Response status:', response.status);

                const data = await response.json();
                console.log('Response data:', data);

                if (data.success && data.hosts) {
                    displaySuggestions(data.hosts);
                } else {
                    suggestionsBox.classList.remove('show');
                }
            } catch (error) {
                console.error('Error searching hosts:', error);
            }
        }, 300);

        // Display suggestions
        function displaySuggestions(users) {
            if (users.length === 0) {
                suggestionsBox.classList.remove('show');
                return;
            }

            suggestionsBox.innerHTML = users.map(user => `
                <div class="host-suggestion-item" data-name="${user.name}" data-id="${user.id}">
                    <i class="fas fa-user me-2" style="opacity: 0.6; font-size: 0.8rem;"></i>
                    ${user.name}
                </div>
            `).join('');

            suggestionsBox.classList.add('show');
        }

        // Event listeners
        hostInput.addEventListener('input', (e) => {
            searchHosts(e.target.value);
        });

        hostInput.addEventListener('focus', () => {
            if (hostInput.value.length >= 2) {
                searchHosts(hostInput.value);
            }
        });

        // Handle suggestion click
        suggestionsBox.addEventListener('click', (e) => {
            const item = e.target.closest('.host-suggestion-item');
            if (item) {
                hostInput.value = item.dataset.name;
                suggestionsBox.classList.remove('show');
            }
        });

        // Handle keyboard navigation
        let currentFocus = -1;
        hostInput.addEventListener('keydown', (e) => {
            const items = suggestionsBox.querySelectorAll('.host-suggestion-item');

            if (!items.length) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                currentFocus++;
                if (currentFocus >= items.length) currentFocus = 0;
                setActive(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                currentFocus--;
                if (currentFocus < 0) currentFocus = items.length - 1;
                setActive(items);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (currentFocus > -1) {
                    items[currentFocus].click();
                }
            } else if (e.key === 'Escape') {
                suggestionsBox.classList.remove('show');
                currentFocus = -1;
            }
        });

        function setActive(items) {
            items.forEach((item, index) => {
                item.classList.remove('active');
                if (index === currentFocus) {
                    item.classList.add('active');
                    item.scrollIntoView({ block: 'nearest' });
                }
            });
        }

        // Close suggestions when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.position-relative')) {
                suggestionsBox.classList.remove('show');
            }
        });

        // Form Submission
        const registrationForm = document.getElementById('registrationForm');
        registrationForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const btn = document.getElementById('registerBtn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            btn.disabled = true;

            // Submit form
            registrationForm.submit();
        });
    </script>

    @if(session('success'))
        <script>
            Swal.fire({
                title: 'Success!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3b82f6',
                timer: 2000,
                timerProgressBar: true,
                showCloseButton: true,
                closeButtonAriaLabel: 'Close this alert'
            });
        </script>
    @endif

    @if($errors->any())
        <script>
            Swal.fire({
                title: 'Error!',
                text: "{{ $errors->first() }}",
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#ef4444',
                showCloseButton: true,
                closeButtonAriaLabel: 'Close this alert'
            });
        </script>
    @endif
@endpush
@endsection
