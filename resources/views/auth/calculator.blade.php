@include('components.head')

@include('components.navbar')
<!-- Main Content -->
<div class="main-container">
    <div class="content-wrapper">
        <div class="layout-row">
            <!-- Left Column - Food Image -->
            <div class="image-column">
                <div class="image-section">
                    <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80"
                        alt="Healthy food bowl with vegetables, fruits, and protein" class="food-image">
                </div>
            </div>

            <!-- Right Column - Calculator -->
            <div class="form-column">
                <div class="calculator-section">
                    <div class="calculator-title">
                        <h2>Calculate Your BMR & TDEE</h2>
                    </div>

                    <form>
                        <!-- Gender Selection -->
                        <div class="form-group">
                            <label class="form-label">Gender</label>
                            <div class="gender-options">
                                <div class="gender-option">
                                    <input type="radio" id="male" name="gender" value="male">
                                    <label for="male">
                                        <img src="{{ asset('images/male.png') }}" alt="male" width="30px"
                                            height="30px">
                                        <p>Male</p>
                                    </label>
                                </div>
                                <div class="gender-option">
                                    <input type="radio" id="female" name="gender" value="female">
                                    <label for="female">
                                        <img src="{{ asset('images/female.png') }}" alt="female" width="30px"
                                            height="30px">
                                        <p>Female</p>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Age Input -->
                        <div class="form-group">
                            <label for="age" class="form-label">Age</label>
                            <input type="number" id="age" class="form-control" placeholder="Enter your age"
                                min="1" max="120">
                            <div class="form-text">years old</div>
                        </div>

                        <!-- Weight Input -->
                        <div class="form-group">
                            <label for="weight" class="form-label">Weight</label>
                            <input type="number" id="weight" class="form-control" placeholder="Enter your weight"
                                min="1" step="0.1">
                            <div class="form-text">Kg</div>
                        </div>

                        <!-- Height Input -->
                        <div class="form-group">
                            <label for="height" class="form-label">Height</label>
                            <input type="number" id="height" class="form-control" placeholder="Enter your height"
                                min="1" step="0.1">
                            <div class="form-text">Cm</div>
                        </div>

                        <!-- Activity Level -->
                        <div class="form-group">
                            <label for="activity" class="form-label">Activity Level</label>
                            <select id="activity" class="form-control">
                                <option value="" disabled selected>Select Activity Level</option>
                                <option value="1.2">Sedentary</option>
                                <option value="1.375">Lightly active</option>
                                <option value="1.55">Moderately active</option>
                                <option value="1.725">Very active</option>
                                <option value="1.9">Extra active</option>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-calculate text-white" id="calculateBtn">
                                Calculate
                            </button>
                            <button type="button" class="btn btn-clear" id="clearBtn">
                                Clear
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
                                    activity. To
                                    maintain your current weight, consume calories equal to your TDEE.</p>
                            </div>
                        </div>
                </div>
            </div>
        </div>

        {{-- OK Button --}}
        <button onclick="closeModal()" class="ok-btn">OK</button>
    </div>
</div>


