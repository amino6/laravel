<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'tagline' => $this->tagline,
            'about' => $this->about,
            'location' => $this->location,
            'formatted_address' => $this->formatted_address,
            'available_to_hire' => $this->available_to_hire,
            'designs' => $this->designs
        ];
    }
}
