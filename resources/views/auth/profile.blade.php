<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <style>

    </style>
</head>

<body>
    @include('components.navbar')
    <div class="container">
        <div class="profile-section">
            <h1 class="section-title">Edit Profile</h1>

            @if ($loading)
                <div class="loading">Loading profile data...</div>
            @endif

            @if ($errors->any())
                <div class="error">
                    {{ $errors->first() }}
                </div>
            @endif

            <div id="profileContent" @if ($loading) style="display: none;" @endif>
                <div class="profile-photo">
                    <div class="photo-circle" id="profilePhotoDisplay">
                        @if ($user->image_path && Storage::disk('public')->exists($user->image_path))
                            <img src="{{ asset('storage/' . $user->image_path) }}?v={{ time() }}"
                                alt="Profile Photo">
                        @else
                            <span id="photoInitial">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="upload-container">
                        <form action="{{ route('profile.upload') }}" method="POST" enctype="multipart/form-data"
                            id="photoUploadForm">
                            @csrf
                            <div class="upload-btn">
                                Upload new photo
                                <input type="file" id="photoUpload" name="photo"
                                    accept="image/jpeg,image/png,image/jpg"
                                    onchange="document.getElementById('photoUploadForm').submit()">
                            </div>
                        </form>
                        <div class="photo-info">At least 800 x 800 x recommended<br>JPG or PNG is allowed</div>
                    </div>
                </div>

                <div class="personal-info-header">
                    <h2 style="font-size: 18px; color: #333; font-weight: 600; margin: 0;">Personal Informations</h2>
                    <button type="button" class="edit-toggle-btn" id="editToggleBtn" onclick="toggleAllEdit()">
                        <span class="edit-icon">✎</span>
                        <span id="editBtnText">Edit</span>
                    </button>
                </div>

                <form class="form-grid" id="profileForm" method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-input editable-field" id="name" name="name"
                            value="{{ old('name', $user->name) }}" placeholder="Enter your full name" readonly>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Height (cm)</label>
                        <input type="number" class="form-input editable-field" id="tb" name="tb"
                            value="{{ old('tb', $user->tb) }}" placeholder="Enter height in cm" readonly>
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-input editable-field" id="email" name="email"
                            value="{{ old('email', $user->email) }}" placeholder="Enter your email" readonly>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Weight (kg)</label>
                        <input type="number" class="form-input editable-field" id="bb" name="bb"
                            value="{{ old('bb', $user->bb) }}" placeholder="Enter weight in kg" readonly>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Age</label>
                        <input type="number" class="form-input editable-field" id="usia" name="usia"
                            value="{{ old('usia', $user->usia) }}" placeholder="Enter your age" readonly>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="password-container">
                            <input type="password" class="form-input editable-field" id="password" name="password"
                                value="********" placeholder="********" readonly
                                data-has-password="{{ $user->password ? 'true' : 'false' }}">
                            <button type="button" class="password-toggle" onclick="togglePasswordVisibility(this)">
                                👁️
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Activity Level</label>
                        <select class="form-input editable-field" id="aktivitas" name="aktivitas" disabled>
                            <option value="">Select activity level</option>
                            <option value="Sedentary"
                                {{ old('aktivitas', $user->aktivitas) == 'Sedentary' ? 'selected' : '' }}>Sedentary
                            </option>
                            <option value="Lightly Active"
                                {{ old('aktivitas', $user->aktivitas) == 'Lightly Active' ? 'selected' : '' }}>Lightly
                                Active</option>
                            <option value="Moderately Active"
                                {{ old('aktivitas', $user->aktivitas) == 'Moderately Active' ? 'selected' : '' }}>
                                Moderately Active</option>
                            <option value="Very Active"
                                {{ old('aktivitas', $user->aktivitas) == 'Very Active' ? 'selected' : '' }}>Very Active
                            </option>
                            <option value="Extra Active"
                                {{ old('aktivitas', $user->aktivitas) == 'Extra Active' ? 'selected' : '' }}>
                                Extra Active</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <select class="form-input editable-field" id="jenis_kelamin" name="jenis_kelamin" disabled>
                            <option value="">Select gender</option>
                            <option value="L"
                                {{ old('jenis_kelamin', $user->jenis_kelamin) == 'L' ? 'selected' : '' }}>Male</option>
                            <option value="P"
                                {{ old('jenis_kelamin', $user->jenis_kelamin) == 'P' ? 'selected' : '' }}>Female
                            </option>
                        </select>
                    </div>

                    <button class="save-btn" id="saveBtn" type="submit" disabled>Save</button>
                </form>
            </div>
        </div>

        <div class="progress-section">
            <h2 class="section-title">Complete your profile</h2>

            <div class="progress-circle">
                <div class="circle-progress" id="progressCircle">
                    <span class="progress-text" id="progressText">{{ $completionPercentage }}%</span>
                </div>
            </div>

            <ul class="checklist" id="progressChecklist">
                <li class="checklist-item">
                    <span class="check-icon {{ $user->email ? '' : 'incomplete' }}" id="check-account">✓</span>
                    <span>Setup account</span>
                </li>
                <li class="checklist-item">
                    <span class="check-icon {{ $user->image_path ? '' : 'incomplete' }}" id="check-photo">✓</span>
                    <span>Upload photo</span>
                </li>
                <li class="checklist-item">
                    <span class="check-icon {{ $user->aktivitas ? '' : 'incomplete' }}"
                        id="check-assessment">✓</span>
                    <span>Assessment completed</span>
                </li>
                <li class="checklist-item">
                    <span class="check-icon {{ $user->name && $user->usia ? '' : 'incomplete' }}"
                        id="check-info">✓</span>
                    <span>Personal information</span>
                </li>
                <li class="checklist-item">
                    <span class="check-icon incomplete" id="check-forum">✓</span>
                    <span>Join a forum</span>
                </li>
            </ul>
        </div>
    </div>

    <script>
        let isEditMode = false;

        function toggleAllEdit() {
            const editableFields = document.querySelectorAll('.editable-field:not(#password)');
            const editBtn = document.getElementById('editToggleBtn');
            const editBtnText = document.getElementById('editBtnText');
            const saveBtn = document.getElementById('saveBtn');

            isEditMode = !isEditMode;

            editableFields.forEach(field => {
                if (isEditMode) {
                    if (field.tagName === 'SELECT') {
                        field.disabled = false;
                    } else {
                        field.removeAttribute('readonly');
                    }
                    field.style.background = 'white';
                    field.style.borderColor = '#e9ecef';
                } else {
                    if (field.tagName === 'SELECT') {
                        field.disabled = true;
                    } else {
                        field.setAttribute('readonly', true);
                    }
                    field.style.background = '#f8f9fa';
                    field.style.borderColor = '#e9ecef';
                }
            });

            const passwordField = document.getElementById('password');
            if (isEditMode) {
                passwordField.value = '';
                passwordField.placeholder = 'Masukkan password baru';
                passwordField.removeAttribute('readonly');
            } else {
                passwordField.value = '********';
                passwordField.setAttribute('readonly', true);
            }

            if (isEditMode) {
                editBtnText.textContent = 'Done';
                editBtn.style.color = '#667eea';
                saveBtn.disabled = false;
            } else {
                editBtnText.textContent = 'Edit';
                editBtn.style.color = '#999';
                saveBtn.disabled = true;
            }
        }

        // Initialize progress circle
        document.addEventListener('DOMContentLoaded', function() {
            const percentage = {{ $completionPercentage }};
            const circle = document.getElementById('progressCircle');
            circle.style.background =
                `conic-gradient(#7cb342 0deg ${percentage * 3.6}deg, #e9ecef ${percentage * 3.6}deg 360deg)`;
        });

        function togglePasswordVisibility(button) {
            const container = button.closest('.password-container');
            const input = container.querySelector('input');

            if (input.type === 'password') {
                input.type = 'text';
                input.value = 'Password tersimpan (terenkripsi)';
                button.textContent = '🙈';
            } else {
                input.type = 'password';
                input.value = '********';
                button.textContent = '👁️';
            }
        }
    </script>
</body>

</html>
