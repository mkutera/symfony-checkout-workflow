<?php declare(strict_types=1);

namespace App\Domain;

use App\Entity\Product;

interface ProductServiceInterface
{
    public function findProduct(int $productId): ?Product;

    /**
     * @return Product[]
     */
    public function get(): array;
}
