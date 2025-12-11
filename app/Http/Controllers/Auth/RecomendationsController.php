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

                $query = \App\Models\MasterMenu::query();

                // Filter by search (using nama_menu)
                if ($search) {
                    $query->where('nama_menu', 'LIKE', '%' . $search . '%');
                }

                $items = $query->get();

                // Helper transformation for consistency
                $transformMenu = function($item) {
                     $name = strtolower($item->nama_menu);
                     // Category logic (simplified for readability, keeping same logic)
                     $category = 'Lainnya';
                     
                     if (preg_match('/(nasi|bubur|lontong|kentang|ubi|singkong|roti)/i', $name)) {
                         $category = 'Makanan Pokok';
                     } elseif (preg_match('/(ayam|daging|sapi|ikan|lele|telur|udang|cumi|sate|rendang|bebek)/i', $name)) {
                         $category = 'Lauk Pauk';
                     } elseif (preg_match('/(sayur|tumis|bayam|kangkung|brokoli|tomat|kubis|timun|wortel|buncis|kacang|tauge)/i', $name)) {
                         $category = 'Sayuran';
                     } elseif (preg_match('/(mie|bihun|kwetiau|pasta|spaghetti)/i', $name)) {
                         $category = 'Mie & Pasta';
                     } elseif (preg_match('/(tahu|tempe|jamur|perkedel|bakwan)/i', $name)) {
                         $category = 'Lauk Nabati';
                     } elseif (preg_match('/(soto|sop|bakso|gulai|rawon|kari)/i', $name)) {
                         $category = 'Makanan Berkuah';
                     } elseif (preg_match('/(apel|jeruk|pisang|mangga|anggur|semangka|melon|pepaya|salak|duku|alpukat)/i', $name)) {
                         $category = 'Buah-buahan';
                     }

                     $snakeName = str_replace(' ', '_', strtolower($item->nama_menu));
                     $imagePath = null;
                     
                     if (file_exists(public_path('images/makanan/' . $snakeName . '.png'))) {
                         $imagePath = 'images/makanan/' . $snakeName . '.png';
                     } elseif (file_exists(public_path('images/makanan/' . $snakeName . '.jpg'))) {
                         $imagePath = 'images/makanan/' . $snakeName . '.jpg';
                     } 
                     
                     return (object) [
                        'id' => $item->id_menu, // Uses ID from MasterMenu can be used as reference if needed, but we save name/cal
                        'name' => $item->nama_menu,
                        'description' => 'Kalori per ' . $item->jumlah . 'g/ml' . ($item->keterangan ? '. ' . $item->keterangan : ''),
                        'calorie_range' => $item->jumlah_kalori . ' Kcal',
                        'raw_calories' => $item->jumlah_kalori, 
                        'image_path' => $imagePath, 
                        'image_color' => '#' . substr(md5($item->nama_menu), 0, 6),
                        'category' => $category
                     ];
                };

                // Transform all items first
                $allRecommendations = $items->map($transformMenu);

                // Filter by category if requested
                if ($category && $category !== 'all') {
                    $allRecommendations = $allRecommendations->where('category', $category);
                }

                // Sort: Items with images first
                $allRecommendations = $allRecommendations->sortByDesc(function($item) {
                    return $item->image_path ? 1 : 0;
                })->values();

                // Flatten the pages for grid layout - standard 4-col grid doesn't strictly need chunking 
                // but if the view expects chunks, we keep chunks.
                $pageRows = $allRecommendations->chunk(4);

                return response()->json([
                    'success' => true,
                    'data' => $pageRows,
                    'total' => $allRecommendations->count()
                ]);
            } catch (Exception $e) {
                Log::error('Failed to filter recommendations: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to filter recommendations: ' . $e->getMessage()], 500);
            }
        }

        // Tampilan normal untuk halaman rekomendasi
        $items = \App\Models\MasterMenu::all();
        
        // Helper helper for transformation (duplicated for initial load, ideally refactor to method)
        $transformMenu = function($item) {
             $name = strtolower($item->nama_menu);
             $category = 'Lainnya';
             
             if (str_contains($name, 'nasi') || str_contains($name, 'bubur') || str_contains($name, 'lontong') || str_contains($name, 'kentang') || str_contains($name, 'ubi') || str_contains($name, 'singkong') || str_contains($name, 'roti')) {
                 $category = 'Makanan Pokok';
             } elseif (str_contains($name, 'ayam') || str_contains($name, 'daging') || str_contains($name, 'sapi') || str_contains($name, 'ikan') || str_contains($name, 'lele') || str_contains($name, 'telur') || str_contains($name, 'udang') || str_contains($name, 'cumi') || str_contains($name, 'sate') || str_contains($name, 'rendang') || str_contains($name, 'bebek')) {
                 $category = 'Lauk Pauk';
             } elseif (str_contains($name, 'sayur') || str_contains($name, 'tumis') || str_contains($name, 'bayam') || str_contains($name, 'kangkung') || str_contains($name, 'brokoli') || str_contains($name, 'tomat') || str_contains($name, 'kubis') || str_contains($name, 'timun') || str_contains($name, 'wortel') || str_contains($name, 'buncis') || str_contains($name, 'kacang') || str_contains($name, 'tauge')) {
                 $category = 'Sayuran';
             } elseif (str_contains($name, 'mie') || str_contains($name, 'bihun') || str_contains($name, 'kwetiau') || str_contains($name, 'pasta') || str_contains($name, 'spaghetti')) {
                 $category = 'Mie & Pasta';
             } elseif (str_contains($name, 'tahu') || str_contains($name, 'tempe') || str_contains($name, 'jamur') || str_contains($name, 'perkedel') || str_contains($name, 'bakwan')) {
                 $category = 'Lauk Nabati';
             } elseif (str_contains($name, 'soto') || str_contains($name, 'sop') || str_contains($name, 'bakso') || str_contains($name, 'gulai') || str_contains($name, 'rawon') || str_contains($name, 'kari')) {
                 $category = 'Makanan Berkuah';
             } elseif (str_contains($name, 'apel') || str_contains($name, 'jeruk') || str_contains($name, 'pisang') || str_contains($name, 'mangga') || str_contains($name, 'anggur') || str_contains($name, 'semangka') || str_contains($name, 'melon') || str_contains($name, 'pepaya') || str_contains($name, 'salak') || str_contains($name, 'duku') || str_contains($name, 'alpukat')) {
                 $category = 'Buah-buahan';
             } else {
                $category = 'Lainnya';
             }

             $snakeName = str_replace(' ', '_', $name);
             $imagePath = null;
             
             if (file_exists(public_path('images/makanan/' . $snakeName . '.png'))) {
                 $imagePath = 'images/makanan/' . $snakeName . '.png';
             } elseif (file_exists(public_path('images/makanan/' . $snakeName . '.jpg'))) {
                 $imagePath = 'images/makanan/' . $snakeName . '.jpg';
             }

             return (object) [
                'id' => $item->id_menu,
                'name' => $item->nama_menu,
                'description' => 'Kalori per ' . $item->jumlah . 'g/ml',
                'calorie_range' => $item->jumlah_kalori . ' Kcal',
                'image_path' => $imagePath,
                'image_color' => '#' . substr(md5($item->nama_menu), 0, 6),
                'category' => $category
             ];
        };
        
        $recommendations = $items->map($transformMenu);
        
        // Sort: Items with images first
        $recommendations = $recommendations->sortByDesc(function($item) {
            return $item->image_path ? 1 : 0;
        })->values();

        $pageRows = $recommendations->chunk(4);

        // Get unique categories for dropdown
        $categories = $recommendations->pluck('category')->unique()->sort()->values();

        return view('auth.recomend', compact('pageRows', 'categories'));
    }

    public function destroyMenu(Menu $menu)
    {
        try {
            if ($menu->users_id !== Auth::id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $menu->delete();
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            Log::error('Failed to delete menu: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete menu'], 500);
        }
    }

    public function storeMenu(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'calories' => 'required', // Accept string '400' or '400 Kcal'
            ]);

            // Clean calories to get just number if needed, but DB is string so safe to store as is
            // However, it's better to store just the number if we want to calculate later.
            // But let's check what existing data looks like. 
            // In showRecommendations: $menus->select('id', 'name', 'calories') -> formattedMenus uses calories.
            // User menu list shows "$menu->calories Kcal" in JS.
            // So if we store "400", JS shows "400 Kcal".
            // If we store "400 Kcal", JS shows "400 Kcal Kcal".
            // Let's strip non-numeric just in case.
            $calories = preg_replace('/[^0-9]/', '', $validated['calories']);
            
            $menu = Auth::user()->menus()->create([
                'name' => $validated['name'],
                'calories' => $calories
            ]);

            return response()->json(['success' => true, 'data' => $menu]);

        } catch (Exception $e) {
            Log::error('Failed to store menu: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to store menu: ' . $e->getMessage()], 500);
        }
    }
}
