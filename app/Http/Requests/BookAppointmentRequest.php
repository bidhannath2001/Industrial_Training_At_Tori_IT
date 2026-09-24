<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BookAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    
    public function rules(): array
    {
        return [
            'doctor_id'=>'required|exists:doctors,id',
            'appointment_date'=>'required|date|after:today',
            'appointment_time'=>'required|date_format:H:i',
            'notes'=>'nullable|string|max:500'
        ];
    }
}
