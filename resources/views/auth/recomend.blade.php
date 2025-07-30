@auth
    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Food Menu - Scrollable Layout</title>
        <link rel="stylesheet" href="{{ asset('css/recomend.css') }}">
        <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
        <style>
            .main-container {
                background: transparent !important;
            }

            .food-cards-container {
                background: transparent !important;
            }

            .loading-overlay {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(255, 255, 255, 0.8);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.2em;
                color: #666;
                z-index: 10;
            }

            .section {
                position: relative;
            }
        </style>
    </head>

    <body>
        @include('components.navbar')

        <header class="header">
            <h1 class="greeting">Hello {{ Auth::user()->name }}</h1>
            <div class="underline"></div>
            <p class="subtitle">Pick one category to filter meals or search by name</p>

            <div class="search-container">
                <div class="search-box">
                    <input type="text" class="search-input" id="searchInput" placeholder="Search your meals">
                    <button class="search-btn" id="searchBtn">🔍</button>
                </div>
                <p>or</p>
                <select class="dropdown" id="categoryFilter">
                    <option value="all">Select your dining preference</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}">{{ $category }}</option>
                    @endforeach
                </select>
                <button class="my-list-btn" type="button">My list menu</button>
            </div>
        </header>

        <main class="main-container">
            <section class="section" style="background: transparent;" id="recommendationsSection">
                <div class="food-cards-container" style="background: transparent;" id="foodCardsContainer">
                    @foreach ($pageRows as $row)
                        <div class="food-cards-row">
                            @foreach ($row as $item)
                                <div class="food-card">
                                    <div class="food-card-image">
                                        @if ($item->image_path)
                                            <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}"
                                                class="food-card-img"
                                                onerror="this.onerror=null; this.src='{{ asset('images/fallback.jpg') }}'">
                                        @else
                                            <div class="food-card-fallback"
                                                style="background: linear-gradient(45deg, {{ $item->image_color ?? '#4caf50' }});">
                                                <span>{{ substr($item->name, 0, 15) }}</span>
                                            </div>
                                        @endif
                                        <div class="food-card-badge">
                                            <span>⭐</span> {{ $item->calorie_range }}
                                        </div>
                                    </div>
                                    <div class="food-card-content">
                                        <h3>{{ $item->name }}</h3>
                                        <p>{{ Str::limit($item->description, 100) }}</p>
                                        @if ($item->category)
                                            <div style="margin: 10px 0;">
                                                <span
                                                    style="background: #e8f5e8; color: #2e7d32; padding: 4px 8px; border-radius: 12px; font-size: 0.8em;">
                                                    {{ $item->category }}
                                                </span>
                                            </div>
                                        @endif
                                        <button class="food-card-button">Read more</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </section>
        </main>

        <!-- Modal Popup -->
        <div class="popup-overlay" id="menuListPopup">
            <div class="popup-container">
                <img src="{{ asset('images/chef.png') }}" alt="Menu Header" class="popup-header-image">
                <div class="popup-content">
                    <h3 class="popup-title">Welcome to your menu!</h3>
                    <p class="popup-message">Here is the list of menus you have saved</p>

                    <div class="menu-list" id="userMenuList">
                        <!-- Data akan diisi oleh JavaScript -->
                    </div>

                    <button class="popup-ok-btn" type="button">OK</button>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize filter functionality
                initializeFilters();
                initializePopup();
            });

            function initializeFilters() {
                const searchInput = document.getElementById('searchInput');
                const searchBtn = document.getElementById('searchBtn');
                const categoryFilter = document.getElementById('categoryFilter');

                let searchTimeout;

                // Search functionality
                function performSearch() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        filterRecommendations();
                    }, 500); // Delay 500ms untuk menghindari terlalu banyak request
                }

                searchInput.addEventListener('input', performSearch);
                searchBtn.addEventListener('click', filterRecommendations);

                // Category filter
                categoryFilter.addEventListener('change', filterRecommendations);

                // Enter key untuk search
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        filterRecommendations();
                    }
                });
            }

            function initializePopup() {
                // Event listener untuk tombol My List
                const myListBtn = document.querySelector('.my-list-btn');

                if (myListBtn) {
                    myListBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        const popup = document.getElementById('menuListPopup');
                        if (popup) {
                            popup.classList.add('active');
                            loadUserMenus();
                        }
                    });
                }

                // Event listener untuk close popup
                const popupOkBtn = document.querySelector('.popup-ok-btn');
                if (popupOkBtn) {
                    popupOkBtn.addEventListener('click', function() {
                        document.getElementById('menuListPopup').classList.remove('active');
                    });
                }

                // Close popup ketika klik overlay
                const popupOverlay = document.getElementById('menuListPopup');
                if (popupOverlay) {
                    popupOverlay.addEventListener('click', function(e) {
                        if (e.target === this) {
                            this.classList.remove('active');
                        }
                    });
                }
            }

            async function filterRecommendations() {
                const searchValue = document.getElementById('searchInput').value;
                const categoryValue = document.getElementById('categoryFilter').value;
                const container = document.getElementById('foodCardsContainer');
                const section = document.getElementById('recommendationsSection');

                // Show loading
                if (!section.querySelector('.loading-overlay')) {
                    const loadingDiv = document.createElement('div');
                    loadingDiv.className = 'loading-overlay';
                    loadingDiv.innerHTML = 'Loading recommendations...';
                    section.appendChild(loadingDiv);
                }

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    const params = new URLSearchParams();
                    params.append('filter', categoryValue);
                    if (searchValue.trim()) {
                        params.append('search', searchValue.trim());
                    }

                    const response = await fetch(`/recomend?${params}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const result = await response.json();

                    if (result.success) {
                        renderRecommendations(result.data);
                    } else {
                        throw new Error('Failed to filter recommendations');
                    }

                } catch (error) {
                    console.error('Error filtering recommendations:', error);
                    container.innerHTML = `
                    <div style="text-align: center; padding: 40px; color: #666;">
                        <p>Failed to load recommendations</p>
                        <button onclick="location.reload()" style="padding: 10px 20px; background: #4caf50; color: white; border: none; border-radius: 5px; cursor: pointer;">
                            Reload Page
                        </button>
                    </div>
                `;
                } finally {
                    // Hide loading
                    const loading = section.querySelector('.loading-overlay');
                    if (loading) {
                        loading.remove();
                    }
                }
            }

            function renderRecommendations(pageRows) {
                const container = document.getElementById('foodCardsContainer');

                if (!pageRows || pageRows.length === 0) {
                    container.innerHTML = `
                    <div style="text-align: center; padding: 40px; color: #666;">
                        <p>No recommendations found</p>
                        <button onclick="clearFilters()" style="padding: 10px 20px; background: #4caf50; color: white; border: none; border-radius: 5px; cursor: pointer;">
                            Clear Filters
                        </button>
                    </div>
                `;
                    return;
                }

                let html = '';
                pageRows.forEach(row => {
                    html += '<div class="food-cards-row">';
                    row.forEach(item => {
                        const imageHtml = item.image_path ?
                            `<img src="/storage/${item.image_path}" alt="${item.name}" class="food-card-img" onerror="this.onerror=null; this.src='/images/fallback.jpg'">` :
                            `<div class="food-card-fallback" style="background: linear-gradient(45deg, ${item.image_color || '#4caf50'});"><span>${item.name.substr(0, 15)}</span></div>`;

                        const categoryBadge = item.category ?
                            `<div style="margin: 10px 0;"><span style="background: #e8f5e8; color: #2e7d32; padding: 4px 8px; border-radius: 12px; font-size: 0.8em;">${item.category}</span></div>` :
                            '';

                        html += `
                        <div class="food-card">
                            <div class="food-card-image">
                                ${imageHtml}
                                <div class="food-card-badge">
                                    <span>⭐</span> ${item.calorie_range}
                                </div>
                            </div>
                            <div class="food-card-content">
                                <h3>${item.name}</h3>
                                <p>${item.description.length > 100 ? item.description.substr(0, 100) + '...' : item.description}</p>
                                ${categoryBadge}
                                <button class="food-card-button">Read more</button>
                            </div>
                        </div>
                    `;
                    });
                    html += '</div>';
                });

                container.innerHTML = html;
            }

            function clearFilters() {
                document.getElementById('searchInput').value = '';
                document.getElementById('categoryFilter').value = 'all';
                location.reload(); // Simple reload to show all items
            }

            // User menu functions (unchanged)
            async function loadUserMenus() {
                const menuList = document.getElementById('userMenuList');

                if (!menuList) {
                    console.error('Menu list container not found!');
                    return;
                }

                menuList.innerHTML = '<div class="loading-message">Loading menus...</div>';

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    const response = await fetch('/recomend', {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Response is not JSON');
                    }

                    const menus = await response.json();

                    if (Array.isArray(menus) && menus.length > 0) {
                        menuList.innerHTML = menus.map(menu => `
                        <div class="menu-item">
                            <span class="menu-name">${escapeHtml(menu.name)}</span>
                            <div>
                                <span class="menu-calories">${menu.calories} Kcal</span>
                                <button class="delete-btn" data-id="${menu.id}" onclick="deleteMenu(${menu.id})">×</button>
                            </div>
                        </div>
                    `).join('');
                    } else {
                        menuList.innerHTML = '<div class="empty-message">No saved menus yet</div>';
                    }

                } catch (error) {
                    console.error('Error loading menus:', error);
                    menuList.innerHTML = `
                    <div class="error-message">
                        Failed to load menus: ${error.message}
                        <br><button onclick="loadUserMenus()" style="margin-top: 10px; padding: 5px 10px; background: #4caf50; color: white; border: none; border-radius: 5px; cursor: pointer;">Retry</button>
                    </div>
                `;
                }
            }

            async function deleteMenu(menuId) {
                if (!confirm('Are you sure you want to delete this menu?')) {
                    return;
                }

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    const response = await fetch(`/recomend/${menuId}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    if (response.ok) {
                        loadUserMenus();
                    } else {
                        throw new Error('Failed to delete menu');
                    }
                } catch (error) {
                    console.error('Error deleting menu:', error);
                    alert('Failed to delete menu. Please try again.');
                }
            }

            function escapeHtml(text) {
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text.replace(/[&<>"']/g, function(m) {
                    return map[m];
                });
            }
        </script>
    </body>

    </html>
@else
    <script>
        window.location = "/login";
    </script>
@endguest
