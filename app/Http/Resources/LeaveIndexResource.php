<?php

namespace App\Http\Resources;

use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Leave */
class LeaveIndexResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'start_date' => $this->start_date->format('d.m.Y'),
            'end_date' => $this->end_date->format('d.m.Y'),
            'days_count' => $this->days_count,
            'status' => $this->status,
            'reason' => $this->reason,
            'user' => $this->whenLoaded('user', function () {
                return $this->user->first_name . ' ' . $this->user->last_name;
            }),
        ];
    }
}
