<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowSingleDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "username" => $this->username,
            "email" => $this->email,
            "password" => $this->password,
            "otp" => $this->otp,
            "super_admin" => $this->super_admin,
            "admin" => $this->admin,
            "user" => $this->user,
            "street" => $this->street,
            "suite" => $this->suite,
            "city" => $this->city,
            "zip_code" => $this->zip_code,
            "latitude" => $this->latitude,
            "longitude" => $this->longitude,
        ];
    }
}
