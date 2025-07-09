@include('components.head')

@include('components.navbar')
<!-- Main Content -->
<div class="main-container main-calc">
    <div class="content-wrapper content-calc">
        <div class="layout-row">
            <!-- Left Column - Food Image -->
            <div class="image-column">
                <div class="image-section">
                    <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80"
                        alt="Healthy food bowl with vegetables, fruits, and protein" class="food-image-calc">
                </div>
            </div>

            <!-- Right Column - Calculator -->
            <div class="form-column">
                <div class="calculator-section">
                    <div class="calculator-title">
                        <h2>Calculate Your BMR & TDEE</h2>
                        <p class="text-muted">Discover your daily calorie needs for weight management</p>
                    </div>

                    <!-- Gender Selection -->
                    <div class="gender-group">
                        <label class="gender-label">Gender</label>
                        <div class="gender-options">
                            <div class="gender-btn male active" id="maleBtn">
                                <i class="fas fa-mars"></i>
                                <span>Male</span>
                            </div>
                            <div class="gender-btn female" id="femaleBtn">
                                <i class="fas fa-venus"></i>
                                <span>Female</span>
                            </div>
                        </div>
                    </div>

                    <!-- Age Input -->
                    <div class="mb-3">
                        <label for="age" class="form-label">Age</label>
                        <input type="number" class="form-control" id="age" placeholder="Enter your age" min="15"
                            max="100">
                        <div class="form-text">Years old</div>
                    </div>

                    <!-- Weight Input -->
                    <div class="mb-3">
                        <label for="weight" class="form-label">Weight</label>
                        <input type="number" class="form-control" id="weight" placeholder="Enter your weight" min="30"
                            max="200">
                        <div class="form-text">kg</div>
                    </div>

                    <!-- Height Input -->
                    <div class="mb-3">
                        <label for="height" class="form-label">Height</label>
                        <input type="number" class="form-control" id="height" placeholder="Enter your height" min="100"
                            max="250">
                        <div class="form-text">cm</div>
                    </div>

                    <!-- Activity Level -->
                    <div class="mb-4">
                        <label for="activity" class="form-label">Activity Level</label>
                        <select class="form-select" id="activity">
                            <option value="" disabled selected>Activity Level</option>
                            <option value="1.2">Sedentary (little or no exercise)</option>
                            <option value="1.375">Lightly active (light exercise 1-3 days/week)</option>
                            <option value="1.55">Moderately active (moderate exercise 3-5 days/week)</option>
                            <option value="1.725">Very active (hard exercise 6-7 days/week)</option>
                            <option value="1.9">Extra active (very hard exercise & physical job)</option>
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-calculate text-white" id="calculateBtn">
                            <i class="fas fa-calculator me-2"></i>Calculate
                        </button>
                        <button type="button" class="btn btn-clear" id="clearBtn">
                            <i class="fas fa-eraser me-2"></i>Clear
                        </button>
                    </div>

                    <!-- Results Section -->
                    <div class="results-section" id="resultsSection">
                        <div class="results-cards">
                            <div class="result-card">
                                <h6>Basal Metabolic Rate (BMR)</h6>
                                <div class="result-value" id="bmrResult">-</div>
                                <div class="result-unit">calories per day</div>
                            </div>
                            <div class="result-card">
                                <h6>Total Daily Energy Expenditure (TDEE)</h6>
                                <div class="result-value" id="tdeeResult">-</div>
                                <div class="result-unit">calories per day</div>
                            </div>
                        </div>
                        <div class="interpretation">
                            <h6>What this means for you</h6>
                            <p>Your BMR is the number of calories your body needs to perform basic life-sustaining
                                functions. Your TDEE is your total daily calorie expenditure including physical
                                activity. To maintain your current weight, consume calories equal to your TDEE.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Gender selection
        const maleBtn = document.getElementById('maleBtn');
        const femaleBtn = document.getElementById('femaleBtn');
        let selectedGender = 'male';

        maleBtn.addEventListener('click', () => {
            maleBtn.classList.add('active');
            femaleBtn.classList.remove('active');
            selectedGender = 'male';
        });

        femaleBtn.addEventListener('click', () => {
            femaleBtn.classList.add('active');
            maleBtn.classList.remove('active');
            selectedGender = 'female';
        });

        // BMR calculation using Mifflin-St Jeor Equation
        function calculateBMR(weight, height, age, gender) {
            if (gender === 'male') {
                return (10 * weight) + (6.25 * height) - (5 * age) + 5;
            } else {
                return (10 * weight) + (6.25 * height) - (5 * age) - 161;
            }
        }

        function calculateTDEE(bmr, activityLevel) {
            return bmr * activityLevel;
        }

        // Calculate button event
        document.getElementById('calculateBtn').addEventListener('click', function() {
            const age = parseFloat(document.getElementById('age').value);
            const weight = parseFloat(document.getElementById('weight').value);
            const height = parseFloat(document.getElementById('height').value);
            const activityLevel = parseFloat(document.getElementById('activity').value);

            // Validation
            if (!age || !weight || !height || !activityLevel) {
                alert('Please fill in all fields');
                return;
            }

            if (age < 15 || age > 100) {
                alert('Please enter a valid age between 15 and 100');
                return;
            }

            if (weight < 30 || weight > 200) {
                alert('Please enter a valid weight between 30 and 200 kg');
                return;
            }

            if (height < 100 || height > 250) {
                alert('Please enter a valid height between 100 and 250 cm');
                return;
            }

            // Calculate BMR and TDEE
            const bmr = calculateBMR(weight, height, age, selectedGender);
            const tdee = calculateTDEE(bmr, activityLevel);

            // Display results
            document.getElementById('bmrResult').textContent = Math.round(bmr).toLocaleString();
            document.getElementById('tdeeResult').textContent = Math.round(tdee).toLocaleString();

            // Show results section
            document.getElementById('resultsSection').style.display = 'block';

            // Smooth scroll to results
            document.getElementById('resultsSection').scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        });

        // Clear button event
        document.getElementById('clearBtn').addEventListener('click', function() {
            // Clear all inputs
            document.getElementById('age').value = '';
            document.getElementById('weight').value = '';
            document.getElementById('height').value = '';
            document.getElementById('activity').selectedIndex = 0;

            // Reset results
            document.getElementById('bmrResult').textContent = '-';
            document.getElementById('tdeeResult').textContent = '-';
            document.getElementById('resultsSection').style.display = 'none';

            // Reset gender to male
            maleBtn.classList.add('active');
            femaleBtn.classList.remove('active');
            selectedGender = 'male';
        });

        // Input validation on keyup
        document.getElementById('age').addEventListener('keyup', function() {
            if (this.value > 100) this.value = 100;
            if (this.value < 0) this.value = '';
        });

        document.getElementById('weight').addEventListener('keyup', function() {
            if (this.value > 200) this.value = 200;
            if (this.value < 0) this.value = '';
        });

        document.getElementById('height').addEventListener('keyup', function() {
            if (this.value > 250) this.value = 250;
            if (this.value < 0) this.value = '';
        });
</script>
