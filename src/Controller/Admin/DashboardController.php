<?php

namespace App\Controller\Admin;

use App\Entity\Collaboration;
use App\Entity\Item;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $routeBuilder = $this->get(AdminUrlGenerator::class);

        return $this->redirect($routeBuilder->setController(ItemCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Clotilde Ecommerce')
            ->setFaviconPath('img/favicon.ico')
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Home', 'fa fa-home', 'homepage_index');
        yield MenuItem::linkToCrud('Items', 'fas fa-cart-plus', Item::class);
        yield MenuItem::linkToCrud('Collaborations', 'fas fa-file-alt', Collaboration::class);
        yield MenuItem::linkToLogout('Logout', 'fas fa-sign-out-alt');
    }
}
