<nav class="navbar-custom">
    <div class="logo">
        <img src="{{ asset('images/logogizi.png') }}" alt="logo" class="logo-icon">
        CalorieTrack
    </div>
    <div class="nav-center">
        <a href="{{ route('homepage') }}" class="{{ request()->routeIs('homepage') ? 'active' : '' }}">
            Homepage
        </a>
        <a href="{{ route('calculator') }}" class="{{ request()->routeIs('calculator*') ? 'active' : '' }}">
            Calculator
        </a>
        <a href="{{ route('recomend') }}" class="{{ request()->routeIs('recomend*') ? 'active' : '' }}">
            Recommendations
        </a>
        <a href="#" class="{{ request()->routeIs('forum*') ? 'active' : '' }}">Forum</a>
        <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile*') ? 'active' : '' }}">
            Profile
        </a>
    </div>
    <button class="get_started">Get Started</button>
    <button class="mobile-menu-toggle">☰</button>
</nav>

<script>
    // Mobile menu toggle functionality
    document.querySelector('.mobile-menu-toggle')?.addEventListener('click', function() {
        const navCenter = document.querySelector('.nav-center');
        navCenter.classList.toggle('show');
        this.textContent = this.textContent === '☰' ? '✕' : '☰';


    });

    document.addEventListener('DOMContentLoaded', function() {
        const navbar = document.querySelector('.navbar-custom');

        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) { // Mulai berubah setelah scroll 50px
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    });
</script>
