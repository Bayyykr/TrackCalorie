<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controller;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        // Calculate profile completion percentage
        $requiredFields = ['name', 'email', 'usia', 'jenis_kelamin', 'tb', 'bb', 'aktivitas'];
        $completedCount = 0;

        foreach ($requiredFields as $field) {
            if (!empty($user->$field)) {
                $completedCount++;
            }
        }

        $completionPercentage = round(($completedCount / count($requiredFields)) * 100);

        return view('auth.profile', [
            'user' => $user,
            'loading' => false,
            'completionPercentage' => $completionPercentage
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'usia' => 'required|integer|min:1',
            'jenis_kelamin' => 'required|in:L,P',
            'tb' => 'required|numeric|min:50|max:250',
            'bb' => 'required|numeric|min:20|max:300',
            'aktivitas' => 'required|in:Sedentary,Lightly Active,Moderately Active,Very Active,Extra Active'
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully!');
    }

    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $user = Auth::user();

        if ($user->image_path && Storage::disk('public')->exists($user->image_path)) {
            Storage::disk('public')->delete($user->image_path);
        }


        $path = $request->file('photo')->store('profile-photos', 'public');


        logger()->info('Storing image at path:', ['path' => $path]);


        $user->update([
            'image_path' => $path,
            'avatar' => $path
        ]);

        return back()->with('success', 'Foto profil berhasil diupdate!');
    }
}
