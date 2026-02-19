@extends('layouts.receptionist')

@section('title', 'Edit Visitor - Prime Bank Limited')

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
            <h2 class="fw-800 mb-0 text-white letter-spacing-1" style="font-size: 2rem;">Edit Visitor</h2>
        </div>

        <form action="{{ route('visitor.update', $visit->id) }}" method="POST" id="editForm">
            @csrf
            @method('PUT')

            <!-- Section 1: Personal Information -->
            <div class="permission-title">Personal Information</div>
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <label class="form-label">Full Name *</label>
                    <div class="position-relative">
                        <input type="text" name="name" class="input-dark input-custom" placeholder="Enter your full name" value="{{ old('name', $visit->visitor->name) }}" required>
                        <i class="fas fa-user input-icon"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email Address *</label>
                    <div class="position-relative">
                        <input type="email" name="email" class="input-dark input-custom" placeholder="name@email.com" value="{{ old('email', $visit->visitor->email) }}" required>
                        <i class="fas fa-envelope input-icon"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone Number</label>
                    <div class="position-relative">
                        <input type="tel" name="phone" class="input-dark input-custom" placeholder="+880 1XXX-XXXXXX" value="{{ old('phone', $visit->visitor->phone) }}">
                        <i class="fas fa-phone input-icon"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Company/Organization</label>
                    <div class="position-relative">
                        <input type="text" name="company" class="input-dark input-custom" placeholder="Enter company name" value="{{ old('company', $visit->visitor->address) }}">
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
                               value="{{ old('host_name', $visit->meetingUser->name) }}"
                               required
                               autocomplete="off">
                        <i class="fas fa-user-tie input-icon"></i>
                        <div id="host-suggestions" class="host-suggestions"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Visit Type *</label>
                    <div class="position-relative">
                        <select name="visit_type_id" class="input-dark input-custom" required>
                            <option value="" disabled selected>Select visit type</option>
                            @foreach($visitTypes as $type)
                            <option value="{{ $type->id }}" {{ old('visit_type_id', $visit->visit_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down input-icon"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Purpose of Visit *</label>
                    <div class="position-relative">
                        <input type="text" name="purpose" class="input-dark input-custom" placeholder="Nature of visit" value="{{ old('purpose', $visit->purpose) }}" required>
                        <i class="fas fa-briefcase input-icon"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Visit Date *</label>
                    <div class="position-relative">
                        <input type="date" name="visit_date" class="input-dark input-custom" id="visitDate" value="{{ old('visit_date', \Carbon\Carbon::parse($visit->schedule_time)->format('Y-m-d')) }}" required>
                        <i class="fas fa-calendar-alt input-icon"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <div class="position-relative">
                        <select name="status" class="input-dark input-custom">
                            <option value="approved" {{ old('status', $visit->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="pending" {{ old('status', $visit->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ old('status', $visit->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status', $visit->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        <i class="fas fa-chevron-down input-icon"></i>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-end gap-3 mt-5 pt-4" style="border-top: 1px solid rgba(255,255,255,0.05);">
                <a href="{{ route('visitor.index') }}" class="btn-outline btn-reset" style="text-decoration: none;">
                    Cancel
                </a>
                <button type="submit" class="btn-gradient btn-create" id="updateBtn">
                    <i class="fas fa-save"></i> Update Visitor
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <style>
        /* Hide browser default calendar icon for date inputs */
        input[type="date"]::-webkit-calendar-picker-indicator {
            opacity: 0;
            position: absolute;
            left: -9999px;
            cursor: pointer;
            height: 100%;
            width: 100%;
        }

        /* Hide browser default dropdown arrow for select elements */
        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: none;
        }

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

        // Get input elements
        const emailInput = document.querySelector('input[name="email"]');
        const nameInput = document.querySelector('input[name="name"]');
        const phoneInput = document.querySelector('input[name="phone"]');
        const companyInput = document.querySelector('input[name="company"]');
        const hostInput = document.getElementById('host_name');
        const suggestionsBox = document.getElementById('host-suggestions');

        // Debounce function - MUST be defined BEFORE using it
        function debounce(func, delay) {
            let timeoutId;
            return function(...args) {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => func.apply(this, args), delay);
            };
        }

        // Email Auto-fill for existing visitors
        const checkVisitorByEmail = debounce(async (email) => {
            console.log('Checking visitor by email:', email);

            if (!email || email.length < 3) return;

            try {
                const response = await fetch(`{{ route('visitor.autofill') }}?email=${encodeURIComponent(email)}`);
                const data = await response.json();
                console.log('Response:', data);

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
            console.log('Checking visitor by phone:', phone);

            if (!phone || phone.length < 3) return;

            try {
                const response = await fetch(`{{ route('visitor.autofill') }}?phone=${encodeURIComponent(phone)}`);
                const data = await response.json();
                console.log('Response:', data);

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

        // Email input event listener
        emailInput.addEventListener('input', (e) => {
            console.log('Email input:', e.target.value);
            checkVisitorByEmail(e.target.value);
        });

        // Phone input event listener
        phoneInput.addEventListener('input', (e) => {
            console.log('Phone input:', e.target.value);
            checkVisitorByPhone(e.target.value);
        });

        // Search hosts with debounce
        const searchHosts = debounce(async (query) => {
            console.log('Searching hosts for:', query);

            if (query.length < 2) {
                suggestionsBox.classList.remove('show');
                return;
            }

            try {
                const response = await fetch(`{{ route('visitor.search-host') }}?q=${encodeURIComponent(query)}`);
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
        const editForm = document.getElementById('editForm');
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const btn = document.getElementById('updateBtn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            btn.disabled = true;

            // Submit form
            editForm.submit();
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
