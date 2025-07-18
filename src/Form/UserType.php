<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'attr' => [
                    'class' => 'form-control border-jordan color-jordan-form',
                    'minlength' => 2,
                    'maxlength' => 180,
                    'autocomplete' => 'name',
                    'placeholder' => 'Nom',
                ],
                'label' => 'Nom :',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir votre nom',
                    ]),
                    new Length([
                        'max' => 180,
                        'maxMessage' => 'Votre nom peut contenir maximum {{ limit }} caractéres',
                    ]),
                ],
            ])
            ->add('firstName', TextType::class, [
                'attr' => [
                    'class' => 'form-control border-jordan color-jordan-form',
                    'minlength' => 2,
                    'maxlength' => 180,
                    'autocomplete' => 'given-name',
                    'placeholder' => 'Prénom',
                ],
                'label' => 'Prénom :',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir votre prénom',
                    ]),
                    new Length([
                        'max' => 180,
                        'maxMessage' => 'Votre prénom peut contenir maximum {{ limit }} caractéres',
                    ]),
                ],
            ])
            ->add('phoneCode', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, [
                'choices' => [
                    '+33 (France)' => '+33',
                    '+32 (Belgique)' => '+32',
                    '+41 (Suisse)' => '+41',
                    '+49 (Allemagne)' => '+49',
                    '+44 (Royaume-Uni)' => '+44',
                    '+34 (Espagne)' => '+34',
                    '+39 (Italie)' => '+39',
                    '+1 (États-Unis/Canada)' => '+1',
                    // Ajoutez d'autres codes pays si besoin
                ],
                'attr' => [
                    'class' => 'form-control border-jordan color-jordan-form',
                    'autocomplete' => 'tel-country-code',
                ],
                'label' => false,
                'row_attr' => [
                    'class' => 'phone-group-start',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de sélectionner votre code pays',
                    ]),
                ],
                'placeholder' => 'Code pays',
            ])
            ->add('phoneNumber', TextType::class, [
                'attr' => [
                    'class' => 'form-control border-jordan color-jordan-form',
                    'minlength' => 8,
                    'maxlength' => 15,
                    'autocomplete' => 'tel',
                    'placeholder' => 'Numéro de téléphone',
                ],
                'label' => 'Numéro de téléphone :',
                'label_attr' => [
                    'class' => 'form-label phone-group-label',
                ],
                'row_attr' => [
                    'class' => 'phone-group-end',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir votre numéro de téléphone',
                    ]),
                    new Length([
                        'max' => 15,
                        'maxMessage' => 'Votre numéro de téléphone peut contenir maximum {{ limit }} caractéres',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
