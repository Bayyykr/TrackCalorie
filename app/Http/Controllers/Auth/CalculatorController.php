<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CalculatorController extends Controller
{
    public function showFormCalculator()
    {
        return view('auth.calculator');
    }

    public function calculate(Request $request)
    {
        // Contoh logika kalkulasi kalori
        $weight = $request->input('weight'); // berat badan
        $height = $request->input('height'); // tinggi badan
        $age = $request->input('age');       // usia
        $gender = $request->input('gender'); // jenis kelamin

        if ($gender === 'male') {
            $calories = 66 + (13.7 * $weight) + (5 * $height) - (6.8 * $age);
        } else {
            $calories = 655 + (9.6 * $weight) + (1.8 * $height) - (4.7 * $age);
        }

        return view('calculator.result', compact('calories'));
    }
}
