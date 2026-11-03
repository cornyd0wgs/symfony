<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // If user is already authenticated, redirect based on role
        if ($this->getUser()) {
            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('admin_dashboard');
            }
            return $this->redirectToRoute('app_account_dashboard');
        }

        // Get login error if any
        $error = $authenticationUtils->getLastAuthenticationError();
        // Last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'current_page' => 'login',
            'last_username' => $lastUsername,
            'error' => $error ? $error->getMessageKey() : null,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Intercepted by Symfony's firewall logout key
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_account_dashboard');
        }

        $errors = [];
        $data = [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'phone' => '',
        ];

        if ($request->isMethod('POST')) {
            $data['first_name'] = trim((string) $request->request->get('first_name'));
            $data['last_name'] = trim((string) $request->request->get('last_name'));
            $data['email'] = trim((string) $request->request->get('email'));
            $data['phone'] = trim((string) $request->request->get('phone'));
            $password = (string) $request->request->get('password');
            $confirmPassword = (string) $request->request->get('confirm_password');

            if (empty($data['first_name'])) {
                $errors[] = 'First name is required.';
            }
            if (empty($data['last_name'])) {
                $errors[] = 'Last name is required.';
            }
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Please provide a valid email address.';
            }
            if (strlen($password) < 8) {
                $errors[] = 'Password must be at least 8 characters long.';
            }
            if ($password !== $confirmPassword) {
                $errors[] = 'Passwords do not match.';
            }

            // Check if email already exists
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
            if ($existingUser) {
                $errors[] = 'An account with this email address already exists.';
            }

            if (empty($errors)) {
                $user = new User();
                $user->setEmail($data['email'])
                    ->setFirstname($data['first_name'])
                    ->setLastname($data['last_name'])
                    ->setPhone($data['phone'] ?: null)
                    ->setStatus('active')
                    ->setRoles(['ROLE_USER'])
                    ->setCreatedat(new \DateTimeImmutable());

                $hashedPassword = $passwordHasher->hashPassword($user, $password);
                $user->setPassword($hashedPassword);

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Tactical clearance issued. Account registered successfully. Please sign in.');
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('security/register.html.twig', [
            'current_page' => 'register',
            'errors' => $errors,
            'data' => $data,
        ]);
    }
}
