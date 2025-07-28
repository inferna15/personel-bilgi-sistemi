<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class UserShowResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'identity_number' => $this->identity_number,
            'birth_date' => $this->birth_date->format('d.m.Y'),
            'gender' => $this->gender,
            'phone' => $this->phone,
            'address' => $this->address,
            'position' => $this->position,
            'photo' => $this->photo,
            'unit' => $this->unit?->name,
            'role' => $this->getRoleNames()->first(),
        ];
    }
}
