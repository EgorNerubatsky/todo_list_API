<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubtaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=>$this->id,
            "title"=>$this->title,
            "description"=>$this->description,
            "status" => $this->status,
            "priority" => $this->priority,
            "parent_id"=>$this->parent_id,
            "completed_at"=>$this->completed_at,
            'subtasks' => SubtaskResource::collection($this->whenLoaded('subtasks')),

        ];
    }
}
