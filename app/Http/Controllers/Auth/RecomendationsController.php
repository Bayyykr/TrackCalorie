<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Recomendations;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class RecomendationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showRecommendations(Request $request)
    {
        // Jika request AJAX untuk mengambil menu user
        if ($request->wantsJson() && !$request->has('filter')) {
            try {
                Log::info('AJAX request received for user menus', [
                    'user_id' => Auth::id(),
                    'headers' => $request->headers->all()
                ]);

                $menus = Auth::user()->menus()
                    ->select('id', 'name', 'calories')
                    ->latest()
                    ->get();

                Log::info('Menus found:', ['count' => $menus->count(), 'data' => $menus->toArray()]);

                $formattedMenus = $menus->map(function ($menu) {
                    $calories = is_numeric($menu->calories) ? $menu->calories : preg_replace('/[^0-9]/', '', $menu->calories);
                    return [
                        'id' => $menu->id,
                        'name' => $menu->name,
                        'calories' => $calories ?: 0
                    ];
                });

                return response()->json($formattedMenus);
            } catch (Exception $e) {
                Log::error('Failed to load menus: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json(['error' => 'Failed to load menus'], 500);
            }
        }

        // Jika request AJAX untuk filter recommendations
        if ($request->wantsJson() && $request->has('filter')) {
            try {
                $category = $request->get('filter');
                $search = $request->get('search');

                $query = Recomendations::query();

                // Filter by category
                if ($category && $category !== 'all') {
                    $query->where('category', $category);
                }

                // Filter by search
                if ($search) {
                    $query->where('name', 'LIKE', '%' . $search . '%');
                }

                $recommendations = $query->get();
                $pageRows = $recommendations->chunk(5);

                return response()->json([
                    'success' => true,
                    'data' => $pageRows,
                    'total' => $recommendations->count()
                ]);
            } catch (Exception $e) {
                Log::error('Failed to filter recommendations: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to filter recommendations'], 500);
            }
        }

        // Tampilan normal untuk halaman rekomendasi
        $recommendations = Recomendations::all();
        $pageRows = $recommendations->chunk(5);

        // Get unique categories for dropdown
        $categories = Recomendations::whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();

        return view('auth.recomend', compact('pageRows', 'categories'));
    }

    public function destroyMenu(Menu $menu)
    {
        try {
            if ($menu->user_id !== Auth::id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $menu->delete();
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            Log::error('Failed to delete menu: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete menu'], 500);
        }
    }
}
