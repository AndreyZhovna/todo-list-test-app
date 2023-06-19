<?php

namespace App\Http\Resources\Task;

use App\Domain\Task\Entities\Task;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        /** @var Task|self $this */
        return [
            'id' =>  $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'priority' => $this->priority,
            'created_at' => $this->created_at->toDateTimeString(),
            'completed_at' => $this->completed_at?->toDateTimeString(),

            'parent_task' => $this->whenLoaded('parentTask', function () {
                return TaskResource::make($this->parentTask);
            }),
            'sub_tasks' => $this->whenLoaded('subTasks', function () {
                return TaskResource::collection($this->subTasks);
            }),
        ];
    }
}
