<?php

namespace App\Controller\Student;

use App\Entity\Booking;
use App\Entity\Notice;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class StudentDashboardController extends AbstractDashboardController
{
    /**
     * @Route("/student", name="student")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle("ECDL Dashboard<br>STUDENTE");
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Avvisi', 'fa fa-bullhorn', Notice::class);

        yield MenuItem::section('Gestione esami');
        //yield MenuItem::linktoRoute('Prenota esame', 'fa fa-edit', 'bookings');
        yield MenuItem::linkToCrud('Prenota esame', 'fa fa-edit', Booking::class);
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        /** @var User $user */
        return parent::configureUserMenu($user)
            ->setName($user->getFullName())
            ->setMenuItems([
                MenuItem::linktoRoute('Cambia password', 'fa fa-key', 'change_password'),
                MenuItem::linkToLogout('Sign Out', 'fa fa-sign-out')
            ]);
    }
}
