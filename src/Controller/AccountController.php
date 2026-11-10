<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/account')]
#[IsGranted('ROLE_USER')]
final class AccountController extends AbstractController
{
    #[Route('', name: 'app_account_dashboard')]
    public function dashboard(): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $userName = $currentUser->getFirstname() . ' ' . $currentUser->getLastname();
        $userEmail = $currentUser->getEmail();

        $user = [
            'full_name' => $userName,
            'email' => $userEmail,
            'age' => 32,
            'created_at' => $currentUser->getCreatedat() ? $currentUser->getCreatedat()->format('Y-m-d H:i:s') : '2026-04-12 10:15:00',
        ];

        $orderCount = count($currentUser->getOrders());
        $completedCount = 0;
        foreach ($currentUser->getOrders() as $order) {
            if ($order->getStatus() === 'completed') {
                $completedCount++;
            }
        }
        $totalSpent = 0.00;
        $cartCount = count($currentUser->getCarts());

        $recentOrders = [];
        foreach ($currentUser->getOrders() as $o) {
            $recentOrders[] = [
                'id' => $o->getId(),
                'total_amount' => (float) $o->getTotalAmount(),
                'status' => $o->getStatus(),
                'created_at' => $o->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        }

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
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $orders = [];
        foreach ($currentUser->getOrders() as $order) {
            $orders[] = [
                'id' => $order->getId(),
                'recipient_name' => $order->getShippingRecipient(),
                'contact_number' => $order->getShppingPhone() ?? '—',
                'delivery_address' => $order->getShippingAddress(),
                'city' => $order->getShippingCity(),
                'province' => $order->getShippingProvince(),
                'postal_code' => $order->getShippingPostalCode(),
                'payment_method' => 'Standard',
                'gcash_reference' => null,
                'total_amount' => (float) $order->getTotalAmount(),
                'status' => $order->getStatus(),
                'created_at' => $order->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        }

        return $this->render('account/orders.html.twig', [
            'current_page' => 'account',
            'account_tab' => 'orders',
            'orders' => $orders,
        ]);
    }

    #[Route('/profile', name: 'app_account_profile', methods: ['GET', 'POST'])]
    public function profile(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $user = [
            'full_name' => trim($currentUser->getFirstname() . ' ' . $currentUser->getLastname()),
            'email' => $currentUser->getEmail(),
            'age' => 32,
        ];

        $errors = [];
        $success = null;

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('account_profile', (string) $request->request->get('_csrf_token'))) {
                $errors[] = 'Invalid or expired security token.';
            }

            $fullName = trim((string) $request->request->get('full_name'));
            $email = trim((string) $request->request->get('email'));
            $age = (int) $request->request->get('age');
            $newPassword = (string) $request->request->get('new_password');
            $confirmPassword = (string) $request->request->get('confirm_password');

            $user['full_name'] = $fullName;
            $user['email'] = $email;
            $user['age'] = $age;

            if (empty($fullName)) {
                $errors[] = 'Full name is required.';
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Valid email is required.';
            }

            // Check if email changed and if another user has it
            if ($email !== $currentUser->getEmail()) {
                $existing = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
                if ($existing && $existing->getId() !== $currentUser->getId()) {
                    $errors[] = 'This email is already in use by another account.';
                }
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
                $parts = explode(' ', $fullName, 2);
                $currentUser->setFirstname($parts[0]);
                $currentUser->setLastname($parts[1] ?? '');
                $currentUser->setEmail($email);

                // Live password hashing into the database
                if (!empty($newPassword)) {
                    $hashed = $passwordHasher->hashPassword($currentUser, $newPassword);
                    $currentUser->setPassword($hashed);
                }

                $entityManager->flush();

                $success = 'Profile & security credentials updated successfully.';
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
