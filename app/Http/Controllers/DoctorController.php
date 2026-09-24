<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Doctor::with(['user', 'organization', 'subcategory'])->where('status', 'approved');

            //Filter by district
            if ($request->has('district_id') && $request->district_id) {
                $query->whereHas('organization', function ($q) use ($request) {
                    $q->where('district_id', $request->district_id);
                });
            }

            //Filter by subcategory
            if ($request->has('subcategory_id') && $request->subcategory_id) {
                $query->where('subcategory_id', $request->subcategory_id);
            }

            //Search by name or phone
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('phone', 'like', '%' . $search . '%');
                });
            }
            //sorting
            $sortBy = $request->query('sort_by', 'created_at');
            $sortOrder = $request->query('sort_order', 'desc');
            $allowedColumn = ['fee', 'created_at', 'experience_years'];

            if (in_array($sortBy, $allowedColumn)) {
                $query->orderBy($sortBy, $sortOrder);
            }

            //pagination
            $perPage = $request->query('per_page', 10);
            $doctors = $query->paginate($perPage);
            return response()->json([
                'success' => true,
                'message' => 'Doctors fetched successfully',
                'data' => $doctors->items(),
                'pagination' => [
                    'total' => $doctors->total(),
                    'per_page' => $doctors->perPage(),
                    'current_page' => $doctors->currentPage(),
                    'last_page' => $doctors->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch doctors',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //Get doctor details by ID
    public function show($id)
    {
        try {
            $doctor = Doctor::with(['user', 'organization', 'subcategory'])->where('status', 'approved')->find($id);

            if (!$doctor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doctor not found',
                ], 404);
            }
            //get average rating
            $averageRating = $doctor->ratings()->avg('rating');
            $reviewCount = $doctor->ratings()->count();
            return response()->json([
                'success' => true,
                'message' => 'Doctor fetched successfully',
                'data' => [
                    'doctor' => $doctor,
                    'average_rating' => $averageRating,
                    'review_count' => $reviewCount
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch doctor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //get doctors by district

    public function byDistrict($districtId, Request $request)
    {
        try {
            $query = Doctor::with(['user', 'organization', 'subcategory'])->where('status', 'approved')
                ->whereHas('organization', function ($q) use ($districtId) {
                    $q->where('district_id', $districtId);
                });

            $perPage = $request->query('per_page', 10);
            $doctors = $query->paginate($perPage);
            return response()->json([
                'success' => true,
                'message' => 'Doctors fetched successfully',
                'data' => $doctors->items(),
                'pagination' => [
                    'total' => $doctors->total(),
                    'per_page' => $doctors->perPage(),
                    'current_page' => $doctors->currentPage(),
                    'last_page' => $doctors->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch doctors',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //get doctors by subcategory

    public function bySubcategory($subcategoryId, Request $request)
    {
        try {
            $query = Doctor::with(['user', 'organization', 'subcategory'])->where('status', 'approved')
                ->where('subcategory_id', $subcategoryId);
            $perPage = $request->query('per_page', 10);
            $doctors = $query->paginate($perPage);
            return response()->json([
                'success' => true,
                'message' => 'Doctors fetched successfully',
                'data' => $doctors->items(),
                'pagination' => [
                    'total' => $doctors->total(),
                    'per_page' => $doctors->perPage(),
                    'current_page' => $doctors->currentPage(),
                    'last_page' => $doctors->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch doctors',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //get available appointment slots for a doctor
    public function getAvailableSlots($doctorId, Request $request)
    {
        try {
            $doctor = Doctor::find($doctorId);
            $date = $request->query('date');
            // $date = $request->input('date');

            if (!$doctor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doctor not found',
                ], 404);
            }

            if (!$date) {
                return response()->json([
                    'success' => false,
                    'message' => 'Date is required',
                ], 400);
            }

            $dayName = date('D', strtotime($date));
            $availableDays = explode(',', str_replace(' ', '', $doctor->available_days));

            if (!in_array($dayName, $availableDays)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doctor is not available on this day',
                    'data' => []
                ], 400);
            }

            //get booked slots for this date
            $bookedSlots = Appointment::where('doctor_id', $doctorId)
                ->where('appointment_date', $date)
                ->where('status', 'booked')
                ->pluck('appointment_time')
                ->map(function ($time) {
                    return date('H:i', strtotime($time));
                })
                ->toArray();

            // dd([
            //     'starting_time' => $doctor->starting_time,
            //     'ending_time' => $doctor->ending_time,
            //     'break_start' => $doctor->break_time_start,
            //     'break_end' => $doctor->break_time_end,
            //     'booked_slots' => $bookedSlots,
            // ]);

            $slots = [];
            $startTime = strtotime($doctor->starting_time);
            $endTime = strtotime($doctor->ending_time);
            $breakStart = $doctor->break_time_start ? strtotime($doctor->break_time_start) : null;
            $breakEnd = $doctor->break_time_end ? strtotime($doctor->break_time_end) : null;

            for ($time = $startTime; $time < $endTime; $time += 30 * 60) {
                $timeStr = date('H:i', $time);
                //skip break time
                if (
                    $breakStart !== null &&
                    $breakEnd !== null &&
                    $time >= $breakStart &&
                    $time < $breakEnd
                ) {
                    continue;
                }
                //skip already booked slots
                if (!in_array($timeStr, $bookedSlots)) {
                    $slots[] = $timeStr;
                }
            }
            return response()->json([
                'success' => true,
                'message' => 'Available slots fetched successfully',
                'data' => [
                    'doctor_id' => $doctorId,
                    'date' => $date,
                    'available_slots' => $slots
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch available slots',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
