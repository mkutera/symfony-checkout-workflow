<?php declare(strict_types=1);

namespace App\Domain;

use App\Entity\Cart;

interface CheckoutServiceInterface
{
    public function transition(Cart $cart, string $transitionName): void;
}
