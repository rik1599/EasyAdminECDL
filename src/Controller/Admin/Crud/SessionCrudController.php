<?php

namespace App\Controller\Admin\Crud;

use App\Entity\Session;
use App\Enum\EnumSessionStatus;
use App\Enum\EnumSessionType;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SessionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Session::class;
    }
    
    public function configureActions(Actions $actions): Actions
    {
        $cancelSessionAction = Action::new('cancelSession', 'Annulla sessione')
            ->displayIf(function (Session $session) {
                return $session->getStatus() === EnumSessionStatus::ACTIVATED;
            })
            ->linkToCrudAction('cancelSession');
        
            return $actions
                ->add(Crud::PAGE_INDEX, $cancelSessionAction)
                ->disable(Action::DELETE);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        $dateTimeField = DateTimeField::new('datetime', 'Data e ora della sessione')
            ->renderAsChoice(true);
        if ($pageName === Crud::PAGE_NEW) {
            $dateTimeField->setFormTypeOption('data', new \DateTime());
        }
        yield $dateTimeField;

        yield DateField::new('subscribeExpireDate', 'Data scadenza iscrizioni')
            ->setRequired(false)
            ->setHelp("Se ometti questo campo, di default verranno calcolati 7 giorni prima della data dell'esame");
        yield IntegerField::new('rounds', 'Numero turni');
        yield AssociationField::new('certification', 'Certificazione')
            ->setRequired(true);
        yield ChoiceField::new('type', 'Tipo sessione')
            ->setChoices(EnumSessionType::getAll());
        
        yield TextField::new('status', 'Stato Sessione')->hideOnForm();
    }

    /**
     * @param Session $entityInstance
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (is_null($entityInstance->getSubscribeExpireDate())) {
            /** @var DateTime */
            $examDate = $entityInstance->getDatetime();
            $expiryDate = DateTimeImmutable::createFromMutable($examDate->sub(new \DateInterval('P1W')));
            $entityInstance->setSubscribeExpireDate($expiryDate);
        }
        $entityInstance->setStatus(EnumSessionStatus::ACTIVATED);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function cancelSession(AdminContext $adminContext)
    {
        /** @var Session */
        $session = $adminContext->getEntity()->getInstance();
        $session->setStatus(EnumSessionStatus::CANCELED);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirect($adminContext->getReferrer());
    }
}
