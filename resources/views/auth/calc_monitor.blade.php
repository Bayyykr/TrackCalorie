<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Calorie Tracker</title>
    <link rel="stylesheet" href="{{ asset('css/calc_monitor.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    @include('components.navbar')
    <div class="container">
        <div class="left-section">
            <div class="chef-placeholder">
                <img src="/images/chef_l.png" alt="">
            </div>
        </div>

        <div class="right-section">
            <h1 class="title">Monitor Your Daily Calories</h1>

            <div class="calorie-target">
                <span class="target-label">Your daily calorie target :</span>
                <span class="target-value">{{ number_format($tdee, 0, ',', '.') }} Kcal</span>
            </div>

            <!-- Display current calories consumed today -->
            <div class="current-calories">
                <span class="current-label">Today's consumed calories:</span>
                <span class="current-value" id="currentCalories">{{ number_format($todayCalories, 0, ',', '.') }}
                    Kcal</span>
            </div>

            <div class="input-section">
                <label class="input-label">Enter the food you've consumed</label>
                <input type="text" class="food-input" placeholder="Enter your food here" id="foodInput">
                <div class="suggestions" id="suggestions"></div>
            </div>

            <button class="calculate-btn" onclick="calculateCalories()">Calculate</button>

            <div class="action-buttons">
                <button class="action-btn" onclick="showMore()">More</button>
                <button class="action-btn" onclick="goBack()">Back</button>
            </div>

            <div class="result-section" id="resultSection">
                <p class="result-text" id="resultText"></p>
            </div>

            <!-- Loading indicator -->
            <div class="loading" id="loading" style="display: none;">
                <p>Searching menu...</p>
            </div>
        </div>
    </div>

    <script>
        // Auto-complete functionality
        let searchTimeout;
        document.getElementById('foodInput').addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();

            if (query.length < 2) {
                document.getElementById('suggestions').innerHTML = '';
                return;
            }

            searchTimeout = setTimeout(() => {
                searchMenu(query);
            }, 300);
        });

        function searchMenu(query) {
            fetch('/api/search-menu', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        query: query
                    })
                })
                .then(response => response.json())
                .then(data => {
                    displaySuggestions(data.menus);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function displaySuggestions(menus) {
            const suggestionsDiv = document.getElementById('suggestions');

            if (menus.length === 0) {
                suggestionsDiv.innerHTML = '';
                return;
            }

            let html = '<ul class="suggestion-list">';
            menus.forEach(menu => {
                html += `<li class="suggestion-item" onclick="selectMenu('${menu.nama}', ${menu.kalori})">
                    <span class="menu-name">${menu.nama}</span>
                    <span class="menu-calories">${menu.kalori} Kcal</span>
                </li>`;
            });
            html += '</ul>';

            suggestionsDiv.innerHTML = html;
        }

        function selectMenu(menuName, calories) {
            document.getElementById('foodInput').value = menuName;
            document.getElementById('suggestions').innerHTML = '';
        }

        function calculateCalories() {
            const foodInput = document.getElementById('foodInput');
            const resultSection = document.getElementById('resultSection');
            const resultText = document.getElementById('resultText');
            const loading = document.getElementById('loading');

            if (foodInput.value.trim() === '') {
                alert('Please enter some food items first!');
                return;
            }

            // Show loading
            loading.style.display = 'block';
            resultSection.style.display = 'none';

            // Send request to check menu and add calories
            fetch('/api/add-calories', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        menu_name: foodInput.value.trim()
                    })
                })
                .then(response => response.json())
                .then(data => {
                    loading.style.display = 'none';

                    if (data.success) {
                        resultText.innerHTML = `
                        <div class="success-result">
                            <strong>${data.menu.nama}</strong><br>
                            <span class="calories-added">+${data.menu.kalori} Kcal added to today's intake</span><br>
                            <span class="total-calories">Total today: ${data.total_today} Kcal</span>
                        </div>
                    `;

                        // Update current calories display
                        document.getElementById('currentCalories').textContent =
                            new Intl.NumberFormat('id-ID').format(data.total_today) + ' Kcal';

                        resultSection.style.display = 'block';

                        // Clear input
                        foodInput.value = '';
                    } else {
                        resultText.innerHTML = `
                        <div class="error-result">
                            <strong>Menu not found!</strong><br>
                            <span>"${foodInput.value}" is not available in our database.</span><br>
                            <span class="suggestion-text">Please try searching for a different menu item.</span>
                        </div>
                    `;
                        resultSection.style.display = 'block';
                    }

                    // Scroll to result
                    resultSection.scrollIntoView({
                        behavior: 'smooth'
                    });
                })
                .catch(error => {
                    loading.style.display = 'none';
                    console.error('Error:', error);

                    resultText.innerHTML = `
                    <div class="error-result">
                        <strong>Error occurred!</strong><br>
                        <span>Please try again later.</span>
                    </div>
                `;
                    resultSection.style.display = 'block';
                });
        }

        function showMore() {
            // Redirect to history or detailed view
            window.location.href = '/calorie-history';
        }

        function goBack() {
            const foodInput = document.getElementById('foodInput');
            const resultSection = document.getElementById('resultSection');
            const suggestions = document.getElementById('suggestions');

            foodInput.value = '';
            resultSection.style.display = 'none';
            suggestions.innerHTML = '';
        }

        // Enter key support
        document.getElementById('foodInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                // Clear suggestions first
                document.getElementById('suggestions').innerHTML = '';
                calculateCalories();
            }
        });

        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.input-section')) {
                document.getElementById('suggestions').innerHTML = '';
            }
        });
    </script>

    <!-- Include jQuery if not already included -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</body>

</html>
