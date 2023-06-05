<?php declare(strict_types=1);

namespace App\Domain;

use App\Entity\Cart;
use App\Entity\DeliveryAddress;
use App\Repository\DeliveryAddressRepositoryInterface;
use Exception;

class DeliveryAddressService implements DeliveryAddressServiceInterface
{
    public function __construct(
        private readonly DeliveryAddressRepositoryInterface $deliveryAddressRepository,
        private readonly UserServiceInterface $userService,
    )
    {
    }

    private static array $europeanUnionCountryCodes = [
        'AT',
        'PL',
    ];

    public function hasValidDeliveryAddress(Cart $cart): bool
    {
        $deliveryAddress = $cart->getDeliveryAddress();

        if (!$deliveryAddress) {
            return false;
        }

        if ($this->isEuropeanUnionMember($deliveryAddress->getCountry())) {
            return $deliveryAddress->getTaxNumber() !== null;
        }

        return true;
    }

    public function isEuropeanUnionMember(?string $countryCode): bool
    {
        if (null === $countryCode) {
            return false;
        }

        return in_array($countryCode, self::$europeanUnionCountryCodes, true);
    }

    public function getCountries(): array
    {
        return [
            'Austria' => 'AT',
            'Colombia' => 'CO',
            'Poland' => 'PL',
            'United States' => 'US',
        ];
    }

    public function save(DeliveryAddress $deliveryAddress): void
    {
        $this->deliveryAddressRepository->save($deliveryAddress, true);
    }

    public function findDeliveryAddressByUserId(string $userId): ?DeliveryAddress
    {
        return $this->deliveryAddressRepository->findOneByUser($userId);
    }

    /**
     * @throws Exception
     */
    public function newDeliveryAddress(string $userId): DeliveryAddress
    {
        $user = $this->userService->findUser($userId);

        if (!$user) {
            throw new Exception('User not found');
        }

        $deliveryAddress = new DeliveryAddress();
        $deliveryAddress->setUser($user);

        return $deliveryAddress;
    }
}
