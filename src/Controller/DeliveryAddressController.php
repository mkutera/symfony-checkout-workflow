<?php

namespace App\Controller;

use App\Domain\CartServiceInterface;
use App\Domain\DeliveryAddressServiceInterface;
use App\Form\DeliveryAddressType;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\StateMachine;

class DeliveryAddressController extends AbstractController
{
    public function __construct(
        private readonly CartServiceInterface                     $cartService,
        private readonly DeliveryAddressServiceInterface $deliveryAddressService,
        private readonly StateMachine                    $checkoutStateMachine,
    )
    {
    }

    /**
     * @throws Exception
     */
    #[Route('/delivery', name: 'delivery_address')]
    public function index(Request $request): Response
    {
        $userId = $this->getUser()->getUserIdentifier();
        $cart = $this->cartService->findOrCreateCart($userId);

        if (!$this->checkoutStateMachine->can($cart, 'add_delivery_address')) {
            return $this->redirectToRoute('cart');
        }

        $deliveryAddress = $this->deliveryAddressService->findDeliveryAddressByUserId($userId)
            ?? $this->deliveryAddressService->newDeliveryAddress($userId);

        $form = $this
            ->createForm(DeliveryAddressType::class, $deliveryAddress)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->cartService->addDeliveryAddress($cart, $deliveryAddress);
            $this->addFlash('success', 'Delivery address saved successfully!');
        }

        return $this->render('delivery_address/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart,
            'total' => $this->cartService->getCartTotal($cart),
        ]);
    }
}
