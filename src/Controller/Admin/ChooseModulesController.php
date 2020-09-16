<?php

namespace App\Controller\Admin;

use App\Entity\SkillCard;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ChooseModulesController extends AbstractController
{
    /**
     * @Route("/admin/choose-modules/{id}", name="choose_modules", requirements={"page"="\d+"})
     */
    public function chooseModules(AdminContext $adminContext, Request $request, int $id)
    {
        /** @var SkillCard */
        $skillCard = $this->getDoctrine()->getRepository(SkillCard::class)->find($id);
        return $this->render('chooseModules.html.twig', [
            'ea' => $adminContext
        ]);
    }
}