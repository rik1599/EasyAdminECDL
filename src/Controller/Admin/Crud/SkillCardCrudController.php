<?php

namespace App\Controller\Admin\Crud;

use App\Entity\Module;
use App\Entity\SkillCard;
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

class SkillCardCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SkillCard::class;
    }

    public function configureActions(Actions $actions): Actions
    {   
        $modules = Action::new('Scegli esami')
            ->displayIf(function (SkillCard $skillCard) {
                /** @var ModuleRepository */
                $repo = $this->getDoctrine()->getRepository(Module::class);
                $choices = $repo->findModules($skillCard->getCertification(), false);
                return sizeof($choices) > 0;
            })
            ->linkToRoute('choose_modules', function(SkillCard $skillCard) {
                return [
                    'id' => $skillCard->getId()
                ];
            });

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
        yield AssociationField::new('certification', 'Certificazione');
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
        $entityInstance->setStatus('ACTIVATED');
        parent::persistEntity($entityManager, $entityInstance);
    }

    protected function renovateSkillCard(AdminContext $adminContext)
    {
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
}
