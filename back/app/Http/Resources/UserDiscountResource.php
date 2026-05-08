<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDiscountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isExpired = $this->expires_at !== null && $this->expires_at->isPast();
        $status = ($this->is_active && ! $isExpired) ? 'active' : 'expired';

        return [
            'id' => $this->id,
            'title' => $this->title,
            'code' => $this->code,
            'discount_type' => $this->discount_type,
            'discount_value' => (float) $this->discount_value,
            'expires_at' => $this->expires_at?->toIso8601String(),
            'status' => $status,
            'is_active' => (bool) $this->is_active,
        ];
    }
}
