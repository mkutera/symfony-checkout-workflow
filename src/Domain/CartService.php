<?php declare(strict_types=1);

namespace App\Domain;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\DeliveryAddress;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\CartItemRepositoryInterface;
use App\Repository\CartRepositoryInterface;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Money\Money;

class CartService implements CartServiceInterface
{
    private const DEFAULT_QUANTITY = 1;

    public function __construct(
        private readonly EntityManagerInterface      $entityManager,
        private readonly CartRepositoryInterface     $cartRepository,
        private readonly CartItemRepositoryInterface $cartItemRepository,
        private readonly ProductServiceInterface              $productService,
        private readonly CurrencyServiceInterface             $currencyService,
        private readonly UserServiceInterface                 $userService,
        private readonly CheckoutServiceInterface             $checkoutService,
        private readonly DeliveryAddressServiceInterface      $deliveryAddressService,
    )
    {
    }

    /**
     * @throws Exception
     */
    public function summarize(Cart $cart): void
    {
        $this->checkoutService->transition($cart, 'to_summary');
        $this->saveCart($cart);
    }
    /**
     * @throws Exception
     */
    public function addToCart(string $userId, int $productId): void
    {
        $product = $this->productService->findProduct($productId);

        if (!$product) {
            throw new Exception('Product not found');
        }

        $cart = $this->findOrCreateCart($userId);
        $cartItem = $cart->findCartItemByProduct($product);

        if ($cartItem) {
            $cartItem->incrementQuantity();
        } else {
            $cartItem = $this->createCartItem($cart, $product);
            $cart->addCartItem($cartItem);
        }

        $this->entityManager->getConnection()->beginTransaction();
        try {
            $this->checkoutService->transition($cart, 'add_items_to_cart');
            $this->saveCart($cart);
            $this->entityManager->getConnection()->commit();
        } catch (Exception $e) {
            $this->entityManager->getConnection()->rollBack();
            throw $e;
        }
    }

    public function findOrCreateCart(string $userId): Cart
    {
        $cart = $this->cartRepository->findOneByUser($userId);

        if (!$cart) {
            $user = $this->userService->findUser($userId);
            $cart = $this->createCart($user);
        }

        return $cart;
    }



    public function getCartTotal(Cart $cart): Money
    {
        $cartItems = $this->getCartItems($cart);
        $total = new Money(0, $this->currencyService->getCurrency());

        foreach ($cartItems as $cartItem) {
            $total = $total->add($this->calculateCarItemNetPrice($cartItem));
        }

        return $total;
    }

    public function getCartItems(Cart $cart): Collection
    {
        $cartItems = $cart->getCartItems();

        foreach ($cartItems as $cartItem) {
            $cartItem->setNetPrice($this->calculateCarItemNetPrice($cartItem));
        }

        return $cartItems;
    }


    /**
     * @throws Exception
     */
    public function removeFromCart(string $userId, int $productId): void
    {
        $product = $this->productService->findProduct($productId);

        if (!$product) {
            throw new Exception('Product not found');
        }

        $cart = $this->findOrCreateCart($userId);
        $cartItem = $cart->findCartItemByProduct($product);

        if (!$cartItem) {
            throw new Exception('Cart item not found');
        }

        $this->cartItemRepository->remove($cartItem, true);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function addDeliveryAddress(Cart $cart, DeliveryAddress $deliveryAddress): void
    {
        $this->entityManager->getConnection()->beginTransaction();
        try {
            $this->deliveryAddressService->save($deliveryAddress);
            $this->checkoutService->transition($cart, 'add_delivery_address');
            $this->saveCart($cart->setDeliveryAddress($deliveryAddress));
            $this->entityManager->getConnection()->commit();
        } catch (Exception $e) {
            $this->entityManager->getConnection()->rollBack();
            throw $e;
        }
    }

    public function removeCart(Cart $cart): void
    {
        $this->cartRepository->remove($cart, true);
    }

    private function createCart(User $user): Cart
    {
        $entity = new Cart();
        $entity->setUser($user);
        $entity->setCurrentState('shopping_cart');
        $this->saveCart($entity);

        return $entity;
    }

    private function createCartItem(Cart $cart, Product $product): CartItem
    {
        $date = new DateTimeImmutable();
        return (new CartItem())
            ->setCart($cart)
            ->setProduct($product)
            ->setQuantity(self::DEFAULT_QUANTITY)
            ->setCreatedAt($date)
            ->setUpdatedAt($date)
            ->setPrice((int)$product->getNetPrice()->getAmount());
    }

    private function saveCart(Cart $cart): void
    {
        $this->cartRepository->save($cart, true);
    }

    private function calculateCarItemNetPrice(CartItem $cartItem): Money
    {
        return new Money($cartItem->getPrice() * $cartItem->getQuantity(), $this->currencyService->getCurrency());
    }
}
