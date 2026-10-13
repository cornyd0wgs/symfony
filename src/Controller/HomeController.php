<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $statusLeft = '[ LOC. 14.5995° N / 120.9842° E ] // SECURE_LINE_ONLINE';
        $statusRight = 'VER. 25.4.19 // TACTICAL_CORE';

        $principles = [
            [
                'title' => 'INNOVATION',
                'text'  => 'Integrating avant-garde tech materials and modular designs into daily techwear.',
            ],
            [
                'title' => 'QUALITY',
                'text'  => 'Strict wear-testing and military-grade thread components ensure endless durability.',
            ],
            [
                'title' => 'PURPOSE',
                'text'  => 'Every pocket, seam, strap, and enclosure is engineered with precise utility.',
            ],
            [
                'title' => 'DISCIPLINE',
                'text'  => 'Unwavering commitment to clean structural aesthetics and functional precision.',
            ],
        ];

        $partners = [
            ['name' => 'APEX SYSTEMS',   'tag' => 'TACTICAL_NET'],
            ['name' => 'IRONFORGE CO.',  'tag' => 'HEAVY_FABRIC'],
            ['name' => 'VERTEX TECH',    'tag' => 'LAB_SPEC_V1'],
            ['name' => 'BLACKLINE MFG.', 'tag' => 'HARDWARE_SYS'],
        ];

        $featuredProducts = [
            [
                'id' => 1,
                'product_code' => 'CI-VEST-04',
                'name' => 'Tactical Recon Vest MK-IV',
                'price' => 3900.00,
                'discount_price' => 3510.00,
                'discount' => 10,
                'quantity' => 4,
                'units_sold' => 48,
                'is_popular' => true,
                'stock_status' => 'ONLY 4 UNITS LEFT',
                'stock_level' => 'critical',
                'image' => 'images/tactical jacket.png',
                'badge' => 'ACTIVE DROP',
            ],
            [
                'id' => 2,
                'product_code' => 'CI-HOOD-01',
                'name' => 'Stealth Ops Heavy Hoodie',
                'price' => 2900.00,
                'discount_price' => null,
                'discount' => 0,
                'quantity' => 2,
                'units_sold' => 39,
                'is_popular' => true,
                'stock_status' => 'ONLY 2 UNITS LEFT',
                'stock_level' => 'critical',
                'image' => 'images/hoodie.png',
                'badge' => 'HIGH DEMAND',
            ],
            [
                'id' => 3,
                'product_code' => 'CI-BAG-02',
                'name' => 'Modular Deployment Bag',
                'price' => 2500.00,
                'discount_price' => 2375.00,
                'discount' => 5,
                'quantity' => 18,
                'units_sold' => 31,
                'is_popular' => false,
                'stock_status' => 'IN STOCK',
                'stock_level' => 'ok',
                'image' => 'images/bag.png',
                'badge' => null,
            ],
            [
                'id' => 4,
                'product_code' => 'CI-JACK-05',
                'name' => 'Armored All-Weather Jacket',
                'price' => 3500.00,
                'discount_price' => null,
                'discount' => 0,
                'quantity' => 15,
                'units_sold' => 27,
                'is_popular' => false,
                'stock_status' => 'IN STOCK',
                'stock_level' => 'ok',
                'image' => 'images/jacket.png',
                'badge' => null,
            ],
        ];

        return $this->render('home/index.html.twig', [
            'current_page' => 'home',
            'status_left' => $statusLeft,
            'status_right' => $statusRight,
            'principles' => $principles,
            'partners' => $partners,
            'products' => $featuredProducts,
            'active_drop_number' => '04',
            'active_drop_cycle' => 'BATCH #04 // RECON INITIATIVE',
        ]);
    }
}
