<?php

namespace App\Controller;

use App\Domain\CartServiceInterface;
use App\Domain\OrderServiceInterface;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\StateMachine;

class OrderController extends AbstractController
{
    public function __construct(
        private readonly CartServiceInterface  $cartService,
        private readonly OrderServiceInterface $orderService,
        private readonly StateMachine $checkoutStateMachine,
    )
    {
    }

    #[Route('/order', name: 'order', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('order/index.html.twig', [
            'orders' => $this->orderService->getOrdersByUserId($this->getUser()->getUserIdentifier()),
        ]);
    }


    /**
     * @return Response
     */
    #[Route('/order', name: 'place_order', methods: ['POST'])]
    public function makeOrder(): Response
    {
        $cart = $this->cartService->findOrCreateCart($this->getUser()->getUserIdentifier());

        if (!$this->checkoutStateMachine->can($cart, 'confirm_order')) {
            return $this->redirectToRoute('cart');
        }

        $order = $this->orderService->placeAnOrder($cart);

        $this->addFlash('success', "We confirm your order #{$order->getId()}. Thank you!");

        return $this->redirectToRoute('order');
    }
}
