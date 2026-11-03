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
                $errors[] = 'Please enter your real name using letters only (no numbers or symbols).';
            }
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Please enter a valid email address.';
            }
            if (!is_numeric($data['age']) || (int)$data['age'] < 1 || (int)$data['age'] > 100) {
                $errors[] = 'Age must be a number between 1 and 100.';
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
                $parts = explode(' ', $data['full_name'], 2);
                $firstName = $parts[0];
                $lastName = $parts[1] ?? '';

                $user = new User();
                $user->setEmail($data['email'])
                    ->setFirstname($firstName)
                    ->setLastname($lastName)
                    ->setStatus('active')
                    ->setRoles(['ROLE_USER'])
                    ->setCreatedat(new \DateTimeImmutable());

                $hashedPassword = $passwordHasher->hashPassword($user, $password);
                $user->setPassword($hashedPassword);

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Account created successfully. Please log in.');
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
