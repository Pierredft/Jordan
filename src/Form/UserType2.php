<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class UserType2 extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('houseNumber', IntegerType::class, [
                'attr' => [
                    'min' => 1,
                    'max' => 9999,
                    'placeholder' => 'Numéro de maison',
                ],
                'label' => 'Numéro de maison',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir le numéro de votre maison',
                    ]),
                    new Length([
                        'max' => 4,
                        'maxMessage' => 'Le numéro de maison peut contenir maximum {{ limit }} chiffres',
                    ]),
                ],
            ])
            ->add('extension', TextType::class, [
                'label' => 'Extension',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'required' => false,
                'attr' => [
                    'placeholder' => 'Extension (optionnel)',
                ],
            ])
            ->add('street', TextType::class, [
                'label' => 'Rue',
                'label_attr' => [
                    'class' => 'form-label',
                ],
            ])
            ->add('placeCalled', TextType::class, [
                'label' => 'Lieu-dit',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'required' => false,
                'attr' => [
                    'placeholder' => 'Lieu-dit (optionnel)',
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'label_attr' => [
                    'class' => 'form-label',
                ],
            ])
            ->add('zipCode', TextType::class, [
                'label' => 'Code postal',
                'label_attr' => [
                    'class' => 'form-label',
                ],
            ])
            ->add('country', TextType::class, [
                'label' => 'Pays',
                'label_attr' => [
                    'class' => 'form-label',
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
