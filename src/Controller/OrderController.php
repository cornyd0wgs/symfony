<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    #[Route('/admin/orders', name: 'admin_orders')]
    public function index(): Response
    {
        $orders = [
            [
                'id' => 1042,
                'customer_name' => 'Marcus Vance',
                'customer_email' => 'm.vance@tactical.io',
                'total_amount' => 6450.00,
                'status' => 'completed',
                'payment_method' => 'GCash',
                'created_at' => '2026-10-06 08:30:00',
                'items_count' => 2,
            ],
            [
                'id' => 1041,
                'customer_name' => 'Elena Rostova',
                'customer_email' => 'elena.rostova@gmail.com',
                'total_amount' => 12900.00,
                'status' => 'processing',
                'payment_method' => 'Bank Transfer',
                'created_at' => '2026-10-06 07:15:00',
                'items_count' => 3,
            ],
            [
                'id' => 1040,
                'customer_name' => 'David Kim',
                'customer_email' => 'dkim@security.com',
                'total_amount' => 4200.00,
                'status' => 'pending',
                'payment_method' => 'Cash on Delivery',
                'created_at' => '2026-10-05 19:40:00',
                'items_count' => 1,
            ],
            [
                'id' => 1039,
                'customer_name' => 'Sarah Jenkins',
                'customer_email' => 'sjenkins@outdoors.org',
                'total_amount' => 8150.00,
                'status' => 'completed',
                'payment_method' => 'GCash',
                'created_at' => '2026-10-05 14:10:00',
                'items_count' => 2,
            ],
            [
                'id' => 1038,
                'customer_name' => 'Rafael Moreno',
                'customer_email' => 'rafael.moreno@corp.ph',
                'total_amount' => 3100.00,
                'status' => 'cancelled',
                'payment_method' => 'Cash on Delivery',
                'created_at' => '2026-10-04 11:05:00',
                'items_count' => 1,
            ],
        ];

        return $this->render('admin/orders.html.twig', [
            'current_page' => 'orders',
            'orders' => $orders,
        ]);
    }
}
