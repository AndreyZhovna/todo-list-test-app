<?php

namespace App\Http\Controllers\Api\V1\Task;

use App\Domain\Task\DTO\CreateTaskDTO;
use App\Domain\Task\DTO\UpdateTaskDTO;
use App\Domain\Task\Entities\Task;
use App\Domain\Task\Services\TaskService;
use App\Exceptions\BusinessException;
use App\Http\Resources\Task\TaskResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly TaskService $taskService
    ) {}

    /**
     * @return JsonResponse
     */
    public function getTasks(): JsonResponse
    {
        $tasks = QueryBuilder::for(Task::class)
            ->with('parentTask', 'subTasks')
            ->allowedFilters([
                AllowedFilter::scope('title'),
                AllowedFilter::scope('priority_from'),
                AllowedFilter::scope('priority_to'),
                AllowedFilter::scope('status'),
            ])
            ->allowedSorts(['created_at', 'completed_at', 'priority'])
            ->where('user_id', auth()->id())
            ->get();
        $collection = TaskResource::collection($tasks);

        return responder()->ok($collection);
    }

    /**
     * @param CreateTaskDTO $dto
     * @return JsonResponse
     */
    public function createTask(CreateTaskDTO $dto): JsonResponse
    {
        $task = $this->taskService->createTask($dto);

        return responder()
            ->message('The task was successfully created.')
            ->created(['id' => $task->id]);
    }

    /**
     * @param Request $request
     * @param string $taskId
     * @return JsonResponse
     * @throws BusinessException
     */
    public function updateTask(Request $request, string $taskId): JsonResponse
    {
        $task = $this->taskService->findTaskById($taskId);
        $this->authorize('update', $task);

        $dto = UpdateTaskDTO::fromRequest($request);
        $this->taskService->updateTask($dto);

        return responder()
            ->message('The task was successfully updated.')
            ->ok();
    }

    /**
     * @param string $taskId
     * @return JsonResponse
     * @throws BusinessException
     */
    public function markTaskAsCompleted(string $taskId): JsonResponse
    {
        $task = $this->taskService->findTaskById($taskId);
        $this->authorize('complete', $task);

        $this->taskService->markTaskAsCompleted($taskId);

        return responder()
            ->message('The task was marked as completed.')
            ->ok();
    }

    /**
     * @param string $taskId
     * @return JsonResponse
     * @throws BusinessException
     */
    public function deleteTask(string $taskId): JsonResponse
    {
        $task = $this->taskService->findTaskById($taskId);
        $this->authorize('delete', $task);

        $this->taskService->deleteTask($taskId);

        return responder()
            ->message('The task was successfully deleted.')
            ->ok();
    }
}
