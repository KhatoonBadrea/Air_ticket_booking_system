<?php

namespace App\Http\Requests\Auth;

use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     * 
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'     => 'required|string|between:2,100',
            'email'    => 'required|string|email|max:100|unique:users',
            'role'     => 'nullable|in:user,admin',
            'password' => ['required', 'max:16', Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised()], // Enforce complexity
        ];
    }



    public function attributes(): array
    {
        return [
            'name'     => 'Name',
            'email'    => 'Email Address',
            'password' => 'Password',

        ];
    }

    /**
     * Get custom error messages for validator failures.
     *
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'required'       => 'The :attribute field is required.',
            'string'         => 'The :attribute must be a valid string.',
            'password.max'   => 'The password must not exceed 16 characters.',
            'email.email'    => 'The email must be a valid email address.',
            'unique'         => 'This :attribute is already registered.',
            'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',

        ];
    }


    /**
     * Handles failed validation attempts.
     * Logs validation failures with relevant details for debugging and monitoring.
     * Excludes sensitive data (password) from logs.
     * Uses parent's failedValidation to maintain consistent error response format.
     *
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator): void
    {
        // Log validation failure with relevant details
        Log::error('Validation failed for RegisterRequest', [
            'errors' => $validator->errors()->toArray(),
            'input' => $this->except(['password', 'password_confirmation']),
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
        ]);

        throw new HttpResponseException(response()->json([
            'status'  => 'error',
            'message' => 'Validation failed.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
