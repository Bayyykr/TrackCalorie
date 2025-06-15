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

<script>
    // Mobile menu toggle functionality
    document.querySelector('.mobile-menu-toggle')?.addEventListener('click', function() {
        const navCenter = document.querySelector('.nav-center');
        navCenter.classList.toggle('show');
        this.textContent = this.textContent === '☰' ? '✕' : '☰';
    });
</script>
