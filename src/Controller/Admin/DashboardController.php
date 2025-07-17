<?php

namespace App\Controller\Admin;

use App\Entity\Gender;
use App\Entity\Items;
use App\Entity\Product;
use App\Entity\Style;
use App\Entity\PromoCode;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        $url = $routeBuilder->setController(GenderCrudController::class)->generateUrl();

        return $this->redirect($url);

    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Jordan');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Retour sur le site', 'fa fa-home', 'app_home');
        yield MenuItem::linkToCrud('Genre', 'fas fa-venus-mars', Gender::class);
        yield MenuItem::linkToCrud('Style', 'fas fa-vest', Style::class);
        yield MenuItem::linkToCrud('Produit', 'fas fa-shoe-prints', Product::class);
        yield MenuItem::linkToCrud('Articles', 'fas fa-newspaper', Items::class);
        yield MenuItem::linkToCrud('Codes Promo', 'fas fa-tags', PromoCode::class);
    }
}
