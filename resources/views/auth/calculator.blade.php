@include('components.head')

@include('components.navbar')
<div class="main-container">
    <!-- Left Column - Food Image -->
    <div class="image-column">
        <img class="food-image-bottom" src="{{ asset('images/image-bg-calc.png') }}" alt="bg-kubis" width="700"
            height="700">
        <img class="food-image-top" src="{{ asset('images/image-left-calc.png') }}" alt="piring-buah" width="400"
            height="400">
    </div>

    <!-- Right Column - Calculator Form -->
    <div class="form-column">
        <div class="calculator-title">
            <h1>Calculate Your BMR & TDEE</h1>
        </div>

        <form>
            <!-- Gender Selection -->
            <div class="form-group">
                <label class="form-label">Gender</label>
                <div class="gender-options">
                    <div class="gender-option">
                        <input type="radio" id="male" name="gender" value="male">
                        <label for="male">
                            <img src="{{ asset('images/male.png') }}" alt="male" width="30px" height="30px">
                            <p>Male</p>
                        </label>
                    </div>
                    <div class="gender-option">
                        <input type="radio" id="female" name="gender" value="female">
                        <label for="female">
                            <img src="{{ asset('images/female.png') }}" alt="female" width="30px" height="30px">
                            <p>Female</p>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Age Input -->
            <div class="form-group">
                <label for="age" class="form-label">Age</label>
                <input type="number" id="age" class="form-control" placeholder="Enter your age" min="1" max="120">
                <div class="form-text">years old</div>
            </div>

            <!-- Weight Input -->
            <div class="form-group">
                <label for="weight" class="form-label">Weight</label>
                <input type="number" id="weight" class="form-control" placeholder="Enter your weight" min="1"
                    step="0.1">
                <div class="form-text">Kg</div>
            </div>

            <!-- Height Input -->
            <div class="form-group">
                <label for="height" class="form-label">Height</label>
                <input type="number" id="height" class="form-control" placeholder="Enter your height" min="1"
                    step="0.1">
                <div class="form-text">Cm</div>
            </div>

            <!-- Activity Level -->
            <div class="form-group">
                <label for="activity" class="form-label">Activity Level</label>
                <select id="activity" class="form-control">
                    <option value="" disabled selected>Select Activity Level</option>
                    <option value="1.2">Sedentary (little or no exercise)</option>
                    <option value="1.375">Lightly active (light exercise 1-3 days/week)</option>
                    <option value="1.55">Moderately active (moderate exercise 3-5 days/week)</option>
                    <option value="1.725">Very active (hard exercise 6-7 days/week)</option>
                    <option value="1.9">Extra active (very hard exercise & physical job)</option>
                </select>
            </div>

            <button type="button" class="btn-calculate" id="calculateBtn">Calculate</button>
        </form>
        <button type="button" class="btn-monitor" id="monitorBtn">Monitor Calories</button>
    </div>
</div>