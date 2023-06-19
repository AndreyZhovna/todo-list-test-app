<?php
declare(strict_types=1);

namespace App\Domain\Task\DTO;

use App\Domain\Task\Enums\TaskPriorityEnum;
use App\Exceptions\BusinessException;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class CreateTaskDTO extends Data
{
    /**
     * @throws BusinessException
     */
    public function __construct(
        public string $title,
        public string $description,
        public ?string $parentId,
        public ?string $userId,
        public string $priority,
    ) {
        $this->userId = $userId ?? auth()->id();

        if (!$this->userId) {
            throw new BusinessException('No user id provided.');
        }
    }

    public static function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'parent_id' => 'sometimes|uuid|exists:task,id',
            'priority' => 'required|' . Rule::in(array_column(TaskPriorityEnum::cases(), 'value'))
        ];
    }
}