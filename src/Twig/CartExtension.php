<?php

namespace App\Twig;

use App\Entity\Cart;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class CartExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly EntityManagerInterface $em
    ) {
    }

    public function getGlobals(): array
    {
        $user = $this->security->getUser();
        $cartCount = 0;

        if ($user instanceof User) {
            $cart = $this->em->getRepository(Cart::class)->findOneBy(['user' => $user, 'status' => 'active']);
            if ($cart) {
                foreach ($cart->getItems() as $item) {
                    $cartCount += $item->getQuantity();
                }
            }
        }

        return [
            'cart_count' => $cartCount,
        ];
    }
}
