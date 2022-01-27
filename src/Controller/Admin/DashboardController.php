<?php

namespace App\Controller\Admin;

use App\Entity\Outing;
use App\Entity\Place;
use App\Entity\Town;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Sortir (c\'est juste le nom du site, ça ne fait pas sortir XD ) ');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Retour au site', 'fa fa-globe-europe', 'temporary');
        yield MenuItem::linkToDashboard('Accueil admin', 'fa fa-home');
        yield MenuItem::linkToCrud('Participant', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('Lieux', 'fas fa-map-marked-alt', Place::class);
        yield MenuItem::linkToCrud('Villes', 'fas fa-place-of-worship', Town::class);
        yield MenuItem::linkToCrud('Sorties', 'fas fa-route', Outing::class);
    }
}
