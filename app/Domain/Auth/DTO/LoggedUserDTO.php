<?php
declare(strict_types=1);

namespace App\Domain\Auth\DTO;

use Illuminate\Http\Request;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class LoggedUserDTO extends Data
{
    public function __construct(
        public readonly string $userId,
        public readonly string $token
    ) {}

    public static function fromRequest(Request $request): self
    {
        return self::from([
            ...$request->all()
        ]);
    }
}
