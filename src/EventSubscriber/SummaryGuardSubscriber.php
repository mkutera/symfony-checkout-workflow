<?php declare(strict_types=1);

namespace App\EventSubscriber;

use App\Domain\DeliveryAddressServiceInterface;
use App\Entity\Cart;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\GuardEvent;

class SummaryGuardSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly DeliveryAddressServiceInterface $deliveryAddressService)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.checkout.guard.to_summary' => ['onGuard'],
            'workflow.checkout.guard.confirm_order' => ['onGuard'],
        ];
    }

    public function onGuard(GuardEvent $event): void
    {
        $cart = $event->getSubject();
        assert($cart instanceof Cart);

        if (!$this->deliveryAddressService->hasValidDeliveryAddress($cart)) {
            $event->setBlocked(true, 'You must add valid delivery address to your cart before you can proceed');
        }
    }
}
