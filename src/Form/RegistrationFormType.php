<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',EmailType::class,[
                'attr' =>[
                    'class' => 'form-control border-jordan color-jordan-form',
                    'minlength' => 2,
                    'maxLenght' => 180,
                    'autocomplete' => 'email',
                    'placeholder' => 'user@gmail.com',
                ],
                    'label' => 'Adresse e-mail :',
                    'label_attr' => [
                        'class' => 'form-label',
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Merci de saisir votre adresse e-mail',
                        ]),
                        new Length([
                            'max' => 180,
                            'maxMessage' => 'Votre adresse e-mail peut contenir maximum {{ limit }} caractéres',
                        ]),
                        new Assert\Email([
                            'message' => 'Merci de saisir une adresse e-mail valide',
                        ])
                    ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'attr' => [
                    'class' => 'form-check-input ms-3',
                ],
                'label' => 'J\'accepte les mentions légales',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les metions légales.',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class,[
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne correspondent pas',
                'first_options'=> [
                    'attr' => [
                        'class' => 'form-control border-jordan color-jordan-form',
                        'placeholder' => 'Mot de passe',
                    ],
                    'label'=> 'Mot de passe :',
                    'label_attr' => [
                        'class' => 'form-label',
                    ],
                    'constraints' => [
                        new Assert\NotBlank([
                            'message' => 'Merci de saisir un mot de passe',
                        ]),
                        new Assert\Length([
                            'min' => 6,
                            'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractéres',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                        new Assert\Regex([
                            'pattern' => '/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>_\-+=\/~`\[\];\'])[A-Za-z\d!@#$%^&*(),.?":{}|<>_\-+=\/~`\[\];\']{10,}$/',
                            'message' => 'Votre mot de passe doit contenir au moins 10 caractères, une majuscule, un chiffre, un caractère spécial parmi (!@#$%^&*(),.?":{}|<>_-+=/~\[];\'), et ne doit pas contenir d’espace.'
                        ]),
                    ],
                    'mapped' => false,
                ],
                'second_options' => [
                    'attr' => [
                        'class' => 'form-control border-jordan color-jordan-form',
                        'placeholder' => 'Confirmer le mot de passe',
                    ],
                    'label' => 'Confirmer le mot de passe :',
                    'label_attr' => [
                        'class' => 'form-label',
                    ],
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Merci de confirmer votre mot de passe',
                    ]),
                    new Assert\Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractéres',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>_\-+=\/~`\[\];\'])[A-Za-z\d!@#$%^&*(),.?":{}|<>_\-+=\/~`\[\];\']{10,}$/',
                        'message' => 'Votre mot de passe doit contenir au moins 10 caractères, une majuscule, un chiffre, un caractère spécial parmi (!@#$%^&*(),.?":{}|<>_-+=/~\[];\'), et ne doit pas contenir d’espace.'
                    ]),
                ],
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
