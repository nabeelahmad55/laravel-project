<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => $this->status,
            'due_date' => $this->due_date?->format('Y-m-d'),
            'completed_at' => $this->completed_at?->toDateTimeString(),
            'position' => $this->position,
            'project' => new ProjectResource($this->whenLoaded('project')),
            'assignee' => new UserResource($this->whenLoaded('assignee')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'comments_count' => $this->whenCounted('comments'),
            'attachments_count' => $this->whenCounted('attachments'),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'links' => [
                'self' => route('tasks.show', $this->id),
                'project' => route('projects.show', $this->project_id)
            ]
        ];
    }
}