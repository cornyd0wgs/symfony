<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/staff')]
#[IsGranted('ROLE_STAFF')]
final class StaffController extends AbstractController
{
    #[Route('', name: 'staff_dashboard')]
    public function index(): Response
    {
        return $this->render('staff/dashboard.html.twig', [
            'current_page' => 'staff_dashboard',
        ]);
    }
}
