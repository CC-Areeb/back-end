<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserInfo extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'username' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|confirmed',
            'street' => 'nullable',
            'suite' => 'nullable',
            'city' => 'nullable',
            'zip_code' => 'nullable',
            'latitude' => 'nullable',
            'longitude' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please provide us your name',
            'username.required' => 'Please enter a username',
            'email.required' => 'Please enter a valid and unique email',
            'password.required' => 'Please enter a password',
            'password.confirmed' => 'Passwords do not match',
        ];
    }
}
