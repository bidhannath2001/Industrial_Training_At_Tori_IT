<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserProfileController extends Controller
{
    /**
     * Get user profile
     */
    public function show()
    {
        try {
            $user = Auth::user();

            return response()->json([
                'success' => true,
                'message' => 'User profile fetched',
                'data' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch profile'
            ], 500);
        }
    }

    //Update user profile
    
    public function update(Request $request)
    {
        try {
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'phone' => 'sometimes|string|unique:users,phone,' . Auth::id(),
                'gender' => 'sometimes|in:Male,Female,Other',
                'age' => 'sometimes|integer|min:1|max:120',
                'state' => 'sometimes|string|max:255',
                'district_id' => 'sometimes|exists:districts,id',
                'village_name' => 'sometimes|string|max:255',
                'height' => 'sometimes|nullable|numeric|min:50|max:300',
                'weight' => 'sometimes|nullable|numeric|min:20|max:500',
                'avatar' => 'sometimes|nullable|url'
            ]);

            $user = User::findOrFail(Auth::id());
            $user->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //Change password
    
    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed'
            ]);

            $user = User::findOrFail(Auth::id());

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 400);
            }

            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to change password'
            ], 500);
        }
    }
}