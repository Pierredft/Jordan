<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ChassuresController extends AbstractController
{
    #[Route('/chassures', name: 'app_chassures')]
    public function index(): Response
    {
        return $this->render('chassures/chassures.html.twig', [
            'controller_name' => 'ChassuresController',
        ]);
    }
}
