<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(): Response
    {
        $items = [
            [
                'id' => 1,
                'product_code' => 'CI-VEST-04',
                'name' => 'Tactical Recon Vest MK-IV',
                'price' => 3510.00,
                'quantity' => 1,
                'image' => 'images/tactical jacket.png',
                'subtotal' => 3510.00,
            ],
            [
                'id' => 2,
                'product_code' => 'CI-HOOD-01',
                'name' => 'Stealth Ops Heavy Hoodie',
                'price' => 2900.00,
                'quantity' => 1,
                'image' => 'images/hoodie.png',
                'subtotal' => 2900.00,
            ],
        ];

        $subtotal = array_sum(array_column($items, 'subtotal'));
        $shipping = 150.00;
        $total = $subtotal + $shipping;

        return $this->render('cart/index.html.twig', [
            'current_page' => 'cart',
            'items' => $items,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total,
        ]);
    }

    #[Route('/cart/add/{id<\d+>}', name: 'app_cart_add', methods: ['POST'])]
    public function add(int $id, Request $request): Response
    {
        $quantity = max(1, (int) $request->request->get('quantity', 1));
        $this->addFlash('success', "Item #{$id} ({$quantity} unit) allocated to tactical cart manifest.");
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/{id<\d+>}', name: 'app_cart_remove', methods: ['POST'])]
    public function remove(int $id): Response
    {
        $this->addFlash('info', "Item #{$id} retracted from tactical cart manifest.");
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/checkout', name: 'app_cart_checkout', methods: ['POST'])]
    public function checkout(Request $request): Response
    {
        $this->addFlash('success', 'Order requisition successfully dispatched to Caballero Logistics.');
        return $this->redirectToRoute('app_account_orders');
    }
}
