<?php

namespace App\Domain\Task\Enums;

enum TaskPriorityEnum: int
{
    case Low = 1;
    case Moderate = 2;
    case High = 3;
    case Critical = 4;
    case Top = 5;
}
