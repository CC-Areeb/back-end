<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserInfo extends FormRequest
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
            'password' => 'required',
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
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->has('name')) {
                $this->merge(['name' => $this->user->name]);
            }

            if (!$this->has('username')) {
                $this->merge(['username' => $this->user->username]);
            }

            if (!$this->has('email')) {
                $this->merge(['email' => $this->user->email]);
            }

            if (!$this->has('password')) {
                $this->merge(['password' => $this->user->password]);
            }
        });
    }
}
