<?php

namespace App\Controller;

use App\Domain\ProductServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/', name: 'products')]
    public function index(ProductServiceInterface $productService): Response
    {
        $products = $productService->get();

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }
}
