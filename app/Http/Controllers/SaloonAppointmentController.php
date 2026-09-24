<?php

namespace App\Http\Controllers;

use App\Models\SaloonAppointment;
use App\Models\SaloonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaloonAppointmentController extends Controller
{
    /**
     * Book saloon appointment
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'service_id' => 'required|exists:saloon_services,id',
                'organization_id' => 'required|exists:organizations,id',
                'appointment_date' => 'required|date|after:today',
                'appointment_time' => 'required|date_format:H:i'
            ]);

            $service = SaloonService::find($request->service_id);

            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service not found'
                ], 404);
            }

            // Check if slot already booked
            $exists = SaloonAppointment::where('service_id', $request->service_id)
                                      ->where('appointment_date', $request->appointment_date)
                                      ->where('appointment_time', $request->appointment_time)
                                      ->where('status', 'booked')
                                      ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'This time slot is already booked'
                ], 409);
            }

            $appointment = SaloonAppointment::create([
                'user_id' => Auth::id(),
                'organization_id' => $request->organization_id,
                'service_id' => $request->service_id,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'amount' => $service->price,
                'status' => 'booked',
                'payment_status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Saloon appointment booked successfully',
                'data' => $appointment->load(['service', 'organization'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to book appointment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's saloon appointments
     */
    public function userAppointments(Request $request)
    {
        try {
            $query = SaloonAppointment::where('user_id', Auth::id())
                                     ->with(['service', 'organization']);

            // Filter by status
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            $perPage = $request->query('per_page', 15);
            $appointments = $query->orderBy('appointment_date', 'desc')
                                 ->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Appointments fetched',
                'data' => $appointments->items(),
                'pagination' => [
                    'total' => $appointments->total(),
                    'per_page' => $appointments->perPage(),
                    'current_page' => $appointments->currentPage()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch appointments'
            ], 500);
        }
    }

    /**
     * Get appointment details
     */
    public function show($id)
    {
        try {
            $appointment = SaloonAppointment::with(['service', 'organization', 'user'])
                                           ->find($id);

            if (!$appointment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Appointment not found'
                ], 404);
            }

            // Check authorization
            if ($appointment->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Appointment fetched',
                'data' => $appointment
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch appointment'
            ], 500);
        }
    }

    /**
     * Cancel saloon appointment
     */
    public function cancel($id)
    {
        try {
            $appointment = SaloonAppointment::find($id);

            if (!$appointment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Appointment not found'
                ], 404);
            }

            // Check authorization
            if ($appointment->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            // Check if appointment is in future
            if ($appointment->appointment_date < now()->toDateString()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot cancel past appointments'
                ], 400);
            }

            $appointment->update([
                'status' => 'cancelled',
                'cancelled_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Appointment cancelled successfully',
                'data' => $appointment
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel appointment'
            ], 500);
        }
    }
}