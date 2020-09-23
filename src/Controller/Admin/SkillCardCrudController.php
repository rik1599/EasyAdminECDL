<?php

namespace App\Controller\Admin\Crud;

use App\Entity\CertificationModule;
use App\Entity\Module;
use App\Entity\SkillCard;
use App\Entity\SkillCardModule;
use App\Enum\EnumSkillCard;
use App\Form\SkillCardModuleType;
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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
     * @param EntityManagerInterface $entityManager
     * @param SkillCard $entityInstance
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance->getCertification()->hasExpiry() && is_null($entityInstance->getExpiresAt())) {
            $entityInstance->setExpiresAt(
                (new \DateTime())->add($entityInstance->getCertification()->getDuration())
            );
        }

        $cmRepo = $this->getDoctrine()->getRepository(CertificationModule::class);
        $mandatoryModules = $cmRepo->findByCertification($entityInstance->getCertification(), true);
        foreach ($mandatoryModules as $module) {
            $entityInstance->addSkillCardModule(
                (new SkillCardModule())
                    ->setModule($module)
                    ->setIsPassed(false)
            );
        }

        $entityInstance->setStatus(EnumSkillCard::ACTIVATED);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function renovateSkillCard(AdminContext $adminContext)
    {
        /*
            TODO: in fase di rinnovo aggiungere gli esami della nuova certificazione (se non sono già presenti nel portfolio)
            Se questi sono già presenti nel portfolio, allora impostare il campo isPassed come false
        */

        /** @var SkillCard */
        $skillCard = $adminContext->getEntity()->getInstance();
        $updateCert = $skillCard->getCertification()->getUpdateCertification();
        $skillCard->setCertification($updateCert);
        switch ($skillCard->getStatus()) {
            case EnumSkillCard::ACTIVATED:
                $skillCard->setStatus(EnumSkillCard::UPDATING);
                break;
            
            case EnumSkillCard::UPDATING:
                $skillCard->setStatus(EnumSkillCard::ACTIVATED);
                break;

            default:
                throw new \Exception("Stato SkillCard non valido");
        }


        /** @var \DateTime */
        $oldExpiry = $skillCard->getExpiresAt();
        if (!is_null($updateCert->getDuration()) && $oldExpiry instanceof \DateTime) {
            $newExpiry = \DateTimeImmutable::createFromMutable($oldExpiry->add($updateCert->getDuration()));
            $skillCard->setExpiresAt($newExpiry);
        }
        $this->getDoctrine()->getManager()->flush();
        return $this->redirect($adminContext->getReferrer());
    }

    public function chooseModules(Request $request, AdminContext $adminContext)
    {
        /** @var SkillCard */
        $skillCard = $adminContext->getEntity()->getInstance();

        $form = $this->createModulesForm($skillCard);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
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

    protected function createModulesForm(SkillCard $skillCard)
    {   
        return $this->createFormBuilder($skillCard)
            ->add('skillCardModules', CollectionType::class, [
                'label' => false,
                'entry_type' => SkillCardModuleType::class,
                'entry_options' => ['certification' => $skillCard->getCertification(), 'label' => false],
                'by_reference' => false,
                'allow_delete' => true,
                'allow_add' => true,
                'row_attr' => ['class' => 'form-inline']
            ])
            ->add('save', SubmitType::class)
            ->getForm();
    }
}
