<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - CalorieTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>
    {{-- @include('components.navbar') --}}

    <div class="login-container">
        <div class="left-section">
            <div class="sign-in-form">
                <h1 class="sign-in-header">Sign in to your account</h1>

                <!-- Error Messages -->
                <div id="errorMessage" class="alert alert-danger d-none"></div>
                <div id="successMessage" class="alert alert-success d-none"></div>

                <form id="loginForm">
                    @csrf
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email"
                            placeholder="Enter your email address" required>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Enter your password" required>
                    </div>

                    <div class="remember-forgot">
                        <div class="remember-me">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Remember me</label>
                        </div>
                        <div class="forgot-password">
                            <a href="#">Forgot password?</a>
                        </div>
                    </div>

                    <button type="submit" class="btn-signin">Sign in</button>

                    <div class="signup-link">
                        Don't have an account? <a href="{{ route('register') }}">Sign up now</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="right-section">
            <div class="image-container">
                <img src="{{ asset('images/bg-kanan.png') }}" alt="Healthy Nutrition" class="food-image">
                <img src="{{ asset('images/plate.png') }}" alt="Healthy Nutrition" class="food-plate">
                <img src="{{ asset('images/apel.png') }}" alt="Apple" class="floating-fruit float1">
                <img src="{{ asset('images/tomat.png') }}" alt="tomat" class="floating-fruit float5">
                <img src="{{ asset('images/paprika.png') }}" alt="paprika" class="floating-fruit float4">
                <img src="{{ asset('images/jeruk.webp') }}" alt="jeruk" class="floating-fruit float2">
            </div>
        </div>
    </div>

    <script>
        function showMessage(message, type = 'error') {
            const errorDiv = document.getElementById('errorMessage');
            const successDiv = document.getElementById('successMessage');

            // Hide both messages first
            errorDiv.classList.add('d-none');
            successDiv.classList.add('d-none');

            if (type === 'error') {
                errorDiv.textContent = message;
                errorDiv.classList.remove('d-none');
            } else {
                successDiv.textContent = message;
                successDiv.classList.remove('d-none');
            }
        }

        // Form submission with validation
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            const remember = document.getElementById('remember').checked;

            const btn = document.querySelector('.btn-signin');
            const originalText = btn.textContent;

            // Basic validation
            if (!email || !password) {
                showMessage('Please fill in all required fields', 'error');
                return;
            }

            // Disable button and show loading state
            btn.textContent = 'Signing in...';
            btn.disabled = true;

            try {
                const response = await fetch("{{ route('login') }}", {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({
                        email: email,
                        password: password,
                        remember: remember
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showMessage(data.message, 'success');

                    // Small delay for user to see success message
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                } else {
                    // Handle error response
                    const errorMessage = data.message || 'Email atau password salah';
                    showMessage(errorMessage, 'error');

                    btn.textContent = originalText;
                    btn.disabled = false;
                }
            } catch (error) {
                console.error('Login error:', error);
                showMessage('Terjadi kesalahan. Silakan coba lagi.', 'error');

                btn.textContent = originalText;
                btn.disabled = false;
            }
        });

        // Alternative: Traditional form submission (uncomment if you prefer this approach)
        /*
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();

            if (!email || !password) {
                e.preventDefault();
                showMessage('Please fill in all required fields', 'error');
                return;
            }

            // Let the form submit naturally to the server
            // The server will handle the redirect
        });
        */
    </script>
</body>

</html>
