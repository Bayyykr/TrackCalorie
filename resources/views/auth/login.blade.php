<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - CalorieTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>
    <nav class="navbar-custom">
        <div class="logo">
            <img src="{{ asset('images/logogizi.png') }}" alt="logo" class="logo-icon">
            CalorieTrack
        </div>
        <div class="nav-center">
            <a href="#">Homepage</a>
            <a href="#">Calculator</a>
            <a href="#">Recommendations</a>
            <a href="#">Forum</a>
        </div>
        <button class="mobile-menu-toggle">☰</button>
    </nav>

    <div class="login-container">
        <div class="left-section">
            <div class="sign-in-form">
                <h1 class="sign-in-header">Sign in to your account</h1>

                <form>
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email"
                            placeholder="Enter your email address">
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Enter your password">
                    </div>

                    <div class="remember-forgot">
                        <div class="remember-me">
                            <input type="checkbox" id="remember">
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
        // Mobile menu toggle functionality
        document.querySelector('.mobile-menu-toggle').addEventListener('click', function() {
            const navCenter = document.querySelector('.nav-center');
            navCenter.classList.toggle('show');
            this.textContent = this.textContent === '☰' ? '✕' : '☰';
        });

        // Form submission with validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const btn = document.querySelector('.btn-signin');
            const originalText = btn.textContent;

            if (email && password) {
                // Simulate loading state
                btn.textContent = 'Signing in...';
                btn.disabled = true;

                setTimeout(() => {
                    // Simulate successful login
                    btn.textContent = '✓ Success!';
                    btn.style.background = '#2e7d32';

                    setTimeout(() => {
                        btn.textContent = originalText;
                        btn.style.background = '#4caf50';
                        btn.disabled = false;
                        this.reset();
                    }, 1500);
                }, 1500);
            } else {
                alert('Please fill in all required fields');
            }
        });
    </script>
</body>

</html>