<?php

namespace App\Controller;

use App\Domain\CartServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\StateMachine;

class PurchaseSummaryController extends AbstractController
{
    public function __construct(
        private readonly CartServiceInterface  $cartService,
        private readonly StateMachine $checkoutStateMachine,
    )
    {
    }

    #[Route('/purchase-summary', name: 'purchase_summary')]
    public function index(): Response
    {
        $cart = $this->cartService->findOrCreateCart($this->getUser()->getUserIdentifier());

        if (!$this->checkoutStateMachine->can($cart, 'to_summary')) {
            return $this->redirectToRoute('delivery_address');
        }

        $this->cartService->summarize($cart);

        return $this->render('purchase_summary/index.html.twig', [
            'cart' => $this->cartService->findOrCreateCart(1),
            'cartItems' => $this->cartService->getCartItems($cart),
            'total' => $this->cartService->getCartTotal($cart),
        ]);
    }
}
