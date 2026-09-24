<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    
    //Get all subcategories
     
    public function index(Request $request)
    {
        try {
            $query = Subcategory::query();

            // Search
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where('name', 'like', "%$search%");
            }

            $perPage = $request->query('per_page', 50);
            $subcategories = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Subcategories fetched successfully',
                'data' => $subcategories->items(),
                'pagination' => [
                    'total' => $subcategories->total(),
                    'per_page' => $subcategories->perPage(),
                    'current_page' => $subcategories->currentPage()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subcategories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //Get single subcategory
    
    public function show($id)
    {
        try {
            $subcategory = Subcategory::find($id);

            if (!$subcategory) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subcategory not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Subcategory fetched successfully',
                'data' => $subcategory
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subcategory'
            ], 500);
        }
    }
}