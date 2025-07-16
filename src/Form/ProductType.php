<?php

namespace App\Form;

use App\Entity\Gender;
use App\Entity\Items;
use App\Entity\Product;
use App\Entity\Style;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('picture')
            ->add('quantity')
            ->add('sales')
            ->add('description')
            ->add('price')
            ->add('salePrice')
            ->add('gender', EntityType::class, [
                'class' => Gender::class,
                'choice_label' => 'id',
            ])
            ->add('items', EntityType::class, [
                'class' => Items::class,
                'choice_label' => 'id',
            ])
            ->add('style', EntityType::class, [
                'class' => Style::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
