<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddReviewRequest;
use App\Models\Appointment;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(AddReviewRequest $request){
        try{
            $appointment= Appointment::find($request->appointment_id);

            if (!$appointment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Appointment not found'
                ], 404);
            }
            if ($appointment->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
            // Check if review already exists for this appointment
            $exists = Rating::where('appointment_id', $request->appointment_id)->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already reviewed this appointment'
                ], 409);
            }
            $review = Rating::create([
                'user_id' => Auth::id(),
                'doctor_id' => $request->doctor_id,
                'appointment_id' => $request->appointment_id,
                'rating' => $request->rating,
                'review' => $request->review,
                'verified_appointment' => true
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Review added successfully',
                'data' => $review
            ], 201);

        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Failed to add review',
                'error'=>$e->getMessage()
            ], 500);
        }
    }

    //get reviews for a doctor
    public function doctorReviews($doctorId, Request $request)
    {
        try {
            $query = Rating::where('doctor_id', $doctorId)
                           ->with(['user']);

            $perPage = $request->query('per_page', 10);
            $reviews = $query->orderBy('created_at', 'desc')
                            ->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Reviews fetched',
                'data' => $reviews->items(),
                'pagination' => [
                    'total' => $reviews->total(),
                    'per_page' => $reviews->perPage(),
                    'current_page' => $reviews->currentPage(),
                    'last_page'=>$reviews->lastPage()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch reviews'
            ], 500);
        }
    }

    //update review
    public function update(Request $request, $id)
    {
        try {
            $review = Rating::find($id);

            if (!$review) {
                return response()->json([
                    'success' => false,
                    'message' => 'Review not found'
                ], 404);
            }

            // Check authorization
            if ($review->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $review->update([
                'rating' => $request->rating ?? $review->rating,
                'review' => $request->review ?? $review->review
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Review updated successfully',
                'data' => $review
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update review'
            ], 500);
        }
    }

    //delete review
     public function destroy($id)
    {
        try {
            $review = Rating::find($id);

            if (!$review) {
                return response()->json([
                    'success' => false,
                    'message' => 'Review not found'
                ], 404);
            }

            // Check authorization
            if ($review->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $review->delete();

            return response()->json([
                'success' => true,
                'message' => 'Review deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete review'
            ], 500);
        }
    }
}
