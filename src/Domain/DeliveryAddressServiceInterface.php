<?php declare(strict_types=1);

namespace App\Domain;

use App\Entity\Cart;
use App\Entity\DeliveryAddress;

interface DeliveryAddressServiceInterface
{
    public function hasValidDeliveryAddress(Cart $cart): bool;

    public function isEuropeanUnionMember(?string $countryCode): bool;

    public function getCountries(): array;

    public function save(DeliveryAddress $deliveryAddress): void;

    public function findDeliveryAddressByUserId(string $userId): ?DeliveryAddress;

    public function newDeliveryAddress(string $userId): DeliveryAddress;
}
