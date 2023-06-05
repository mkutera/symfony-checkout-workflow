<?php declare(strict_types=1);

namespace App\Domain;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;

class UserService implements UserServiceInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    )
    {
    }

    public function findFirstOrCreate(): User
    {
        return $this->userRepository->findFirstOrCreate();
    }

    public function findUser(string $userId): ?User
    {
        $user = $this->userRepository->find($userId);

        if (!$user) {
            return null;
        }

        return $user;
    }
}
