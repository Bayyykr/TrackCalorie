<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - CalorieTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        html,
        body {
            overflow-x: hidden;
            height: 100%;
        }

        body {
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Animations */
        @keyframes wave {
            0% {
                transform: translateX(0) translateY(0);
            }

            25% {
                transform: translateX(5px) translateY(-8px);
            }

            50% {
                transform: translateX(0) translateY(0);
            }

            75% {
                transform: translateX(-5px) translateY(8px);
            }

            100% {
                transform: translateX(0) translateY(0);
            }
        }

        @keyframes slideInFromRight {
            0% {
                transform: translateX(100vw) rotate(0deg);
            }

            100% {
                transform: translateX(0) rotate(0deg);
            }
        }

        @keyframes fadeInFloat {
            0% {
                opacity: 0;
                transform: translateY(20px) scale(0.8);
            }

            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes floatAnimation {
            0% {
                transform: translateY(0) translateX(0) rotate(0deg);
            }

            25% {
                transform: translateY(-20px) translateX(15px) rotate(5deg);
            }

            50% {
                transform: translateY(10px) translateX(-15px) rotate(-5deg);
            }

            75% {
                transform: translateY(-15px) translateX(10px) rotate(3deg);
            }

            100% {
                transform: translateY(0) translateX(0) rotate(0deg);
            }
        }

        @keyframes rotatePlate {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Navbar */
        .navbar-custom {
            background: transparent;
            padding: 1.2rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .logo {
            display: flex;
            align-items: center;
            font-size: 26px;
            font-weight: bold;
            color: #2e7d32;
            text-decoration: none;
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            margin-right: 10px;
        }

        .nav-center {
            display: flex;
            gap: 2.8rem;
            align-items: center;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }

        .nav-center a {
            text-decoration: none;
            color: #555;
            font-weight: 500;
            font-size: 16px;
            padding: 0.5rem 0;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-center a:hover {
            color: #4caf50;
        }

        .nav-center a::after {
            content: "";
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background: #4caf50;
            transition: all 0.3s ease;
        }

        .nav-center a:hover::after {
            width: 100%;
            left: 0;
        }

        /* Login Container */
        .login-container {
            display: flex;
            min-height: 100vh;
        }

        .left-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: white;
            padding-top: 80px;
        }

        .right-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            background-color: white;
        }

        /* Image container */
        .image-container {
            position: relative;
            width: 100%;
            height: 100%;
            max-width: 100%;
            max-height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .food-image {
            width: 100%;
            height: 103%;
            position: relative;
            z-index: 3;
            filter: drop-shadow(0 15px 25px rgba(0, 0, 0, 0.2));
            animation: wave 8s ease-in-out infinite;
        }

        .food-plate {
            position: absolute;
            width: 400px;
            max-width: 80%;
            z-index: 10;
            animation: slideInFromRight 2s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards,
                rotatePlate 35s linear infinite;
            animation-delay: 0s, 2s;
        }

        .floating-fruit {
            position: absolute;
            z-index: 5;
            opacity: 0;
            animation: fadeInFloat 0.8s ease-out forwards,
                floatAnimation 8s ease-in-out infinite;
        }

        .float1 {
            animation-delay: 2s, 2.5s;
            width: 60px;
            height: 60px;
            top: 35%;
            left: 5%;
        }

        .float2 {
            animation-delay: 2.2s, 2.7s;
            width: 60px;
            height: 60px;
            top: 75%;
            left: 10%;
        }

        .float4 {
            animation-delay: 2.4s, 2.9s;
            width: 60px;
            height: 60px;
            top: 75%;
            left: 75%;
        }

        .float5 {
            animation-delay: 2.6s, 3.1s;
            width: 60px;
            height: 60px;
            top: 15%;
            left: 75%;
        }

        /* Sign Up Form - DIPERKECIL */
        .sign-up-form {
            max-width: 320px;
            width: 100%;
            padding: 0;
        }

        .sign-up-header {
            font-size: 22px;
            font-weight: 700;
            color: #333;
            margin-bottom: 1.5rem;
            text-align: center;
            position: relative;
            line-height: 1.2;
        }

        .sign-up-header::after {
            content: "";
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 3px;
            background: #4caf50;
            border-radius: 3px;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.3rem;
            display: block;
            font-size: 13px;
        }

        .form-control {
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 13px;
            transition: all 0.3s ease;
            background: #fafafa;
        }

        .form-control:focus {
            border-color: #4caf50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
            outline: none;
            background: white;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
            background: #fff5f5;
        }

        .terms-checkbox {
            display: flex;
            align-items: flex-start;
            margin: 1rem 0 1.5rem;
        }

        .terms-checkbox input {
            margin-top: 2px;
            margin-right: 7px;
            width: 14px;
            height: 14px;
            cursor: pointer;
            accent-color: #4caf50;
        }

        .terms-checkbox label {
            font-size: 12px;
            color: #555;
            cursor: pointer;
            line-height: 1.3;
        }

        .terms-checkbox label a {
            color: #4caf50;
            text-decoration: none;
            font-weight: 500;
        }

        .terms-checkbox label a:hover {
            text-decoration: underline;
        }

        .btn-signup {
            background: #4caf50;
            border: none;
            border-radius: 6px;
            padding: 10px;
            font-size: 14px;
            font-weight: 600;
            color: white;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .btn-signup::after {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg,
                    rgba(255, 255, 255, 0) 0%,
                    rgba(255, 255, 255, 0.2) 30%,
                    rgba(255, 255, 255, 0.5) 50%,
                    rgba(255, 255, 255, 0.2) 70%,
                    rgba(255, 255, 255, 0) 100%);
            transform: rotate(30deg);
            transition: all 0.9s ease;
            z-index: -1;
            opacity: 0;
        }

        .btn-signup:hover {
            background: #45a049;
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(76, 175, 80, 0.4);
        }

        .btn-signup:hover::after {
            opacity: 1;
            top: -100%;
            left: 100%;
        }

        .btn-signup:active {
            transform: translateY(-1px);
        }

        .signin-link {
            text-align: center;
            margin-top: 1rem;
            color: #666;
            font-size: 13px;
        }

        .signin-link a {
            color: #4caf50;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .signin-link a:hover {
            text-decoration: underline;
            color: #3d8b40;
        }

        /* Google Sign Up Button */
        .google-signup {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #555;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 1.5rem 0 1rem;
        }

        .google-signup:hover {
            background: #f8f9fa;
            border-color: #ccc;
        }

        .google-signup i {
            color: #4285F4;
            font-size: 15px;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 18px 0;
            position: relative;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #ddd;
        }

        .divider span {
            padding: 0 10px;
            color: #777;
            font-size: 12px;
            background: white;
            position: relative;
            z-index: 1;
        }

        .divider-title {
            text-align: center;
            color: #777;
            font-size: 13px;
            margin: 18px 0 0;
            font-weight: 500;
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 28px;
            color: #666;
            cursor: pointer;
            z-index: 1001;
        }

        /* Alert Messages */
        .alert {
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.375rem;
            font-size: 13px;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .d-none {
            display: none !important;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .nav-center {
                gap: 2.2rem;
            }

            .sign-up-header {
                font-size: 21px;
            }

            .image-container {
                max-width: 450px;
                height: 450px;
            }
        }

        @media (max-width: 992px) {
            .navbar-custom {
                padding: 1rem 5%;
            }

            .nav-center {
                gap: 1.8rem;
            }

            .sign-up-header {
                font-size: 20px;
            }

            .image-container {
                max-width: 400px;
                height: 400px;
            }

            .food-plate {
                width: 350px;
            }
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }

            .navbar-custom {
                padding: 0.9rem 5%;
            }

            .left-section {
                padding: 2rem 1.5rem;
                order: 2;
                padding-top: 70px;
            }

            .right-section {
                padding: 1.5rem;
                order: 1;
                min-height: 400px;
                padding-top: 70px;
            }

            .image-container {
                max-width: 350px;
                height: 350px;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .nav-center {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: white;
                flex-direction: column;
                gap: 0;
                padding: 1rem 0;
                box-shadow: 0 8px 15px rgba(0, 0, 0, 0.08);
            }

            .nav-center.show {
                display: flex;
            }

            .nav-center a {
                padding: 1rem 2rem;
                width: 100%;
                font-size: 17px;
            }

            .nav-center a:hover {
                background: #f8f9fa;
            }

            .sign-up-header {
                font-size: 20px;
                margin-bottom: 1.3rem;
            }

            .float1 {
                top: 15%;
                left: 25%;
            }

            .float2 {
                top: 65%;
                left: 15%;
            }

            .float4 {
                top: 75%;
                left: 80%;
            }

            .float5 {
                top: 10%;
                left: 80%;
            }

            .food-plate {
                width: 300px;
            }
        }

        @media (max-width: 576px) {
            .logo {
                font-size: 22px;
            }

            .logo-icon {
                width: 32px;
                height: 32px;
                font-size: 16px;
            }

            .left-section {
                padding: 1.5rem 1rem;
                padding-top: 60px;
            }

            .right-section {
                padding: 1rem;
                min-height: 300px;
                padding-top: 60px;
            }

            .sign-up-header {
                font-size: 19px;
                margin-bottom: 1.2rem;
            }

            .form-group {
                margin-bottom: 0.9rem;
            }

            .sign-up-form {
                max-width: 300px;
            }

            .image-container {
                max-width: 300px;
                height: 300px;
            }

            .float1 {
                top: 10%;
                left: 20%;
                width: 45px;
                height: 45px;
            }

            .float2 {
                top: 70%;
                left: 10%;
                width: 40px;
                height: 40px;
            }

            .float4 {
                top: 80%;
                left: 85%;
                width: 50px;
                height: 50px;
            }

            .float5 {
                top: 5%;
                left: 85%;
                width: 35px;
                height: 35px;
            }

            .food-plate {
                width: 250px;
            }

            .form-control {
                padding: 7px 10px;
                font-size: 12px;
            }

            .btn-signup {
                padding: 9px;
                font-size: 13px;
            }

            .google-signup {
                padding: 7px;
                font-size: 13px;
                margin: 1.3rem 0 0.8rem;
            }

            .terms-checkbox {
                margin: 0.8rem 0 1.2rem;
            }
        }
    </style>
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
            <div class="sign-up-form">
                <h1 class="sign-up-header">Sign Up</h1>

                <!-- Error/Success Messages -->
                <div id="errorMessage" class="alert alert-danger d-none"></div>
                <div id="successMessage" class="alert alert-success d-none"></div>

                <form id="signupForm">
                    @csrf
                    <div class="form-group">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name"
                            placeholder="Enter your full name" required>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email"
                            placeholder="Enter your email address" required>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Create a password (min. 6 characters)" required>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation"
                            name="password_confirmation" placeholder="Confirm your password" required>
                    </div>

                    <div class="terms-checkbox">
                        <input type="checkbox" id="terms" required>
                        <label for="terms">
                            By signing up you agree with the <a href="#">Privacy policy</a> and <a
                                href="#">Terms</a> of
                            CalorieTrack.
                        </label>
                    </div>

                    <button type="submit" class="btn-signup">Sign up</button>
                </form>

                <div class="divider-title">Or sign up with</div>

                <button class="google-signup">
                    <i class="fab fa-google"></i>
                    Sign up with Google
                </button>

                <div class="signin-link">
                    Already have an account? <a href="{{ route('login') }}">Sign In</a>
                </div>
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

        function validatePasswords() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;
            const confirmField = document.getElementById('password_confirmation');

            if (password !== confirmPassword) {
                confirmField.classList.add('is-invalid');
                return false;
            } else {
                confirmField.classList.remove('is-invalid');
                return true;
            }
        }

        // Mobile menu toggle functionality
        document.querySelector('.mobile-menu-toggle').addEventListener('click', function() {
            const navCenter = document.querySelector('.nav-center');
            navCenter.classList.toggle('show');
            this.textContent = this.textContent === '☰' ? '✕' : '☰';
        });

        // Real-time password confirmation validation
        document.getElementById('password_confirmation').addEventListener('input', validatePasswords);

        // Form submission with validation and actual server request
        document.getElementById('signupForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            const passwordConfirmation = document.getElementById('password_confirmation').value.trim();
            const terms = document.getElementById('terms').checked;

            const btn = document.querySelector('.btn-signup');
            const originalText = btn.textContent;

            // Client-side validation
            if (!name || !email || !password || !passwordConfirmation) {
                showMessage('Please fill in all required fields', 'error');
                return;
            }

            if (password.length < 6) {
                showMessage('Password must be at least 6 characters long', 'error');
                return;
            }

            if (!validatePasswords()) {
                showMessage('Passwords do not match', 'error');
                return;
            }

            if (!terms) {
                showMessage('Please accept the terms and conditions', 'error');
                return;
            }

            // Show loading state
            btn.textContent = 'Creating account...';
            btn.disabled = true;

            try {
                const response = await fetch("{{ route('register') }}", {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({
                        name: name,
                        email: email,
                        password: password,
                        password_confirmation: passwordConfirmation
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showMessage(data.message, 'success');
                    btn.textContent = '✓ Account created!';
                    btn.style.background = '#2e7d32';

                    // Reset form
                    this.reset();

                    // Redirect to login page after delay
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 2000);
                } else {
                    // Handle validation errors
                    if (data.errors) {
                        const firstError = Object.values(data.errors)[0];
                        showMessage(Array.isArray(firstError) ? firstError[0] : firstError, 'error');
                    } else {
                        showMessage(data.message || 'Registration failed. Please try again.', 'error');
                    }

                    btn.textContent = originalText;
                    btn.style.background = '#4caf50';
                    btn.disabled = false;
                }
            } catch (error) {
                console.error('Registration error:', error);
                showMessage('Network error. Please check your connection and try again.', 'error');

                btn.textContent = originalText;
                btn.style.background = '#4caf50';
                btn.disabled = false;
            }
        });
    </script>
</body>

</html>
