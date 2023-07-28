<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreUserInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
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
}
