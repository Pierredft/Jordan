<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\PromoCode;
use App\Security\EmailVerifier;
use App\Form\RegistrationFormType;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $user->setRoles(['ROLE_USER']);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $email = (new TemplatedEmail())
                ->from(new Address('pierredefauquet@gmail.com', 'JordanPierre'))
                ->to((string) $user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig');

            $logoPath = $this->getParameter('kernel.project_dir') . '/public/images/logo/logo.svg';
            if (file_exists($logoPath)) {
                $logo = new DataPart(new File($logoPath), 'logo', 'image/svg+xml');
                $logo->asInline();
                $logo->setContentId('logo@domain.com');
                $email->addPart($logo);
            }

            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user, $email);

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, EntityManagerInterface $em, MailerInterface $mailer): Response
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            /** @var User $user */
            $user = $this->getUser();
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        if ($user->isVerified()) {
            $promoCode = $em->getRepository(PromoCode::class)->findOneBy(['name' => 'welcome']);
            $user = $em->getRepository(User::class)->find($user->getId());
            if ($promoCode) {
                $email = (new TemplatedEmail())
                    ->from(new Address('pierredefauquet@gmail.com', 'JordanPierre'))
                    ->to((string) $user->getEmail())
                    ->subject('Bienvenue parmi nous !')
                    ->htmlTemplate('promo/welcome.html.twig')
                    ->context([
                        'promoCode' => $promoCode,
                        'user' => $user,
                        'userEmail' => $user->getEmail()
                    ]);

                $logoPath = $this->getParameter('kernel.project_dir') . '/public/images/logo/logo.svg';
                if (file_exists($logoPath)) {
                    $logo = new DataPart(new File($logoPath), 'logo', 'image/svg+xml');
                    $logo->asInline();
                    $logo->setContentId('logo@domain.com');
                    $email->addPart($logo);
                }

                $mailer->send($email);
            }

            // @TODO Change the redirect on success and handle or remove the flash message in your templates
            $this->addFlash('success', 'Your email address has been verified.');

            return $this->redirectToRoute('app_home');
        }

        // Ensure all code paths return a value
        return $this->redirectToRoute('app_register');
    }
}
