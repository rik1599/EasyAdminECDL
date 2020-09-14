<?php

namespace App\Controller\Admin;

use App\Entity\Certification;
use App\Entity\Module;
use App\Entity\Notice;
use App\Entity\Student;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminDashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {   
        return $this->render('dashboardAdminHome.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle("ICDL Dashboard<br>ADMIN");
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Avvisi', 'fa fa-bullhorn', Notice::class);
        
        yield MenuItem::section('Gestione Corsi ICDL');
        yield MenuItem::linkToCrud('Moduli', 'fa fa-tags', Module::class);
        yield MenuItem::linkToCrud('Certificazioni', 'fa fa-chalkboard', Certification::class);
        
        yield MenuItem::section("Gestione utenti");
        yield MenuItem::linkToCrud("Utenti", "fa fa-users", User::class);
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {   
        /** @var User $user */
        return parent::configureUserMenu($user)
            ->setName($user->getFullName())
            ->setMenuItems([
                MenuItem::linktoRoute('Cambia password', 'fa fa-user', 'change_password'),
                MenuItem::linkToLogout('Sign Out', 'fa fa-sign-out')
            ]);
    }
}
