<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Organization;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    //Unified search for doctors and saloons
    
    public function search(Request $request)
    {
        try {
            $query = $request->query('q');

            if (!$query || strlen($query) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search query must be at least 2 characters'
                ], 400);
            }

            // Search doctors
            $doctors = Doctor::with(['user', 'organization', 'subcategory'])
                            ->where('status', 'approved')
                            ->where(function ($q) use ($query) {
                                $q->whereHas('user', function ($subQ) use ($query) {
                                    $subQ->where('name', 'like', "%$query%")
                                         ->orWhere('phone', 'like', "%$query%");
                                })->orWhereHas('subcategory', function ($subQ) use ($query) {
                                    $subQ->where('name', 'like', "%$query%");
                                });
                            })
                            ->limit(10)
                            ->get();

            // Search clinics
            $clinics = Organization::where('type', 'doctor')
                                   ->where('is_approved', true)
                                   ->where(function ($q) use ($query) {
                                       $q->where('name', 'like', "%$query%")
                                         ->orWhere('location', 'like', "%$query%");
                                   })
                                   ->limit(10)
                                   ->get();

            // Search saloons
            $saloons = Organization::where('type', 'beautician')
                                   ->where('is_approved', true)
                                   ->where(function ($q) use ($query) {
                                       $q->where('name', 'like', "%$query%")
                                         ->orWhere('location', 'like', "%$query%");
                                   })
                                   ->limit(10)
                                   ->get();

            return response()->json([
                'success' => true,
                'message' => 'Search results',
                'data' => [
                    'doctors' => $doctors,
                    'clinics' => $clinics,
                    'saloons' => $saloons,
                    'total_results' => count($doctors) + count($clinics) + count($saloons)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed'
            ], 500);
        }
    }
}