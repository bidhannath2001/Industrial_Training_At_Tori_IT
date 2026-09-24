<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'=>'required|string|max:255',
            'email'=>'required|email|unique:users,email',
            'phone'=>'required|string|unique:users,phone|regex:/^[0-9]{10,}$/',
            'password'=>'required|string|min:8|confirmed',
            'gender'=>'required|in:Male,Female,Other',
            'age'=>'required|integer|min:1|max:120',
            'district_id'=>'required|exists:districts,id',
            'village_name'=>'required|string|max:255',
            'state'=>'required|string|max:255',
            'height'=>'nullable|numeric|min:50|max:300',
            'weight'=>'nullable|numeric|min:20|max:500'
        ];
    }

    public function messages():array{
        return[
            'phone.regex'=>'Phone must be at least 10 digits',
            'age.min'=>'Age must be greater than 0',
            'district_id.exists'=>'Invalid district selected'
        ];
    }
}
