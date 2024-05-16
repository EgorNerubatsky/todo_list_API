<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            "status" => $this->status,
            "priority" => $this->priority,
            "title" => $this->title,
            "description" => $this->description,
            "created_at" => $this->created_at,
            "completed_at" => $this->completed_at,
            'subtasks' => TaskResource::collection($this->whenLoaded('subtasks')),
        ];
    }
}
