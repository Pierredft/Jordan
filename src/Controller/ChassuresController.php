<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ChassuresController extends AbstractController
{
    #[Route('/chaussures', name: 'app_chaussures')]
    public function index(): Response
    {
        return $this->render('chaussures/chaussures.html.twig', [
            'controller_name' => 'ChassuresController',
        ]);
    }
}
