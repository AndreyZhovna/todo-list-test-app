<?php

namespace App\Domain\Task\Policies;

use App\Domain\Auth\Entities\User;
use App\Domain\Task\Entities\Task;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    public function update(User $user, Task $task): Response
    {
        return $user->id === $task->user_id
            ? Response::allow()
            : throw new AuthorizationException('You do not have access to the task.');
    }

    public function complete(User $user, Task $task): Response
    {
        return $user->id === $task->user_id
            ? Response::allow()
            : throw new AuthorizationException('You do not have access to the task.');
    }

    public function delete(User $user, Task $task): Response
    {
        return $user->id === $task->user_id
            ? Response::allow()
            : throw new AuthorizationException('You do not have access to the task.');
    }
}