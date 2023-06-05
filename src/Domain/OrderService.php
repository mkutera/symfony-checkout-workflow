<?php declare(strict_types=1);

namespace App\Domain;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\OrderRepositoryInterface;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Money\Money;

class OrderService implements OrderServiceInterface
{
    public function __construct(
        private readonly CartServiceInterface            $cartService,
        private readonly OrderRepositoryInterface        $orderRepository,
        private readonly CheckoutServiceInterface        $checkoutService,
        private readonly EntityManagerInterface $entityManager,
        private readonly CurrencyServiceInterface        $currencyService,
    )
    {
    }


    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function placeAnOrder(Cart $cart): Order
    {
        $this->entityManager->getConnection()->beginTransaction();
        try {
            $order = $this->mapCartToOrderEntity($cart);
            $this->orderRepository->save($order, true);
            $this->cartService->removeCart($cart);
            $this->checkoutService->transition($cart, 'confirm_order');

            $this->entityManager->getConnection()->commit();
        } catch (Exception $e) {
            $this->entityManager->getConnection()->rollBack();
            throw $e;
        }

        return $order;
    }

    /**
     * @param Cart $cart
     * @return Order
     */
    private function mapCartToOrderEntity(Cart $cart): Order
    {
        $order = new Order();
        $order->setUser($cart->getUser());
        $deliveryAddress = $cart->getDeliveryAddress();
        $order->setName($deliveryAddress->getName());
        $order->setAddress($deliveryAddress->getAddress());
        $order->setEmail($deliveryAddress->getEmail());
        $order->setPhone($deliveryAddress->getPhone());
        $order->setPhone($deliveryAddress->getPhone());
        $order->setCountry($deliveryAddress->getCountry());
        $order->setTaxNumber($deliveryAddress->getTaxNumber());
        $order->setCreatedAt(new DateTimeImmutable());

        foreach ($cart->getCartItems() as $item) {
            $order->addOrderItem(
                $this->createOrderItemEntity($item)
            );
        }

        return $order;
    }

    protected function createOrderItemEntity(CartItem $item): OrderItem
    {
        return (new OrderItem())
            ->setPrice($item->getPrice())
            ->setProduct($item->getProduct())
            ->setQuantity($item->getQuantity());
    }

    public function getOrdersByUserId(string $userId): array
    {
        $orders = $this->orderRepository->findBy(['user' => $userId]);

        foreach ($orders as $order) {
            $order->setTotal($this->getCartTotal($order));
        }

        return $orders;
    }

    private function getCartTotal(Order $order): Money
    {
        $total = new Money(0, $this->currencyService->getCurrency());

        foreach ($order->getOrderItems() as $item) {
            $total = $total->add($this->calculateCarItemNetPrice($item));
        }

        return $total;
    }

    private function calculateCarItemNetPrice(OrderItem $orderItem): Money
    {
        return new Money($orderItem->getPrice() * $orderItem->getQuantity(), $this->currencyService->getCurrency());
    }
}
