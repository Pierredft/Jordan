<?php

namespace App\Controller;

use App\Entity\UserNewsletter;
use App\Form\UserNewsletterType;
use App\Repository\UserNewsletterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user/newsletter')]
final class UserNewsletterController extends AbstractController
{
    #[Route('/subscribe', name: 'app_newsletter_subscribe', methods: ['POST'])]
    public function subscribe(Request $request, EntityManagerInterface $entityManager, UserNewsletterRepository $userNewsletterRepository): Response
    {
        // Vérification du token CSRF
        if (!$this->isCsrfTokenValid('newsletter_subscription', $request->request->get('_token'))) {
            $this->addFlash('error', 'Token de sécurité invalide.');
            return $this->redirectToRoute('app_home');
        }

        $email = $request->request->get('email');

        // Validation simple de l'email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('error', 'Veuillez saisir une adresse email valide.');
            return $this->redirectToRoute('app_home');
        }

        // Vérifier si l'email existe déjà
        $existingSubscription = $userNewsletterRepository->findOneBy(['email' => $email]);

        if ($existingSubscription) {
            $this->addFlash('warning', 'Cet email est déjà inscrit à notre newsletter.');
            return $this->redirectToRoute('app_home');
        }

        // Créer une nouvelle inscription
        $userNewsletter = new UserNewsletter();
        $userNewsletter->setEmail($email);

        try {
            $entityManager->persist($userNewsletter);
            $entityManager->flush();

            $this->addFlash('success', 'Merci ! Vous êtes maintenant inscrit(e) à notre newsletter.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue. Veuillez réessayer.');
        }

        return $this->redirectToRoute('app_home');
    }
}
