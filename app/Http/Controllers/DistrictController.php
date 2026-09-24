<?php

namespace App\Http\Controllers;

use App\Models\District;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    
    //Get all districts
     
    public function index(Request $request)
    {
        try {
            $query = District::query();

            // Filter by state
            if ($request->has('state') && $request->state) {
                $query->where('state', $request->state);
            }

            // Search
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where('name', 'like', "%$search%");
            }

            $perPage = $request->query('per_page', 100);
            $districts = $query->orderBy('name')
                              ->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Districts fetched successfully',
                'data' => $districts->items(),
                'pagination' => [
                    'total' => $districts->total(),
                    'per_page' => $districts->perPage(),
                    'current_page' => $districts->currentPage()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch districts'
            ], 500);
        }
    }

    
    //Get single district
    
    public function show($id)
    {
        try {
            $district = District::find($id);

            if (!$district) {
                return response()->json([
                    'success' => false,
                    'message' => 'District not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'District fetched successfully',
                'data' => $district
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch district'
            ], 500);
        }
    }
}