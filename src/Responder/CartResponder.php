<?php declare(strict_types=1);

namespace App\Responder;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CartResponder
{
    public function addToCartSuccess(): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => 'Product added to cart successfully.',
        ];

        return new JsonResponse($response, Response::HTTP_CREATED);
    }

    public function addToCartError(string $errorMessage): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => 'Failed to add product to cart.',
            'error' => $errorMessage,
        ];

        return new JsonResponse($response, Response::HTTP_BAD_REQUEST);
    }
}
