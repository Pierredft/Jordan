<?php

namespace App\Twig;

use App\Repository\CartRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class CartGlobalExtension extends AbstractExtension implements GlobalsInterface
{
    private CartRepository $cartRepository;
    private Security $security;

    public function __construct(CartRepository $cartRepository, Security $security)
    {
        $this->cartRepository = $cartRepository;
        $this->security = $security;
    }

    public function getGlobals(): array
    {
        $cartQuantity = 0;
        $user = $this->security->getUser();

        if ($user) {
            $cartQuantity = $this->cartRepository->getTotalQuantityByUser($user);
        }

        return [
            'cart_quantity' => $cartQuantity,
        ];
    }
}
