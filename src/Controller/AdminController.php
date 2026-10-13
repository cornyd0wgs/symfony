<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
final class AdminController extends AbstractController
{
    #[Route('', name: 'admin_dashboard')]
    public function index(): Response
    {
        // Mock data reflecting the original dashboard structure
        $metrics = [
            'total_orders' => 124,
            'today_orders' => 8,
            'total_revenue' => 482500.00,
            'today_revenue' => 31200.00,
            'customer_count' => 86,
            'low_stock_count' => 3,
            'out_of_stock_count' => 1,
            'average_order_value' => 3891.12,
        ];

        $statusCounts = [
            'completed' => 84,
            'processing' => 22,
            'pending' => 14,
            'cancelled' => 4,
        ];

        $totalStatus = max(1, array_sum($statusCounts));
        $completedDeg = ($statusCounts['completed'] / $totalStatus) * 360;
        $processingDeg = $completedDeg + (($statusCounts['processing'] / $totalStatus) * 360);
        $pendingDeg = $processingDeg + (($statusCounts['pending'] / $totalStatus) * 360);

        $chartPoints = [
            ['date' => 'Oct 1', 'revenue' => 35000, 'x' => 24.0, 'y' => 160.0],
            ['date' => 'Oct 2', 'revenue' => 42000, 'x' => 132.6, 'y' => 140.0],
            ['date' => 'Oct 3', 'revenue' => 28000, 'x' => 241.3, 'y' => 180.0],
            ['date' => 'Oct 4', 'revenue' => 61000, 'x' => 350.0, 'y' => 85.0],
            ['date' => 'Oct 5', 'revenue' => 54000, 'x' => 458.6, 'y' => 105.0],
            ['date' => 'Oct 6', 'revenue' => 78000, 'x' => 567.3, 'y' => 35.0],
            ['date' => 'Oct 7', 'revenue' => 31200, 'x' => 676.0, 'y' => 170.0],
        ];

        $svgPoints = implode(' ', array_map(fn($p) => "{$p['x']},{$p['y']}", $chartPoints));

        $recentOrders = [
            [
                'id' => 1042,
                'customer' => 'Marcus Vance',
                'date' => 'Oct 6, 2026',
                'amount' => 6450.00,
                'status' => 'completed',
            ],
            [
                'id' => 1041,
                'customer' => 'Elena Rostova',
                'date' => 'Oct 6, 2026',
                'amount' => 12900.00,
                'status' => 'processing',
            ],
            [
                'id' => 1040,
                'customer' => 'David Kim',
                'date' => 'Oct 5, 2026',
                'amount' => 4200.00,
                'status' => 'pending',
            ],
            [
                'id' => 1039,
                'customer' => 'Sarah Jenkins',
                'date' => 'Oct 5, 2026',
                'amount' => 8150.00,
                'status' => 'completed',
            ],
            [
                'id' => 1038,
                'customer' => 'Rafael Moreno',
                'date' => 'Oct 4, 2026',
                'amount' => 3100.00,
                'status' => 'cancelled',
            ],
        ];

        $topProducts = [
            [
                'name' => 'Tactical Recon Vest MK-IV',
                'image' => '/images/tactical jacket.png',
                'units_sold' => 48,
                'revenue' => 187200.00,
            ],
            [
                'name' => 'Stealth Ops Heavy Hoodie',
                'image' => '/images/hoodie.png',
                'units_sold' => 39,
                'revenue' => 113100.00,
            ],
            [
                'name' => 'Modular Deployment Bag',
                'image' => '/images/bag.png',
                'units_sold' => 31,
                'revenue' => 77500.00,
            ],
            [
                'name' => 'Armored All-Weather Jacket',
                'image' => '/images/jacket.png',
                'units_sold' => 27,
                'revenue' => 94500.00,
            ],
            [
                'name' => 'Operator Tactical Cap',
                'image' => '/images/cap.png',
                'units_sold' => 22,
                'revenue' => 26400.00,
            ],
        ];

        $stockWatch = [
            [
                'code' => 'CI-HOOD-01',
                'name' => 'Stealth Ops Heavy Hoodie',
                'image' => '/images/hoodie.png',
                'quantity' => 2,
                'status' => 'LOW STOCK',
                'status_class' => 'stock-low',
            ],
            [
                'code' => 'CI-CAP-03',
                'name' => 'Operator Tactical Cap',
                'image' => '/images/cap.png',
                'quantity' => 0,
                'status' => 'OUT OF STOCK',
                'status_class' => 'stock-zero',
            ],
            [
                'code' => 'CI-VEST-04',
                'name' => 'Tactical Recon Vest MK-IV',
                'image' => '/images/tactical jacket.png',
                'quantity' => 4,
                'status' => 'LOW STOCK',
                'status_class' => 'stock-low',
            ],
            [
                'code' => 'CI-BAG-02',
                'name' => 'Modular Deployment Bag',
                'image' => '/images/bag.png',
                'quantity' => 18,
                'status' => 'IN STOCK',
                'status_class' => 'stock-ok',
            ],
        ];

        $recentReviews = [
            [
                'customer' => 'Carlos Mendoza',
                'product' => 'Tactical Recon Vest MK-IV',
                'rating' => 5,
                'comment' => 'Exceptional build quality and stitching. Plate pockets fit securely with zero bounce.',
                'date' => 'Oct 5, 2026',
            ],
            [
                'customer' => 'Liam Bennett',
                'product' => 'Armored All-Weather Jacket',
                'rating' => 5,
                'comment' => 'Water repellent finish held up under intense rain. Highly recommended.',
                'date' => 'Oct 4, 2026',
            ],
            [
                'customer' => 'Alex Thorne',
                'product' => 'Modular Deployment Bag',
                'rating' => 4,
                'comment' => 'Great organization and compartment layout. Rugged zippers.',
                'date' => 'Oct 3, 2026',
            ],
        ];

        return $this->render('admin/dashboard.html.twig', [
            'current_page' => 'dashboard',
            'metrics' => $metrics,
            'status_counts' => $statusCounts,
            'status_total' => $totalStatus,
            'completed_deg' => $completedDeg,
            'processing_deg' => $processingDeg,
            'pending_deg' => $pendingDeg,
            'chart_points' => $chartPoints,
            'svg_points' => $svgPoints,
            'recent_orders' => $recentOrders,
            'top_products' => $topProducts,
            'stock_watch' => $stockWatch,
            'recent_reviews' => $recentReviews,
        ]);
    }

