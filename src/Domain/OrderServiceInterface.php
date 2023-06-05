<?php declare(strict_types=1);

namespace App\Domain;

use App\Entity\Cart;
use App\Entity\Order;

interface OrderServiceInterface
{
    public function placeAnOrder(Cart $cart): Order;

    /**
     * @return Order[]
     */
    public function getOrdersByUserId(string $userId): array;
}
