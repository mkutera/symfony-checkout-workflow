<?php declare(strict_types=1);

namespace App\Domain;

use App\Entity\Cart;
use Exception;
use Symfony\Component\Workflow\StateMachine;

class CheckoutService implements CheckoutServiceInterface
{
    public function __construct(private readonly StateMachine $checkoutStateMachine)
    {
    }

    /**
     * @throws Exception
     */
    public function transition(Cart $cart, string $transitionName): void
    {
        if (!$this->checkoutStateMachine->can($cart, $transitionName)) {
            throw new Exception('Transition not allowed');
        }

        $this->checkoutStateMachine->apply($cart, $transitionName);
    }
}
