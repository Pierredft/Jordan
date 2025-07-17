<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Product;
use App\Repository\CartRepository;
use App\Repository\PromoCodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/cart')]
#[IsGranted('ROLE_USER')]
class CartController extends AbstractController
{
    #[Route('/add/{id}', name: 'app_cart_add', methods: ['POST'])]
    public function add(Product $product, Request $request, EntityManagerInterface $entityManager, CartRepository $cartRepository): Response {
        $user = $this->getUser();
        $quantity = (int) $request->request->get('quantity', 1);

        // Vérifier si le produit a suffisamment de stock
        if ($product->getQuantity() < $quantity) {
            $this->addFlash('error', 'Stock insuffisant pour ce produit.');
            return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
        }

        // Vérifier si l'article est déjà dans le panier
        $existingCartItem = $cartRepository->findCartItem($user, $product);

        if ($existingCartItem) {
            // Vérifier si la nouvelle quantité totale ne dépasse pas le stock
            $newTotalQuantity = $existingCartItem->getQuantity() + $quantity;
            if ($product->getQuantity() < $newTotalQuantity) {
                $this->addFlash('error', 'Stock insuffisant pour cette quantité.');
                return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
            }

            $existingCartItem->setQuantity($newTotalQuantity);
        } else {
            // Créer un nouvel item dans le panier
            $cartItem = new Cart();
            $cartItem->setUser($user)
                ->setProduct($product)
                ->setQuantity($quantity);

            $entityManager->persist($cartItem);
        }

        // Décrémenter la quantité du produit
        $product->setQuantity($product->getQuantity() - $quantity);

        $entityManager->flush();

        $this->addFlash('success', 'Produit ajouté au panier avec succès!');

        return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
    }

    #[Route('/', name: 'app_cart')]
    public function index(CartRepository $cartRepository, SessionInterface $session): Response
    {
        $user = $this->getUser();
        $cartItems = $cartRepository->findCartByUser($user);

        $subtotal = 0;
        foreach ($cartItems as $item) {
            // Utiliser la même logique que dans le template
            $price = $item->getProduct()->sales == 1 ?
                $item->getProduct()->getSalePrice() :
                $item->getProduct()->getPrice();
            $subtotal += $price * $item->getQuantity();
        }

        // Récupérer le code promo de la session
        $appliedPromoCode = $session->get('applied_promo_code');
        $discount = 0;
        $total = $subtotal;

        if ($appliedPromoCode) {
            $discount = ($subtotal * $appliedPromoCode['discount']) / 100;
            $total = $subtotal - $discount;
        }

        return $this->render('cart/index.html.twig', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'total' => $total,
            'appliedPromoCode' => $appliedPromoCode,
            'discount' => $discount,
        ]);
    }

    #[Route('/update/{id}', name: 'app_cart_update', methods: ['POST'])]
    public function update(Cart $cartItem, Request $request, EntityManagerInterface $entityManager): Response
    {
        $newQuantity = (int) $request->request->get('quantity', 1);
        $oldQuantity = $cartItem->getQuantity();
        $product = $cartItem->getProduct();

        // Calculer la différence de quantité
        $quantityDifference = $newQuantity - $oldQuantity;

        // Si on augmente la quantité, vérifier le stock disponible
        if ($quantityDifference > 0) {
            if ($product->getQuantity() < $quantityDifference) {
                $this->addFlash('error', 'Stock insuffisant pour cette quantité.');
                return $this->redirectToRoute('app_cart');
            }
            // Décrémenter le stock
            $product->setQuantity($product->getQuantity() - $quantityDifference);
        }
        // Si on diminue la quantité, remettre en stock
        else if ($quantityDifference < 0) {
            $product->setQuantity($product->getQuantity() + abs($quantityDifference));
        }

        // Mettre à jour la quantité dans le panier
        $cartItem->setQuantity($newQuantity);

        $entityManager->flush();

        $this->addFlash('success', 'Quantité mise à jour avec succès!');

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/remove/{id}', name: 'app_cart_remove', methods: ['POST'])]
    public function remove(Cart $cartItem, EntityManagerInterface $entityManager): Response
    {
        // Remettre la quantité en stock
        $product = $cartItem->getProduct();
        $product->setQuantity($product->getQuantity() + $cartItem->getQuantity());

        $entityManager->remove($cartItem);
        $entityManager->flush();

        $this->addFlash('success', 'Produit retiré du panier.');

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/apply-promo', name: 'app_cart_apply_promo', methods: ['POST'])]
    public function applyPromoCode(Request $request, PromoCodeRepository $promoCodeRepository, SessionInterface $session): Response
    {
        $promoCodeValue = $request->request->get('promo_code');

        if (empty($promoCodeValue)) {
            $this->addFlash('error', 'Veuillez entrer un code promo.');
            return $this->redirectToRoute('app_cart');
        }

        $promoCode = $promoCodeRepository->findOneByCode($promoCodeValue);

        if (!$promoCode) {
            $this->addFlash('error', 'Code promo invalide.');
            return $this->redirectToRoute('app_cart');
        }

        // Stocker les informations du code promo en session
        $session->set('applied_promo_code', [
            'code' => $promoCode->getCode(),
            'name' => $promoCode->getName(),
            'discount' => $promoCode->getDiscount()
        ]);

        $this->addFlash('success', 'Code promo "' . $promoCode->getCode() . '" appliqué avec succès ! Remise de ' . $promoCode->getDiscount() . '%.');

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/remove-promo', name: 'app_cart_remove_promo', methods: ['POST'])]
    public function removePromoCode(SessionInterface $session): Response
    {
        $session->remove('applied_promo_code');
        $this->addFlash('success', 'Code promo supprimé.');

        return $this->redirectToRoute('app_cart');
    }
}
