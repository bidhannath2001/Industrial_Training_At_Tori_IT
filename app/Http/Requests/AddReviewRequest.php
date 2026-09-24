<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class AddReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    
    public function rules(): array
    {
        return [
            'doctor_id'=>'required|exists:doctors,id',
            'appointment_id'=>'required|exists:appointments,id',
            'rating'=>'required|min:1|max:5',
            'review'=>'nullable|string|max:1000'
        ];
    }
    #[Override]
    public function messages()
    {
        return[
            'rating.min'=>'Rating must be at least 1',
            'rating.max'=>'Rating cannot exceed 5'
        ];
    }
}
