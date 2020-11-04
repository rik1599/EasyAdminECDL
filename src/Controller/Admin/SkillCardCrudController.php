<?php

namespace App\Controller\Admin;

use App\Entity\CertificationModule;
use App\Entity\SkillCard;
use App\Entity\SkillCardModule;
use App\Enum\EnumSkillCard;
use App\Enum\EnumSkillcardModule;
use App\Form\SkillCardModuleType;
use App\Repository\CertificationModuleRepository;
use App\Repository\SkillCardModuleRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class SkillCardCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SkillCard::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $update = Action::new('Rinnova')
            ->displayIf(static function (SkillCard $skillCard) {
                return !is_null($skillCard->getCertification()->getUpdateCertification());
            })
            ->linkToCrudAction('renovateSkillCard');

        return $actions
            ->add(Crud::PAGE_INDEX, $update)
            ->add(Crud::PAGE_DETAIL, $update)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('number', 'Numero');
        yield TextField::new('skillCardOwnerName', 'Nome studente')->hideOnForm();
        yield AssociationField::new('student', 'Email studente');
        yield AssociationField::new('certification', 'Certificazione');
        yield IntegerField::new('credits', 'Crediti');
        yield DateField::new('expiresAt', 'Data scadenza')
            ->setHelp('Se non inserito verrà eventualmente usata la data odierna più la durata del certificazione scelta');
        yield TextField::new('status')
            ->hideOnForm();

        if ($pageName === Crud::PAGE_EDIT) {
            /** @var AdminContext */
            $adminContext = $this->get(AdminContextProvider::class)->getContext();
            /** @var SkillCard $skillCard */
            $skillCard = $adminContext->getEntity()->getInstance();

            yield CollectionField::new('skillCardModules', 'Esami')
                ->setFormTypeOptions([
                    'entry_type' => SkillCardModuleType::class,
                    'by_reference' => false,
                    'allow_delete' => true,
                    'entry_options' => [
                        'label' => false,
                        'skillCard' => $skillCard
                    ],
                    'row_attr' => ['class' => 'form-inline']
                ]);
        }
    }

    /**
     * If skillCard expiry date is null and its certification has expiry, set current datetime + certification duration
     * as skillCard expire date
     * @param EntityManagerInterface $entityManager
     * @param SkillCard $entityInstance
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance->getCertification()->hasExpiry() && is_null($entityInstance->getExpiresAt())) {
            $entityInstance->setExpiresAt(
                (new DateTime())->add($entityInstance->getCertification()->getDuration())
            );
        }

        $this->addMandatoryModules($entityInstance);

        $entityInstance->setStatus(EnumSkillCard::ACTIVATED);
        parent::persistEntity($entityManager, $entityInstance);
    }

    /**
     * Renovate the skillcard inside the current Context
     * If the skillCard is activated, it passes to updating state and the certification modules are added to the skillCard
     * or if they are already present, they are resetted.
     * If the skillCard is in updating state, it only return to activated state
     * @param AdminContext $adminContext - the current context
     * @return RedirectResponse - redirection to $adminContext referrer link
     */
    public function renovateSkillCard(AdminContext $adminContext)
    {
        /** @var SkillCard */
        $skillCard = $adminContext->getEntity()->getInstance();
        $updateCert = $skillCard->getCertification()->getUpdateCertification();
        $skillCard->setCertification($updateCert);
        switch ($skillCard->getStatus()) {
            case EnumSkillCard::ACTIVATED:
                $skillCard->setStatus(EnumSkillCard::UPDATING);
                $this->addMandatoryModules($skillCard);
                break;
            
            case EnumSkillCard::UPDATING:
                $skillCard->setStatus(EnumSkillCard::ACTIVATED);
                break;

            default:
                throw new InvalidArgumentException("Stato SkillCard non valido");
        }


        /** @var DateTime */
        $oldExpiry = clone ($skillCard->getExpiresAt());
        if (!is_null($updateCert->getDuration()) && $oldExpiry instanceof DateTime) {
            $newExpiry = clone ($oldExpiry->add($updateCert->getDuration()));
            $skillCard->setExpiresAt($newExpiry);
        }
        $this->getDoctrine()->getManager()->flush();
        return $this->redirect($adminContext->getReferrer());
    }

    protected function addMandatoryModules(SkillCard $skillCard)
    {
        /** @var CertificationModuleRepository */
        $cmRepo = $this->getDoctrine()->getRepository(CertificationModule::class);
        $mandatoryModules = $cmRepo->findByCertification($skillCard->getCertification(), true);

        /** @var SkillCardModuleRepository */
        $smRepo = $this->getDoctrine()->getRepository(SkillCardModule::class);

        foreach ($mandatoryModules as $module) {
            $skillCardModule = $smRepo->findOneBy([
                'skillCard' => $skillCard->getId(),
                'module' => $module->getId()
            ]);

            if (is_null($skillCardModule)) {
                $skillCard->addSkillCardModule(
                    (new SkillCardModule())
                        ->setModule($module)
                        ->setStatus(EnumSkillcardModule::UNPASSED)
                );
            } else {
                $skillCardModule->setStatus(EnumSkillcardModule::UNPASSED);
            }
        }
    }
}
