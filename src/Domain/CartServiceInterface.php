<?php declare(strict_types=1);

namespace App\Domain;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\DeliveryAddress;
use Doctrine\Common\Collections\Collection;
use Money\Money;

interface CartServiceInterface
{
    public function summarize(Cart $cart): void;

    public function addToCart(string $userId, int $productId): void;

    public function findOrCreateCart(string $userId): Cart;

    public function getCartTotal(Cart $cart): Money;

    /**
     * @param Cart $cart
     * @return Collection<int, CartItem>
     */
    public function getCartItems(Cart $cart): Collection;

    public function removeFromCart(string $userId, int $productId): void;

    public function addDeliveryAddress(Cart $cart, DeliveryAddress $deliveryAddress): void;

    public function removeCart(Cart $cart): void;
}
