<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HomePageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showHomepage(Request $request)
    {
        $user = Auth::user();

        $hour = Carbon::now()->hour;
        $currentDateTime = Carbon::now()->format('j M Y, H:i');
        $currentDay = Carbon::now()->format('l');
        $user = Auth::user();

        // Pastikan data user lengkap
        if (!$user || is_null($user->bb) || is_null($user->tb) || is_null($user->usia) || is_null($user->jenis_kelamin) || is_null($user->aktivitas)) {
            return null;
        }

        $tinggi_meter = $user->tb / 100; // konversi cm ke meter
        $bmi = $user->bb / ($tinggi_meter * $tinggi_meter);

        // Hitung BMR (sesuai rumus di gambar)
        if ($user->jenis_kelamin == 'L') { // Laki-laki (atau 'male')
            // BMR = 88.362 + (13.397 × bb(kg)) + (4.799 × tb(cm)) - (5.677 × usia(tahun))
            $bmr = 88.362 + (13.397 * $user->bb) + (4.799 * $user->tb) - (5.677 * $user->usia);
        } else { // Perempuan (atau 'female')
            // BMR = 447.593 + (9.247 × bb(kg)) + (3.098 × tb(cm)) - (4.330 × usia)
            $bmr = 447.593 + (9.247 * $user->bb) + (3.098 * $user->tb) - (4.330 * $user->usia);
        }

        // Faktor aktivitas - gunakan nilai langsung seperti di form HTML
        $activityFactors = [
            '1.2' => 1.2,        // Sedentary (little or no exercise)
            '1.375' => 1.375,    // Lightly active (light exercise 1-3 days/week)
            '1.55' => 1.55,      // Moderately active (moderate exercise 3-5 days/week)
            '1.725' => 1.725,    // Very active (hard exercise 6-7 days/week)
            '1.9' => 1.9         // Extra active (very hard exercise & physical job)
        ];

        // Ambil faktor aktivitas (default: 1.2 jika tidak valid)
        $factor = $activityFactors[$user->aktivitas] ?? 1.2;

        // Hitung TDEE = BMR × Tingkat Aktivitas
        $tdee = $bmr * $factor;

        // Bulatkan hasil untuk display yang lebih rapi
        $bmi = round($bmi, 1);      // BMI dengan 1 angka desimal
        $bmr = round($bmr);         // BMR dibulatkan ke bilangan bulat
        $tdee = round($tdee);       // TDEE dibulatkan ke bilangan bulat


        $calorieNeeds = $tdee;
        $today = date('Y-m-d');

        $calorieConsumed = DB::table('daily_calories')
            ->where('date', $today)
            ->sum('calories');


        $caloriePercentage = $calorieNeeds ? round(($calorieConsumed / $calorieNeeds) * 100) : 0;

        $lessCalorie = $calorieNeeds - $calorieConsumed;

        if ($hour >= 5 && $hour < 12) {
            $greeting = 'GOOD MORNING';
        } elseif ($hour >= 12 && $hour < 17) {
            $greeting = 'GOOD AFTERNOON';
        } elseif ($hour >= 17 && $hour < 21) {
            $greeting = 'GOOD EVENING';
        } else {
            $greeting = 'GOOD NIGHT';
        }

        $startDate = Carbon::now()->startOfWeek(); // atau ->subDays(6) kalau mau 6 hari ke belakang dari hari ini
        $endDate = Carbon::now()->endOfWeek();

        // Ambil total kalori per hari
        $caloriesPerDay = DB::table('daily_calories')
            ->select(DB::raw('DATE(date) as date'), DB::raw('SUM(calories) as total'))
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Siapkan data array untuk 7 hari
        $weekDays = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
        $chartData = [];

        foreach ($weekDays as $index => $day) {
            $date = $startDate->copy()->addDays($index)->format('Y-m-d');
            $dayData = $caloriesPerDay->firstWhere('date', $date);
            $chartData[] = $dayData ? (int)$dayData->total : 0;
        }

        $targetPerDay = $tdee;

        // Buat array target untuk 7 hari (untuk chart)
        $targetData = array_fill(0, 7, $targetPerDay);

        return view('auth.homepage', compact(
            'greeting',
            'currentDateTime',
            'currentDay',
            'user',
            'calorieNeeds',
            'caloriePercentage',
            'lessCalorie',
            'calorieConsumed',
            'bmi',
            'bmr',
            'tdee',
            'chartData',
            'targetData'
        ));
    }


    private function calculateCalorieNeeds($user) {}
}