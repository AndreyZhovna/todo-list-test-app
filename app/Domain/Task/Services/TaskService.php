<?php
declare(strict_types=1);

namespace App\Domain\Task\Services;

use App\Domain\Task\DTO\CreateTaskDTO;
use App\Domain\Task\DTO\UpdateTaskDTO;
use App\Domain\Task\Entities\Task;
use App\Exceptions\BusinessException;
use App\Domain\Task\Enums\TaskStatusEnum;
use Illuminate\Support\Str;

final class TaskService
{
    /**
     * @param string $taskId
     * @return Task|null
     */
    public function findTaskById(string $taskId): ?Task
    {
        return Task::find($taskId);
    }

    /**
     * @param string $taskId
     * @return bool
     */
    public function doesTaskHaveIncompleteSubTasks(string $taskId): bool
    {
        return Task::query()
            ->where('parent_id', $taskId)
            ->where('status', TaskStatusEnum::TODO)
            ->exists();
    }

    /**
     * @param CreateTaskDTO $dto
     * @return Task
     */
    public function createTask(CreateTaskDTO $dto): Task
    {
        return Task::create([
            'id' => Str::orderedUuid(),
            'title' => $dto->title,
            'description' => $dto->description,
            'parent_id' => $dto->parentId,
            'user_id' => $dto->userId,
            'status' => TaskStatusEnum::TODO->value,
            'priority' => $dto->priority
        ]);
    }

    /**
     * @param UpdateTaskDTO $dto
     * @return void
     * @throws BusinessException
     */
    public function updateTask(UpdateTaskDTO $dto): void
    {
        $task = $this->findTaskById($dto->id);

        if (!$task) {
            throw new BusinessException('No task found.');
        }

        // Unset completed_at, if task returns to the "ToDo" status
        if ($task->status === TaskStatusEnum::DONE->value && $dto->status === TaskStatusEnum::TODO->value) {
            $task->completed_at = null;
        }

        $task
            ->fill([
                'title' => $dto->title,
                'description' => $dto->description,
                'parent_id' => $dto->parentId,
                'priority' => $dto->priority,
                'status' => $dto->status
            ])
            ->save();
    }

    /**
     * @param string $taskId
     * @return void
     * @throws BusinessException
     */
    public function deleteTask(string $taskId): void
    {
        $task = $this->findTaskById($taskId);

        if (!$task) {
            throw new BusinessException('No task found.');
        }

        if ($task->status === TaskStatusEnum::DONE->value) {
            throw new BusinessException('You cannot delete the completed task.');
        }

        $task->delete();
        $this->deleteSubTasksByParentId($taskId);
    }

    /**
     * @param string $taskId
     * @return void
     * @throws BusinessException
     */
    public function markTaskAsCompleted(string $taskId): void
    {
        $task = $this->findTaskById($taskId);

        if (!$task) {
            throw new BusinessException('No task found.');
        }

        if ($task->status === TaskStatusEnum::DONE->value) {
            throw new BusinessException('The task is already marked as completed.');
        }

        if ($this->doesTaskHaveIncompleteSubTasks($taskId)) {
            throw new BusinessException('The task has incomplete subtasks.');
        }

        $task->status = TaskStatusEnum::DONE->value;
        $task->completed_at = now();
        $task->save();
    }

    /**
     * @param string $taskId
     * @return void
     */
    private function deleteSubTasksByParentId(string $taskId): void
    {
        Task::query()
            ->where('parent_id', $taskId)
            ->delete();
    }
}