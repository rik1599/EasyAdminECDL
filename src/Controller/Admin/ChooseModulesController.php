<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Crud\SkillCardCrudController;
use App\Entity\CertificationModule;
use App\Entity\Module;
use App\Entity\SkillCard;
use App\Repository\ModuleRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ChooseModulesController extends AbstractController
{
    const fieldName = 'chosenModules';
    /** @var CrudUrlGenerator */
    private $crudUrlGenerator;

    public function __construct(CrudUrlGenerator $crudUrlGenerator)
    {
        $this->crudUrlGenerator = $crudUrlGenerator;
    }

    /**
     * @Route("/admin/choose-modules/{id}", name="choose_modules", requirements={"page"="\d+"})
     */
    public function chooseModules(Request $request, int $id, AdminContext $adminContext)
    {
        /** @var SkillCard */
        $skillCard = $this->getDoctrine()->getRepository(SkillCard::class)->find($id);
        /** @var ModuleRepository */
        $repo = $this->getDoctrine()->getRepository(Module::class);
        $choices = $repo->findNotMandatoryModules($skillCard->getCertification());
        $skillCardIndexUrl = $this->crudUrlGenerator->build()
            ->setController(SkillCardCrudController::class)
            ->setAction(Crud::PAGE_INDEX)
            ->generateUrl();

        if (sizeof($choices) == 0) {
            $this->addFlash('danger', 'La certificazione di questa Skillcard non prevede esami a scelta');
            return $this->redirect($skillCardIndexUrl);
        }

        $form = $this->createModulesForm($skillCard, $choices);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Esami a scelta impostati correttamente');
            return $this->redirect($skillCardIndexUrl);
        }

        return $this->render('customForm.html.twig', [
            'ea' => $adminContext,
            'form' => $form->createView(),
        ]);
    }

    protected function createModulesForm(SkillCard $skillCard, array $choices)
    {   
        return $this->createFormBuilder($skillCard)
            ->add(self::fieldName, EntityType::class, [
                'class' => Module::class,
                'choices' => $choices,
                'choice_label' => 'nome',
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('save', SubmitType::class)
            ->getForm();
    }
}