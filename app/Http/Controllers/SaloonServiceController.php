<?php

namespace App\Http\Controllers;

use App\Models\SaloonService;
use App\Models\Organization;
use Illuminate\Http\Request;

class SaloonServiceController extends Controller
{
    //get services for a saloon
    
    public function bySaloon($saloonId, Request $request)
    {
        try {
            $saloon = Organization::where('type', 'beautician')
                                  ->find($saloonId);

            if (!$saloon) {
                return response()->json([
                    'success' => false,
                    'message' => 'Saloon not found'
                ], 404);
            }

            $services = SaloonService::where('organization_id', $saloonId)
                                    ->get();

            return response()->json([
                'success' => true,
                'message' => 'Services fetched successfully',
                'data' => [
                    'saloon' => $saloon,
                    'services' => $services
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch services'
            ], 500);
        }
    }

    //Get single service
     
    public function show($id)
    {
        try {
            $service = SaloonService::with(['organization'])->find($id);

            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Service fetched successfully',
                'data' => $service
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch service'
            ], 500);
        }
    }
}