<?php

namespace App\Controller\Student;

use App\Entity\Session;
use App\Entity\SkillCard;
use App\Entity\User;
use App\Repository\SessionRepository;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BookingsController extends AbstractController
{
    /**
     * @Route("/student/bookings", name="bookings")
     */
    public function form(AdminContext $adminContext)
    {
        /** @var SessionRepository */
        $repo = $this->getDoctrine()->getRepository(Session::class);
        /** @var User */
        $user = $adminContext->getUser();
        $student = $user->getStudent();
        foreach ($student->getValidSkillCard() as $skillCard) {
            /** @var SkillCard $skillCard */
            dump($repo->getAvailableSessionsForSkillCard($skillCard));
        }
        return $this->render('student/booking.html.twig');
    }
}