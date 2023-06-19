<?php

namespace App\Domain\Task\Enums;

enum TaskStatusEnum: string
{
    case TODO = 'todo';
    case DONE = 'done';
}
