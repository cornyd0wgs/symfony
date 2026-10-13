<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(): Response
    {
        return $this->render('contact/index.html.twig', [
            'current_page' => 'contact',
            'coordinates' => '[ 14.5995° N // 120.9842° E ]',
            'support_channel' => 'SECURE_COMMS // PROTOCOL_09',
        ]);
    }
}
