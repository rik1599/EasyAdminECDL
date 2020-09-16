<?php

namespace App\Controller\Student;

use App\Entity\Notice;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
            ->setTitle("ECDL Dashboard\nStudente");
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linktoRoute('Cambia password', 'fa fa-user', 'change_password');
        yield MenuItem::linkToCrud('Avvisi', 'fa fa-bullhorn', Notice::class);
    }
}
