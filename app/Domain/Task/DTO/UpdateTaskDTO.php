<?php
declare(strict_types=1);

namespace App\Domain\Task\DTO;

use App\Domain\Task\Enums\TaskPriorityEnum;
use App\Domain\Task\Enums\TaskStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class UpdateTaskDTO extends Data
{
    public function __construct(
        public string $id,
        public ?string $title,
        public ?string $description,
        public ?string $parentId,
        public string $priority,
        public ?string $status,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return self::from([
            ...$request->all(),
            'id' => $request->route('taskId')
        ]);
    }

    public static function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'parent_id' => 'sometimes|uuid|exists:task,id',
            'priority' => 'required|' . Rule::in(array_column(TaskPriorityEnum::cases(), 'value')),
            'status' => 'required|' . Rule::in(array_column(TaskStatusEnum::cases(), 'value')),
        ];
    }
}