<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    private function getSampleProducts(): array
    {
        return [
            [
                'id' => 1,
                'product_code' => 'CI-VEST-04',
                'name' => 'Tactical Recon Vest MK-IV',
                'description' => 'Lightweight ballistic nylon tactical vest with modular MOLLE webbing, reinforced drag handle, and rapid-release side buckles.',
                'price' => 3900.00,
                'discount' => 10,
                'discount_price' => 3510.00,
                'quantity' => 4,
                'units_sold' => 48,
                'is_featured' => true,
                'is_popular' => true,
                'image' => 'images/tactical jacket.png',
                'category' => 'Outerwear',
            ],
            [
                'id' => 2,
                'product_code' => 'CI-HOOD-01',
                'name' => 'Stealth Ops Heavy Hoodie',
                'description' => '500 GSM heavy-fleece tactical hoodie with water-resistant DWR coating, magnetic storm pockets, and reinforced elbow guards.',
                'price' => 2900.00,
                'discount' => 0,
                'discount_price' => null,
                'quantity' => 2,
                'units_sold' => 39,
                'is_featured' => true,
                'is_popular' => true,
                'image' => 'images/hoodie.png',
                'category' => 'Tops',
            ],
            [
                'id' => 3,
                'product_code' => 'CI-BAG-02',
                'name' => 'Modular Deployment Bag',
                'description' => 'Weatherproof 1000D Cordura pack featuring tactical organizer dividers, padded laptop sleeve, and laser-cut attachment slots.',
                'price' => 2500.00,
                'discount' => 5,
                'discount_price' => 2375.00,
                'quantity' => 18,
                'units_sold' => 31,
                'is_featured' => false,
                'is_popular' => false,
                'image' => 'images/bag.png',
                'category' => 'Gear & Bags',
            ],
            [
                'id' => 4,
                'product_code' => 'CI-JACK-05',
                'name' => 'Armored All-Weather Jacket',
                'description' => '3-layer technical shell engineered for storm-grade wind and rain protection with taped seams and breathable armpit vents.',
                'price' => 3500.00,
                'discount' => 0,
                'discount_price' => null,
                'quantity' => 15,
                'units_sold' => 27,
                'is_featured' => true,
                'is_popular' => false,
                'image' => 'images/jacket.png',
                'category' => 'Outerwear',
            ],
            [
                'id' => 5,
                'product_code' => 'CI-CAP-03',
                'name' => 'Operator Tactical Cap',
                'description' => 'Ripstop low-profile cap with hook-and-loop patch panel, breathable mesh ventilation, and quick-dry sweatband.',
                'price' => 1200.00,
                'discount' => 0,
                'discount_price' => null,
                'quantity' => 0,
                'units_sold' => 22,
                'is_featured' => false,
                'is_popular' => false,
                'image' => 'images/cap.png',
                'category' => 'Headwear',
            ],
        ];
    }

    #[Route('/admin/products', name: 'admin_products')]
    public function adminProducts(): Response
    {
        return $this->render('admin/products.html.twig', [
            'current_page' => 'products',
            'products' => $this->getSampleProducts(),
        ]);
    }

    #[Route('/products', name: 'app_products')]
    public function catalog(): Response
    {
        return $this->render('product/index.html.twig', [
            'current_page' => 'gallery',
            'products' => $this->getSampleProducts(),
        ]);
    }

    #[Route('/products/{id<\d+>}', name: 'app_product_detail')]
    public function detail(int $id): Response
    {
        $products = $this->getSampleProducts();
        $product = null;
        foreach ($products as $p) {
            if ($p['id'] === $id) {
                $product = $p;
                break;
            }
        }

        if (!$product) {
            throw $this->createNotFoundException('Product not found.');
        }

        return $this->render('product/detail.html.twig', [
            'current_page' => 'product',
            'product' => $product,
            'related_products' => array_slice($products, 0, 3),
        ]);
    }
}
