<?php

namespace App\Controller\Admin\Crud;

use App\Entity\Module;
use App\Entity\SkillCard;
use App\Entity\SkillCardModules;
use App\Repository\ModuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class SkillCardCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SkillCard::class;
    }

    public function configureActions(Actions $actions): Actions
    {   
        $modules = Action::new('Scegli esami')
            ->linkToCrudAction('chooseModules');

        $update = Action::new('Rinnova')
            ->displayIf(static function (SkillCard $skillCard) {
                return !is_null($skillCard->getCertification()->getUpdateCertification());
            })
            ->linkToCrudAction('renovateSkillCard');

        return $actions
            ->add(Crud::PAGE_INDEX, $update)
            ->add(Crud::PAGE_DETAIL, $update)
            ->add(Crud::PAGE_INDEX, $modules)
            ->add(Crud::PAGE_DETAIL, $modules)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('number', 'Numero');
        yield AssociationField::new('student', 'Email studente');
        
        if ($pageName !== Crud::PAGE_EDIT) {
            yield AssociationField::new('certification', 'Certificazione');
        }
        
        yield IntegerField::new('credits', 'Crediti');
        yield DateField::new('expiresAt', 'Data scadenza')
            ->setHelp('Se non inserito verrà eventualmente usata la data odierna più la durata del certificazione scelta');
        yield TextField::new('status')
            ->hideOnForm();
    }

    /**
     * @param SkillCard $entityInstance
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance->getCertification()->hasExpiry() && is_null($entityInstance->getExpiresAt())) {
            $entityInstance->setExpiresAt(
                (new \DateTime())->add($entityInstance->getCertification()->getDuration())
            );
        }

        /** @var ModuleRepository */
        $repo = $this->getDoctrine()->getRepository(Module::class);
        $modules = $repo->findModules($entityInstance->getCertification(), true);
        foreach ($modules as $module) {
            $entityInstance->addSkillCardModule(
                (new SkillCardModules())
                    ->setModule($module)
                    ->setIsPassed(false)
            );
        }
        $entityInstance->setStatus('ACTIVATED');
        parent::persistEntity($entityManager, $entityInstance);
    }

    protected function renovateSkillCard(AdminContext $adminContext)
    {
        /*
            TODO: in fase di rinnovo aggiungere gli esami della nuova certificazione (se non sono già presenti nel portfolio)
            Se questi sono già presenti nel portfolio, allora impostare il campo isPassed come false
        */

        /** @var SkillCard */
        $skillCard = $adminContext->getEntity()->getInstance();
        $updateCert = $skillCard->getCertification()->getUpdateCertification();
        $skillCard->setCertification($updateCert);
        /** @var \DateTime */
        $oldExpiry = $skillCard->getExpiresAt();
        if (!is_null($updateCert->getDuration()) && $oldExpiry instanceof \DateTime) {
            $newExpiry = \DateTimeImmutable::createFromMutable($oldExpiry->add($updateCert->getDuration()));
            $skillCard->setExpiresAt($newExpiry);
        }
        $this->getDoctrine()->getManager()->flush($skillCard);
        return $this->redirect($adminContext->getReferrer());
    }

    /* #region ChooseModule Region */
    const fieldName = 'skillCardModules';
    public function chooseModules(Request $request, AdminContext $adminContext)
    {
        /** @var SkillCard */
        $skillCard = $adminContext->getEntity()->getInstance();
        /** @var ModuleRepository */
        $repo = $this->getDoctrine()->getRepository(Module::class);
        $choices = $repo->findModules($skillCard->getCertification(), null);
        $defaults = [];
        $ids = [];

        foreach ($skillCard->getSkillCardModules()->getIterator() as $skillCardModule) {
            /** @var SkillCardModules $skillCardModule */
            $module = $skillCardModule->getModule();
            $defaults[] = $module;
            $ids[$module->getId()] = $skillCardModule->getId();
        }

        $form = $this->createModulesForm($choices, $defaults);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Module[] */
            $choices = $form->getData()['skillCardModules'];
            foreach (array_diff($choices, $defaults) as $choice) {
                $skillCard->addSkillCardModule((new SkillCardModules)->setModule($choice)->setIsPassed(false));
            }
            
            $repo = $this->getDoctrine()->getRepository(SkillCardModules::class);
            foreach (array_diff($defaults, $choices) as $choice) {
                $skillCard->removeSkillCardModule($repo->find($ids[$choice->getId()]));
            }

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Esami impostati correttamente');
            return $this->redirect($adminContext->getReferrer());
        }

        return $this->render('customForm.html.twig', [
            'title' => 'Imposta gli esami',
            'ea' => $adminContext,
            'form' => $form->createView(),
        ]);
    }

    protected function createModulesForm(array $choices, array $defaults)
    {   
        return $this->createFormBuilder()
            ->add(self::fieldName, EntityType::class, [
                'label' => false,
                'class' => Module::class,
                'choices' => $choices,
                'choice_label' => 'nome',
                'expanded' => true,
                'multiple' => true,
                'data' => $defaults
            ])
            ->add('save', SubmitType::class)
            ->getForm();
    }
    /* #endregion */
}
