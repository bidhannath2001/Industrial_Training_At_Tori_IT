<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;

class SaloonController extends Controller
{
    
    public function index(Request $request)
    {
        try {
            $query = Organization::where('type', 'beautician')
                                 ->where('is_approved', true)
                                 ->with(['saloonServices']);

            // Filter by district
            if ($request->has('district_id') && $request->district_id) {
                $query->where('district_id', $request->district_id);
            }

            // Search
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where('name', 'like', "%$search%")
                      ->orWhere('location', 'like', "%$search%");
            }

            $perPage = $request->query('per_page', 10);
            $saloons = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Saloons fetched successfully',
                'data' => $saloons->items(),
                'pagination' => [
                    'total' => $saloons->total(),
                    'per_page' => $saloons->perPage(),
                    'current_page' => $saloons->currentPage(),
                    'last_page'=>$saloons->lastPage()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch saloons'
            ], 500);
        }
    }

    
    //Get saloon details
    
    public function show($id)
    {
        try {
            $saloon = Organization::with(['saloonServices', 'district'])
                                  ->where('type', 'beautician')
                                  ->where('is_approved', true)
                                  ->find($id);

            if (!$saloon) {
                return response()->json([
                    'success' => false,
                    'message' => 'Saloon not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Saloon fetched successfully',
                'data' => $saloon
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch saloon'
            ], 500);
        }
    }

    //Get saloons by district
    
    public function byDistrict($districtId, Request $request)
    {
        try {
            $query = Organization::where('type', 'beautician')
                                 ->where('is_approved', true)
                                 ->where('district_id', $districtId);

            $perPage = $request->query('per_page', 15);
            $saloons = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Saloons fetched by district',
                'data' => $saloons->items(),
                'pagination' => [
                    'total' => $saloons->total(),
                    'per_page' => $saloons->perPage(),
                    'current_page' => $saloons->currentPage()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch saloons'
            ], 500);
        }
    }
}