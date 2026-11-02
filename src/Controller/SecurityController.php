<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(Request $request, SessionInterface $session): Response
    {
        $error = null;
        $email = '';

        if ($request->isMethod('POST')) {
            $email = trim((string) $request->request->get('email'));
            $password = (string) $request->request->get('password');

            if ($email === '' || $password === '') {
                $error = 'Please enter both email and password.';
            } else {
                // Check demo credentials or accept simulated login
                if ($email === 'admin@caballeroindustries.com' && $password === 'Admin123!') {
                    $session->set('user_id', 1);
                    $session->set('user_name', 'Site Admin');
                    $session->set('user_role', 'admin');
                    $this->addFlash('success', 'Logged in as Administrator.');
                    return $this->redirectToRoute('admin_dashboard');
                } else {
                    // Regular customer session login simulation
                    $session->set('user_id', 2);
                    $session->set('user_name', strstr($email, '@', true) ?: 'Tactical Operator');
                    $session->set('user_role', 'customer');
                    $this->addFlash('success', 'Authentication verified. Welcome to Caballero Industries.');
                    return $this->redirectToRoute('app_account_dashboard');
                }
            }
        }

        return $this->render('security/login.html.twig', [
            'current_page' => 'login',
            'last_username' => $email,
            'error' => $error,
        ]);
    }

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request, SessionInterface $session): Response
    {
        $errors = [];
        $data = [
            'full_name' => '',
            'email' => '',
            'age' => '',
        ];

        if ($request->isMethod('POST')) {
            $data['full_name'] = trim((string) $request->request->get('full_name'));
            $data['email'] = trim((string) $request->request->get('email'));
            $data['age'] = trim((string) $request->request->get('age'));
            $password = (string) $request->request->get('password');
            $confirmPassword = (string) $request->request->get('confirm_password');

            if (empty($data['full_name'])) {
                $errors[] = 'Please enter your full name.';
            }
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Please provide a valid email address.';
            }
            if (!is_numeric($data['age']) || (int) $data['age'] < 1 || (int) $data['age'] > 120) {
                $errors[] = 'Age must be between 1 and 120.';
            }
            if (strlen($password) < 8) {
                $errors[] = 'Password must be at least 8 characters.';
            }
            if ($password !== $confirmPassword) {
                $errors[] = 'Passwords do not match.';
            }

            if (empty($errors)) {
                $session->set('user_id', 10);
                $session->set('user_name', $data['full_name']);
                $session->set('user_role', 'customer');
                $this->addFlash('success', 'Account created successfully. Welcome to Caballero Industries.');
                return $this->redirectToRoute('app_account_dashboard');
            }
        }

        return $this->render('security/register.html.twig', [
            'current_page' => 'register',
            'errors' => $errors,
            'data' => $data,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(SessionInterface $session): Response
    {
        $session->clear();
        $this->addFlash('info', 'Secure session terminated.');
        return $this->redirectToRoute('app_home');
    }
}
