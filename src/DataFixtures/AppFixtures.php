<?php

namespace App\DataFixtures;

use App\Entity\Gender;
use App\Entity\Items;
use App\Entity\Product;
use App\Entity\Style;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Store Gender entities for later reference
        $genderTitles = ['Homme', 'Femme', 'Mixte'];
        $genderEntities = [];
        foreach ($genderTitles as $title) {
            $gender = new Gender();
            $gender->setTitle($title);
            $manager->persist($gender);
            $genderEntities[] = $gender;
        }

        $itemsTitles = ['Chassures', 'Hauts et T-shirts Jordan', 'Shorts', 'Accessoires et équipement'];
        $itemsEntities = [];
        foreach ($itemsTitles as $title) {
            $items = new Items();
            $items->setNom($title);
            $manager->persist($items);
            $itemsEntities[] = $items;
        }

        $stylesTitles = ['LifeStyle', 'Football', 'Training', 'BasketBall', 'Golf', 'Danse'];
        $stylesEntities = [];
        foreach ($stylesTitles as $title) {
            $style = new Style();
            $style->setTitle($title);
            $manager->persist($style);
            $stylesEntities[] = $style;
        }

        for ($i=0; $i < 20; $i++) {
            $product = new Product();
            $product->setName("Jordan {$i}");
            $product->setPicture('https://static.nike.com/a/images/t_PDP_1728_v1/f_auto,q_auto:eco,u_126ab356-44d8-4a06-89b4-fcdcc8df0245,c_scale,fl_relative,w_1.0,h_1.0,fl_layer_apply/495387b7-b98a-41c9-a572-22a9abaa05f4/LAL+B+NK+STMNT+SWNGMN+JRSY.png');
            $product->setQuantity(rand(1, 100));
            $product->setPrice(rand(50, 200));

            // Définir si le produit est en solde (true/false)
            $isOnSale = rand(0, 1) === 1;
            $product->setSales($isOnSale);

            // Définir le prix de vente seulement si en solde
            $product->setSalePrice($isOnSale ? rand(30, 150) : null);

            $product->setDescription("Description for Jordan {$i}");
            $product->setGender($genderEntities[array_rand($genderEntities)]);
            $product->setItems($itemsEntities[array_rand($itemsEntities)]);
            $product->setStyle($stylesEntities[array_rand($stylesEntities)]);

            $manager->persist($product);
        }

        $manager->flush();
    }
}
