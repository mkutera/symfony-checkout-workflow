<?php declare(strict_types=1);

namespace App\Domain;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Money\Money;

class ProductService implements ProductServiceInterface
{
    public function __construct(
        private readonly EntityManagerInterface   $entityManager,
        private readonly CurrencyServiceInterface $currencyService,
    )
    {
    }

    public function findProduct(int $productId): ?Product
    {
        $product = $this->findOneProductById($productId);

        return $product?->setNetPrice($this->getMoneyNetPrice($product));

    }

    /**
     * @return Product[]
     */
    public function get(): array
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll();

        /** @var Product $product */
        foreach ($products as $product) {
            $product->setNetPrice($this->getMoneyNetPrice($product));
        }

        return $products;
    }

    protected function getMoneyNetPrice(Product $product): Money
    {
        return new Money($product->getPrice(), $this->currencyService->getCurrency());
    }

    protected function findOneProductById(int $productId): ?Product
    {
        return $this->entityManager->getRepository(Product::class)->find($productId);
    }
}
