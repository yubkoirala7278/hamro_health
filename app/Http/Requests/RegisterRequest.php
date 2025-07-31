<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    // Add failedValidation for RestApi
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'parent_full_name' => 'required|string|max:255',
            'parent_email' => 'required|email|unique:users,email|max:255',
            'parent_phone' => 'required|string|regex:/^9[7-8][0-5][0-9]{7}$/|unique:users,phone',
            'parent_home_address' => 'nullable|string|max:255',
            'child_full_name' => 'required|string|max:255',
            'child_dob' => 'required|date|before:today',
            'child_school_id' => 'required|exists:schools,id',
            'child_emergency_contact_number' => 'required|string|regex:/^9[7-8][0-5][0-9]{7}$/',
        ];
    }

    public function messages(): array
    {
        return [
            'parent_email.unique' => 'The email address is already registered.',
            'parent_phone.unique' => 'The phone number is already registered.',
            'parent_phone.regex' => 'The phone number must be a valid 10-digit Nepali number starting with 97x or 98x.',
            'child_emergency_contact_number.regex' => 'The emergency contact number must be a valid 10-digit Nepali number starting with 97x or 98x.',
            'child_dob.before' => 'The date of birth must be a valid date in the past.',
            'child_school_id.exists' => 'The selected school does not exist.',
        ];
    }
}
