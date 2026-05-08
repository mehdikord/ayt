<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'image_url' => $this->image_url,
            'is_active' => (bool) $this->is_active,
            'sort_order' => $this->sort_order,
            'items' => MenuItemResource::collection(
                $this->whenLoaded('items')
            ),
        ];
    }
}
