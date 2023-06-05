<?php

namespace App\Controller;

use App\Domain\CartServiceInterface;
use App\Responder\CartResponder;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    public function __construct(
        private readonly CartServiceInterface   $cartService,
        private readonly CartResponder $cartResponder,
    )
    {
    }

    #[Route('/cart', name: 'cart', methods: ['GET'])]
    public function index(): Response
    {
        $cart = $this->cartService->findOrCreateCart($this->getUser()->getUserIdentifier());
        $cartItems = $this->cartService->getCartItems($cart);
        return $this->render('cart/index.html.twig', [
            'total' => $this->cartService->getCartTotal($cart),
            'cartItems' => $cartItems,
            'cart' => $cart,
        ]);
    }

    #[Route('/cart', name: 'add-to-cart', methods: ['POST'])]
    public function addToCart(Request $request): Response
    {
        $productId = $request->get('productId');

        try {
            $this->cartService->addToCart($this->getUser()->getUserIdentifier(), (int)$productId);
            return $this->cartResponder->addToCartSuccess();
        } catch (Exception $e) {
            return $this->cartResponder->addToCartError($e->getMessage());
        }
    }

    #[Route('/cart/{productId}', name: 'remove-from-cart', methods: ['PATCH'])]
    public function removeFromCart(int $productId): Response
    {
        try {
            $this->cartService->removeFromCart($this->getUser()->getUserIdentifier(), $productId);

            return $this->cartResponder->addToCartSuccess();
        } catch (Exception $e) {
            return $this->cartResponder->addToCartError($e->getMessage());
        }
    }
}
