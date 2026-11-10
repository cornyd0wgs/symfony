<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/users')]
#[IsGranted('ROLE_ADMIN')]
final class AdminUserController extends AbstractController
{
    #[Route('', name: 'admin_users_index', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $search = trim((string) $request->query->get('q', ''));
        $roleFilter = trim((string) $request->query->get('role', ''));

        $qb = $userRepository->createQueryBuilder('u')
            ->orderBy('u.id', 'DESC');

        if (!empty($search)) {
            $qb->andWhere('u.email LIKE :search OR u.firstname LIKE :search OR u.lastname LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        if (!empty($roleFilter)) {
            $qb->andWhere('u.roles LIKE :role')
               ->setParameter('role', '%' . $roleFilter . '%');
        }

        $users = $qb->getQuery()->getResult();

        return $this->render('admin/users/index.html.twig', [
            'current_page' => 'users',
            'users' => $users,
            'search' => $search,
            'role_filter' => $roleFilter,
        ]);
    }

    #[Route('/new', name: 'admin_users_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $user = new User();
        $user->setStatus('active');

        $form = $this->createForm(UserType::class, $user, [
            'is_edit' => false,
            'current_role' => 'ROLE_STAFF',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $roleChoice = $form->get('roleChoice')->getData() ?? 'ROLE_STAFF';
            $plainPassword = $form->get('plainPassword')->getData();

            $user->setRoles([$roleChoice]);
            $user->setCreatedat(new \DateTimeImmutable());

            if (!empty($plainPassword)) {
                $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', sprintf('User "%s" created successfully.', $user->getEmail()));

            return $this->redirectToRoute('admin_users_index');
        }

        return $this->render('admin/users/new.html.twig', [
            'current_page' => 'users',
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_users_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        User $user,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $currentRoles = $user->getRoles();
        $primaryRole = in_array('ROLE_ADMIN', $currentRoles, true)
            ? 'ROLE_ADMIN'
            : (in_array('ROLE_STAFF', $currentRoles, true) ? 'ROLE_STAFF' : 'ROLE_USER');

        $form = $this->createForm(UserType::class, $user, [
            'is_edit' => true,
            'current_role' => $primaryRole,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newRole = $form->get('roleChoice')->getData();
            if ($newRole) {
                $user->setRoles([$newRole]);
            }

            $plainPassword = $form->get('plainPassword')->getData();
            if (!empty($plainPassword)) {
                $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            }

            $entityManager->flush();

            $this->addFlash('success', sprintf('User "%s" updated successfully.', $user->getEmail()));

            return $this->redirectToRoute('admin_users_index');
        }

        return $this->render('admin/users/edit.html.twig', [
            'current_page' => 'users',
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/toggle-status', name: 'admin_users_toggle_status', methods: ['POST'])]
    public function toggleStatus(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->getUser() === $user) {
            $this->addFlash('error', 'You cannot disable your own administrator account.');
            return $this->redirectToRoute('admin_users_index');
        }

        if ($this->isCsrfTokenValid('toggle_status' . $user->getId(), $request->request->getString('_token'))) {
            $newStatus = ($user->getStatus() === 'active') ? 'disabled' : 'active';
            $user->setStatus($newStatus);
            $entityManager->flush();

            $this->addFlash('success', sprintf('User "%s" status set to %s.', $user->getEmail(), strtoupper($newStatus)));
        } else {
            $this->addFlash('error', 'Invalid security token.');
        }

        return $this->redirectToRoute('admin_users_index');
    }

    #[Route('/{id}/delete', name: 'admin_users_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->getUser() === $user) {
            $this->addFlash('error', 'You cannot delete your own logged-in account.');
            return $this->redirectToRoute('admin_users_index');
        }

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->getString('_token'))) {
            $userEmail = $user->getEmail();
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', sprintf('User account "%s" was successfully deleted.', $userEmail));
        } else {
            $this->addFlash('error', 'Invalid security token.');
        }

        return $this->redirectToRoute('admin_users_index');
    }
}
