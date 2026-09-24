<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookAppointmentRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function store(BookAppointmentRequest $request){
        try{
            $doctor = Doctor::find($request->doctor_id);
            if(!$doctor){
                return response()->json([
                    'success' => false,
                    'message' => 'Doctor not found',
                ],404);
            }
            $exists = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->where('status', 'booked')
            ->exists();
            if($exists){
                return response()->json([
                    'success' => false,
                    'message' => 'This slot is already booked',
                ],409);
            }
            //calculate total amount
            $amount = $doctor->fee;
            $bookingFee = $doctor->organization->booking_fee??0;
            $totalAmount = $amount + $bookingFee;

            $appointment = Appointment::create([
                'user_id'=>Auth::user()->id,
                'doctor_id'=>$request->doctor_id,
                'organization_id'=>$doctor->organization_id,
                'appointment_date'=>$request->appointment_date,
                'appointment_time'=>$request->appointment_time,
                'notes'=>$request->notes,
                'amount'=>$amount,
                'booking_fee'=>$bookingFee,
                'total_amount'=>$totalAmount,
                'status'=>'booked',
                'payment_status'=>'pending'
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Appointment booked successfully',
                'data'=>$appointment->load(['doctor','organization'])
            ],201);
        }catch(\Exception $e){
            return response()->json([
                'success'=>false,
                'message'=>'Failed to book appointment',
                'error'=>$e->getMessage()
            ],500);
        }
    }

    //get user's appointments
    public function userAppointments(Request $request){
        try{
            $query = Appointment::where('user_id', $request->user_id)->with(['doctor','organization']);

            //filter by status
            if($request->has('status') && $request->status){
                $query->where('status', $request->status);
            }
            $perpage = $request->query('per_page',10);
            $appointments = $query->orderBy('appointment_date','desc')->paginate($perpage);
            return response()->json([
                'success' => true,
                'message' => 'Appointments fetched successfully',
                'data'=>$appointments->items(),
                'pagination'=>[
                    'total'=>$appointments->total(),
                    'per_page'=>$appointments->perPage(),
                    'current_page'=>$appointments->currentPage(),
                    'last_page'=>$appointments->lastPage(),
                ]
            ]);

        }catch(\Exception $e){
            return response()->json([
                'success'=>false,
                'message'=>'Failed to get user appointments',
                'error'=>$e->getMessage()
            ],500);
        }
    }

    //get appointment details
    public function show($id){
        try{
            $appointment = Appointment::with(['doctor','organization','user'])->find($id);
            if(!$appointment){
                return response()->json([
                    'success' => false,
                    'message' => 'Appointment not found',
                ],404);
            }
            //check if user is authorized to view this appointment
            if($appointment->user_id != Auth::id()){
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to view this appointment',
                ],403);
            }
            return response()->json([
                'success' => true,
                'message' => 'Appointment fetched successfully',
                'data'=>$appointment
            ]);
        }catch(\Exception $e){
            return response()->json([
                'success'=>false,
                'message'=>'Failed to get appointment',
                'error'=>$e->getMessage()
            ],500);
        }
    }
    //cancel appointment
    public function cancel($id){
        try{
            $appointment = Appointment::find($id);
            if(!$appointment){
                return response()->json([
                    'success'=>false,
                    'message'=>'Appointment not found'
                ],404);
            }
            //check authorization
            if($appointment->user!==Auth::id()){
                return response()->json([
                    'success'=>false,
                    'message'=>'Unauthorized'
                ],403);
            }

            //check if appointment is in future
            if($appointment->appointment_date<now()->toDateString()){
                return response()->json([
                    'success'=>false,
                    'message'=> 'Cannot cancel past appointments'
                ],400);
            }
            return response()->json([
                'success'=>true,
                'message'=>'Appointment cancelled successfully',
                'data'=>$appointment
            ]);
        }catch(\Exception $e){
            return response()->json([
                'success'=>false,
                'message'=>'Failed to cancel appointment'
            ],500);
        }
    }
}
