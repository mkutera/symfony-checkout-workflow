<?php declare(strict_types=1);

namespace App\EventSubscriber;

use App\Domain\UserServiceInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AutoLoginSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Security              $security,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly UserServiceInterface  $userService,
    )
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if ($this->security->getUser()) {
            return;
        }

        $user = $this->userService->findFirstOrCreate();

        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $this->tokenStorage->setToken($token);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }
}
