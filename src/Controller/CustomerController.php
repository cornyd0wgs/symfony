<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CustomerController extends AbstractController
{
    #[Route('/admin/customers', name: 'admin_customers')]
    public function index(): Response
    {
        $customers = [
            [
                'id' => 1,
                'full_name' => 'Marcus Vance',
                'email' => 'm.vance@tactical.io',
                'age' => 34,
                'order_count' => 12,
                'total_spent' => 46800.00,
                'created_at' => '2026-03-14',
            ],
            [
                'id' => 2,
                'full_name' => 'Elena Rostova',
                'email' => 'elena.rostova@gmail.com',
                'age' => 29,
                'order_count' => 7,
                'total_spent' => 31500.00,
                'created_at' => '2026-04-02',
            ],
            [
                'id' => 3,
                'full_name' => 'David Kim',
                'email' => 'dkim@security.com',
                'age' => 41,
                'order_count' => 5,
                'total_spent' => 18400.00,
                'created_at' => '2026-05-18',
            ],
            [
                'id' => 4,
                'full_name' => 'Sarah Jenkins',
                'email' => 'sjenkins@outdoors.org',
                'age' => 27,
                'order_count' => 9,
                'total_spent' => 27950.00,
                'created_at' => '2026-06-09',
            ],
            [
                'id' => 5,
                'full_name' => 'Rafael Moreno',
                'email' => 'rafael.moreno@corp.ph',
                'age' => 38,
                'order_count' => 3,
                'total_spent' => 9300.00,
                'created_at' => '2026-07-22',
            ],
        ];

        return $this->render('admin/customers.html.twig', [
            'current_page' => 'customers',
            'customers' => $customers,
        ]);
    }
}
