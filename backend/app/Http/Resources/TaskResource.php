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
            'id'        => $this->id,
            'statement' => $this->statement,
            'task_date' => $this->task_date?->format('Y-m-d'),
            'priority'  => (int) $this->priority,
            'position'  => (int) $this->position,
            'is_done'   => (bool) $this->is_done,
            'created_at'=> $this->created_at?->toISOString(),
            'updated_at'=> $this->updated_at?->toISOString(),
        ];
    }
}
