<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    //Register a new user
    public function register(RegisterUserRequest $request){
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'gender' => $request->gender,
                'age' => $request->age,
                'district_id' => $request->district_id,
                'village_name' => $request->village_name,
                'state' => $request->state,
                'height' => $request->height,
                'weight' => $request->weight,
                'is_active' => true
            ]);
            
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data'=>[
                    'user'=>[
                        'id'=>$user->id,
                        'name'=>$user->name,
                        'email'=>$user->email,
                        'phone'=>$user->phone,
                        'gender'=>$user->gender,
                        'age'=>$user->age,
                    ],
                    'token'=>$token
                ]
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message'=>'Registration failed',
                'error'=>$e->getMessage()
            ],500);
        }
    }

    //Login user
    public function login(LoginRequest $request){
        try{
            $user = User::Where('email',$request->email)->first();

            if(!$user || !Hash::check($request->password,$user->password)){
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid email or password',
                ],401);
            }
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'User logged in successfully',
                'data'=>[
                    'user'=>[
                        'id'=>$user->id,
                        'name'=>$user->name,
                        'email'=>$user->email,
                        'phone'=>$user->phone
                    ],
                    'token'=>$token
                ]
            ],200);
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message'=>'Login failed',
                'error'=>$e->getMessage()
            ],500);
        }
    }

    //Get current user
    public function currentUser(Request $request){
        return response()->json([
            'success' => true,
            'data' => $request->user(),
        ]);
    }

    //logout
    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out',
        ]);
    }
}
