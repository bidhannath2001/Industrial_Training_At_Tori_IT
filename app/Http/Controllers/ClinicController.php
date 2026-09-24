<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;

class ClinicController extends Controller
{
    //get all
    public function index(Request $request){
        try{
            $query = Organization::where('type','doctor')->where('is_approved',true);

            //filter by district
            if($request->has('district_id')&& $request->district_id){
                $query->where('district_id',$request->district_id);
            }

            //search
            if($request->has('search')&&$request->search){
                $search = $request->search;
                $query->where('name','like',"%$search%")
                ->orWhere('location','like','%'.$search.'%');
            }
            $perPage = $request->query('per_page',10);
            $clinics = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'message' => 'Clinics fetched successfully',
                'data'=>$clinics->items(),
                'pagination'=>[
                    'total'=>$clinics->total(),
                    'per_page'=>$clinics->perPage(),
                    'current_page'=>$clinics->currentPage(),
                    'last_page'=>$clinics->lastPage(),
                ]
            ]);

        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch clinics',
                'error'=>$e->getMessage()
            ],500);
        }
    }

    //get clinic details
    public function show($id){
        try{
            $clinic = Organization::with('doctors','districts')
            ->where('type','doctor')
            ->where('is_approaved',true)
            ->find($id);

            if(!$clinic){
                return response()->json([
                    'success' => false,
                    'message' => 'Clinic not found',
                ],404);    
            }
            return response()->json([
                'success' => true,
                'message' => 'Clinic fetched successfully',
                'data'=>$clinic
            ],200);
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch clinic',
                'error'=>$e->getMessage()
            ],500);
        }
    }

    //get clinics bu district
    public function byDistrict($districtId, Request $request){
        try{
            $query = Organization::where('type','doctor')
            ->where('is_approved',true)
            ->where('district_id',$districtId);
            $perPage = $request->query('per_page',10);
            $clinics = $query->paginate($perPage);
            return response()->json([
                'success' => true,
                'message' => 'Clinics fetched successfully',
                'data'=>$clinics->items(),
                'pagination'=>[
                    'total'=>$clinics->total(),
                    'per_page'=>$clinics->perPage(),
                    'current_page'=>$clinics->currentPage(),
                    'last_page'=>$clinics->lastPage(),
                ]
            ]);
        }catch(\Exception $e){
            return response()->json([    
                'success' => false,
                'message' => 'Failed to fetch clinics',
                'error'=>$e->getMessage()
            ],500);
        }
    }   
}