<script>
    function calculateBMR(weight, height, age, gender) {
        if (gender === 'male') {
            // Rumus untuk pria: BMR = 88.362 + (13.397 x bb(kg)) + (4.799 x tb(cm)) - (5.677 x usia(tahun))
            return Math.round(88.362 + (13.397 * weight) + (4.799 * height) - (5.677 * age));
        } else {
            // Rumus untuk wanita: BMR = 447.593 + (9.247 x bb(kg)) + (3.098 x tb(cm)) - (4.330 x usia)
            return Math.round(447.593 + (9.247 * weight) + (3.098 * height) - (4.330 * age));
        }
    }

    // Fungsi perhitungan TDEE sesuai rumus: TDEE = BMR x Tingkat Aktivitas
    function calculateTDEE(bmr, activityMultiplier) {
        return Math.round(bmr * activityMultiplier);
    }

    // Fungsi perhitungan BMI sesuai rumus: BMI = berat badan (kg) / (tinggi badan (m))²
    function calculateBMI(weight, height) {
        // height dalam cm, konversi ke meter dengan membagi 100
        const heightInMeters = height / 100;
        // BMI = bb(kg) / (tb(m))²
        return Math.round((weight / (heightInMeters * heightInMeters)) * 10) / 10;
    }

    // Fungsi untuk menampilkan modal
    function showModal(bmr, tdee, bmi) {
        // Update hasil di modal
        document.getElementById('bmrResult').textContent = bmr.toLocaleString('id-ID') + ' kcal/day';
        document.getElementById('tdeeResult').textContent = tdee.toLocaleString('id-ID') + ' kcal/day';
        document.getElementById('bmiResult').textContent = bmi;

        // Tampilkan modal
        const modal = document.getElementById('calculationModal');
        modal.classList.add('active');

        // Disable scroll
        document.body.style.overflow = 'hidden';
    }

    // Fungsi untuk menutup modal
    function closeModal() {
        const modal = document.getElementById('calculationModal');
        modal.classList.remove('active');

        // Enable scroll kembali
        setTimeout(() => {
            document.body.style.overflow = 'auto';
        }, 300);
    }

    // Fungsi validasi form
    function validateInputs() {
        const gender = document.querySelector('input[name="gender"]:checked');
        const age = document.getElementById('age').value;
        const weight = document.getElementById('weight').value;
        const height = document.getElementById('height').value;
        const activity = document.getElementById('activity').value;

        if (!gender) {
            alert('Pilih jenis kelamin terlebih dahulu!');
            return false;
        }

        if (!age || age <= 0 || age > 120) {
            alert('Masukkan umur yang valid (1-120 tahun)!');
            document.getElementById('age').focus();
            return false;
        }

        if (!weight || weight <= 0) {
            alert('Masukkan berat badan yang valid!');
            document.getElementById('weight').focus();
            return false;
        }

        if (!height || height <= 0) {
            alert('Masukkan tinggi badan yang valid!');
            document.getElementById('height').focus();
            return false;
        }

        if (!activity) {
            alert('Pilih tingkat aktivitas!');
            document.getElementById('activity').focus();
            return false;
        }

        return true;
    }

    // Event listener untuk form
    document.addEventListener('DOMContentLoaded', function() {
        // Event listener untuk tombol Calculate
        const calculateBtn = document.getElementById('calculateBtn');
        if (calculateBtn) {
            calculateBtn.addEventListener('click', function(e) {
                e.preventDefault();

                // Validasi input
                if (!validateInputs()) {
                    return;
                }

                // Ambil data from form
                const gender = document.querySelector('input[name="gender"]:checked').value;
                const age = parseInt(document.getElementById('age').value);
                const weight = parseFloat(document.getElementById('weight').value);
                const height = parseFloat(document.getElementById('height').value);
                const activityLevel = document.getElementById('activity').value;

                // Mapping activity level dari value ke multiplier (sama seperti di PHP)
                const activityMapping = {
                    '1.2': 1.2, // sedentary
                    '1.375': 1.375, // light
                    '1.55': 1.55, // moderate
                    '1.725': 1.725, // active
                    '1.9': 1.9 // very_active
                };

                // Hitung BMI terlebih dahulu (sesuai urutan di gambar)
                const bmi = calculateBMI(weight, height);

                // Hitung BMR
                const bmr = calculateBMR(weight, height, age, gender);

                // Hitung TDEE dengan multiplier yang tepat
                const activityMultiplier = activityMapping[activityLevel] || 1.2;
                const tdee = calculateTDEE(bmr, activityMultiplier);

                // Tampilkan modal dengan hasil
                showModal(bmr, tdee, bmi);
            });
        }

        // Close modal ketika klik di luar area modal
        const modal = document.getElementById('calculationModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });
        }

        // Close modal dengan tombol Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('calculationModal');
                if (modal && modal.classList.contains('active')) {
                    closeModal();
                }
            }
        });
    });
</script>
<script>
    document.getElementById('calculateBtn').addEventListener('click', function() {
        const gender = document.querySelector('input[name="gender"]:checked')?.value;
        const usia = document.getElementById('age').value;
        const bb = document.getElementById('weight').value;
        const tb = document.getElementById('height').value;
        const aktivitas = document.getElementById('activity').selectedOptions[0]?.text;

        if (!gender || !usia || !bb || !tb || !aktivitas) {
            alert('Please fill in all fields');
            return;
        }

        fetch('{{ route('calc.save') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    jenis_kelamin: gender === 'male' ? 'L' : 'P',
                    usia: parseInt(usia),
                    bb: parseFloat(bb),
                    tb: parseFloat(tb),
                    aktivitas: aktivitas,
                    image_path: '' // kalau mau tambahkan image path
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Data saved successfully');
                } else {
                    alert('Failed to save data');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Something went wrong');
            });
    });
</script>
