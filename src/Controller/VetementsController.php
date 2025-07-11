<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class VetementsController extends AbstractController
{
    #[Route('/vetements', name: 'app_vetements')]
    public function index(): Response
    {
        return $this->render('vetements/chaussures.html.twig', [
            'controller_name' => 'VetementsController',
        ]);
    }
}