    #[Route('/products', name: 'admin_products')]
    public function products(): Response
    {
        $products = [
            [
                'id' => 1,
                'product_code' => 'CI-VEST-04',
                'name' => 'Tactical Recon Vest MK-IV',
                'price' => 3900.00,
                'discount' => 10,
                'quantity' => 4,
                'units_sold' => 48,
                'is_featured' => true,
                'is_popular' => true,
                'image' => '/images/tactical jacket.png',
            ],
            [
                'id' => 2,
                'product_code' => 'CI-HOOD-01',
                'name' => 'Stealth Ops Heavy Hoodie',
                'price' => 2900.00,
                'discount' => 0,
                'quantity' => 2,
                'units_sold' => 39,
                'is_featured' => true,
                'is_popular' => true,
                'image' => '/images/hoodie.png',
            ],
            [
                'id' => 3,
                'product_code' => 'CI-BAG-02',
                'name' => 'Modular Deployment Bag',
                'price' => 2500.00,
                'discount' => 5,
                'quantity' => 18,
                'units_sold' => 31,
                'is_featured' => false,
                'is_popular' => false,
                'image' => '/images/bag.png',
            ],
            [
                'id' => 4,
                'product_code' => 'CI-JACK-05',
                'name' => 'Armored All-Weather Jacket',
                'price' => 3500.00,
                'discount' => 0,
                'quantity' => 15,
                'units_sold' => 27,
                'is_featured' => true,
                'is_popular' => false,
                'image' => '/images/jacket.png',
            ],
            [
                'id' => 5,
                'product_code' => 'CI-CAP-03',
                'name' => 'Operator Tactical Cap',
                'price' => 1200.00,
                'discount' => 0,
                'quantity' => 0,
                'units_sold' => 22,
                'is_featured' => false,
                'is_popular' => false,
                'image' => '/images/cap.png',
            ],
        ];

        return $this->render('admin/products.html.twig', [
            'current_page' => 'products',
            'products' => $products,
        ]);
    }

    #[Route('/orders', name: 'admin_orders')]
    public function orders(): Response
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

    #[Route('/customers', name: 'admin_customers')]
    public function customers(): Response
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

    #[Route('/reviews', name: 'admin_reviews')]
    public function reviews(): Response
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

    #[Route('/settings', name: 'admin_settings')]
    public function settings(): Response
    {
        return $this->render('admin/settings.html.twig', [
            'current_page' => 'settings',
        ]);
    }
}
