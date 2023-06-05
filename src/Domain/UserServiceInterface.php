<?php declare(strict_types=1);

namespace App\Domain;

use App\Entity\User;

interface UserServiceInterface
{
    public function findFirstOrCreate(): User;

    public function findUser(string $userId): ?User;
}
