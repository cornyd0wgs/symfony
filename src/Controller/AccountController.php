<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/account')]
final class AccountController extends AbstractController
{
    #[Route('', name: 'app_account_dashboard')]
    public function dashboard(SessionInterface $session): Response
    {
        $userName = $session->get('user_name', 'Operator Marcus');
        $userRole = $session->get('user_role', 'customer');

        $user = [
            'full_name' => $userName,
            'email' => 'operator.vance@tactical.io',
            'age' => 32,
            'created_at' => '2026-04-12 10:15:00',
        ];

        $orderCount = 4;
        $completedCount = 3;
        $totalSpent = 15910.00;
        $cartCount = 2;

        $recentOrders = [
            [
                'id' => 1042,
                'total_amount' => 6450.00,
                'status' => 'completed',
                'created_at' => '2026-10-06 08:30:00',
            ],
            [
                'id' => 1039,
                'total_amount' => 8150.00,
                'status' => 'completed',
                'created_at' => '2026-10-05 14:10:00',
            ],
            [
                'id' => 1035,
                'total_amount' => 1310.00,
                'status' => 'processing',
                'created_at' => '2026-09-22 11:20:00',
            ],
        ];

        return $this->render('account/dashboard.html.twig', [
            'current_page' => 'account',
            'account_tab' => 'overview',
            'user' => $user,
            'order_count' => $orderCount,
            'completed_count' => $completedCount,
            'total_spent' => $totalSpent,
            'cart_count' => $cartCount,
            'recent_orders' => $recentOrders,
        ]);
    }

    #[Route('/orders', name: 'app_account_orders')]
    public function orders(): Response
    {
        $orders = [
            [
                'id' => 1042,
                'recipient_name' => 'Marcus Vance',
                'contact_number' => '+63 917 555 0192',
                'delivery_address' => '404 Tactical Way, Sector 7',
                'city' => 'Taguig',
                'province' => 'Metro Manila',
                'postal_code' => '1634',
                'payment_method' => 'GCash',
                'gcash_reference' => 'GC-99382104',
                'total_amount' => 6450.00,
                'status' => 'completed',
                'created_at' => '2026-10-06 08:30:00',
            ],
            [
                'id' => 1039,
                'recipient_name' => 'Marcus Vance',
                'contact_number' => '+63 917 555 0192',
                'delivery_address' => '404 Tactical Way, Sector 7',
                'city' => 'Taguig',
                'province' => 'Metro Manila',
                'postal_code' => '1634',
                'payment_method' => 'Bank Transfer',
                'gcash_reference' => null,
                'total_amount' => 8150.00,
                'status' => 'completed',
                'created_at' => '2026-10-05 14:10:00',
            ],
            [
                'id' => 1035,
                'recipient_name' => 'Marcus Vance',
                'contact_number' => '+63 917 555 0192',
                'delivery_address' => '404 Tactical Way, Sector 7',
                'city' => 'Taguig',
                'province' => 'Metro Manila',
                'postal_code' => '1634',
                'payment_method' => 'Cash on Delivery',
                'gcash_reference' => null,
                'total_amount' => 1310.00,
                'status' => 'processing',
                'created_at' => '2026-09-22 11:20:00',
            ],
        ];

        return $this->render('account/orders.html.twig', [
            'current_page' => 'account',
            'account_tab' => 'orders',
            'orders' => $orders,
        ]);
    }

    #[Route('/profile', name: 'app_account_profile', methods: ['GET', 'POST'])]
    public function profile(Request $request, SessionInterface $session): Response
    {
        $userName = $session->get('user_name', 'Marcus Vance');
        $user = [
            'full_name' => $userName,
            'email' => 'operator.vance@tactical.io',
            'age' => 32,
        ];

        $errors = [];
        $success = null;

        if ($request->isMethod('POST')) {
            $user['full_name'] = trim((string) $request->request->get('full_name'));
            $user['email'] = trim((string) $request->request->get('email'));
            $user['age'] = (int) $request->request->get('age');
            $newPassword = (string) $request->request->get('new_password');
            $confirmPassword = (string) $request->request->get('confirm_password');

            if (empty($user['full_name'])) {
                $errors[] = 'Full name is required.';
            }
            if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Valid email is required.';
            }
            if (!empty($newPassword)) {
                if (strlen($newPassword) < 8) {
                    $errors[] = 'New password must be at least 8 characters long.';
                }
                if ($newPassword !== $confirmPassword) {
                    $errors[] = 'New passwords do not match.';
                }
            }

            if (empty($errors)) {
                $session->set('user_name', $user['full_name']);
                $success = 'Profile & security settings updated successfully.';
            }
        }

        return $this->render('account/profile.html.twig', [
            'current_page' => 'account',
            'account_tab' => 'profile',
            'user' => $user,
            'errors' => $errors,
            'success' => $success,
        ]);
    }
}
