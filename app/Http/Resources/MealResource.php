<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MealResource extends JsonResource
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
            "title" => $this->title,
            "description" => $this->description,
            "status" => $this->status($request->query("diff_time")),
            "category" => $this->whenLoaded("category"),
            "tags" => $this->whenLoaded("tags"),
            "ingredients" => $this->whenLoaded("ingredients"),
        ];
    }
}
