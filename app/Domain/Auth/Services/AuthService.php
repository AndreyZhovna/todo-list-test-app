<?php
declare(strict_types=1);

namespace App\Domain\Auth\Services;

use App\Domain\Auth\DTO\LoggedUserDTO;
use App\Domain\Auth\DTO\LoginUserDTO;
use App\Domain\Auth\DTO\RegisterUserDTO;
use App\Domain\Auth\Entities\User;
use App\Domain\Shared\Services\PasswordHasher;
use App\Exceptions\BusinessException;
use Illuminate\Support\Str;

final class AuthService
{
    public function __construct(
        private readonly PasswordHasher $passwordHasher
    ) {}

    /**
     * @param string $userId
     * @return User|null
     */
    public function findUserById(string $userId): ?User
    {
        return User::find($userId);
    }

    /**
     * @param string $email
     * @return User|null
     */
    public function findUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * @param LoginUserDTO $dto
     * @return LoggedUserDTO
     * @throws BusinessException
     */
    public function login(LoginUserDTO $dto): LoggedUserDTO
    {
        $user = $this->findUserByEmail($dto->email);

        if (!$user) {
            throw new BusinessException('The user with the specified email was not found.');
        }

        if (!$this->passwordHasher->compare($dto->password, $user->password)) {
            throw new BusinessException('The password is incorrect.');
        }

        $apiToken = $this->createUserToken($user->id);

        return new LoggedUserDTO($user->id, $apiToken);
    }

    /**
     * @throws BusinessException
     */
    public function register(RegisterUserDTO $dto): LoggedUserDTO
    {
        $doesEmailTaken = $this->findUserByEmail($dto->email);

        if ($doesEmailTaken) {
            throw new BusinessException('This email is already taken.');
        }

        $user = User::create([
            'id' => Str::orderedUuid(),
            'name' => $dto->name,
            'email' => $dto->email,
            'email_verified_at' => now(), // for test purposes
            'password' => $this->passwordHasher->hash($dto->password),
        ]);
        $apiToken = $this->createUserToken($user->id);

        return new LoggedUserDTO($user->id, $apiToken);
    }

    /**
     * @throws BusinessException
     */
    public function createUserToken(string $userId): string
    {
        $user = $this->findUserById($userId);

        if (!$user) {
            throw new BusinessException('User does not exist.');
        }

        return $user->createToken('Personal Access Token')->plainTextToken;
    }
}