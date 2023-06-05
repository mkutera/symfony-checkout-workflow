<?php declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Cart;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\GuardEvent;

class CartHasItemsGuardSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.checkout.guard.add_delivery_address' => ['onGuard'],
            'workflow.checkout.guard.to_summary' => ['onGuard'],
            'workflow.checkout.guard.confirm_order' => ['onGuard'],
        ];
    }

    public function onGuard(GuardEvent $event): void
    {
        $cart = $event->getSubject();
        assert($cart instanceof Cart);
        if ($cart->getCartItems()->count() === 0) {
            $event->setBlocked(true, 'You must add items to your cart before you can proceed');
        }
    }
}
