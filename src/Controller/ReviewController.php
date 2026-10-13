<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReviewController extends AbstractController
{
    #[Route('/admin/reviews', name: 'admin_reviews')]
    public function index(): Response
    {
        $reviews = [
            [
                'id' => 1,
                'customer_name' => 'Carlos Mendoza',
                'product_name' => 'Tactical Recon Vest MK-IV',
                'rating' => 5,
                'comment' => 'Exceptional build quality and stitching. Plate pockets fit securely with zero bounce.',
                'created_at' => '2026-10-05',
            ],
            [
                'id' => 2,
                'customer_name' => 'Liam Bennett',
                'product_name' => 'Armored All-Weather Jacket',
                'rating' => 5,
                'comment' => 'Water repellent finish held up under intense rain. Highly recommended for field use.',
                'created_at' => '2026-10-04',
            ],
            [
                'id' => 3,
                'customer_name' => 'Alex Thorne',
                'product_name' => 'Modular Deployment Bag',
                'rating' => 4,
                'comment' => 'Great organization and compartment layout. Heavy-duty zippers make access fluid.',
                'created_at' => '2026-10-03',
            ],
            [
                'id' => 4,
                'customer_name' => 'Jordan Reyes',
                'product_name' => 'Stealth Ops Heavy Hoodie',
                'rating' => 5,
                'comment' => 'Comfortable, heavyweight fabric that keeps you warm without bulk.',
                'created_at' => '2026-09-28',
            ],
        ];

        return $this->render('admin/reviews.html.twig', [
            'current_page' => 'reviews',
            'reviews' => $reviews,
        ]);
    }
}
