<?php

namespace App\Controller\Admin;

use App\Entity\Session;
use App\Enum\EnumRole;
use App\Enum\EnumSessionStatus;
use App\Enum\EnumSessionType;
use DateInterval;
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
    /** @var \DateInterval */
    private $defaultExpireTimeInterval;

    public function __construct() {
        $this->defaultExpireTimeInterval = new \DateInterval('P1W');
    }

    public static function getEntityFqcn(): string
    {
        return Session::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $cancelSessionActionName = 'cancelSession';
        $cancelSessionAction = Action::new($cancelSessionActionName, 'Annulla sessione')
            ->displayIf(function (Session $session) {
                return $session->getStatus() === EnumSessionStatus::ACTIVATED;
            })
            ->linkToCrudAction('cancelSession');
        
            return $actions
                ->add(Crud::PAGE_INDEX, $cancelSessionAction)
                ->setPermissions([
                    Action::EDIT => EnumRole::ROLE_ADMIN,
                    Action::NEW => EnumRole::ROLE_ADMIN,
                    $cancelSessionActionName => EnumRole::ROLE_ADMIN
                ])
                ->disable(Action::DELETE);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        /* If you are creating new session, set default value as current datetime */
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
        $this->setExpiryDate($entityInstance, $this->defaultExpireTimeInterval);
        $entityInstance->setStatus(EnumSessionStatus::ACTIVATED);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->setExpiryDate($entityInstance, $this->defaultExpireTimeInterval);
        parent::updateEntity($entityManager, $entityInstance);
    }

    /**
     * If subscribe expiry date is null, set subscribe expiry date
     * @param Session $session - the session to calculate subscribe expiry date
     * @param DateInterval $dateInterval - time interval to subtrack to session datetime
     */
    private function setExpiryDate(Session $session, \DateInterval $dateInterval)
    {
        if (is_null($session->getSubscribeExpireDate())) {
            /** @var \DateTime */
            $examDate = $session->getDatetime();
            $expiryDate = (clone $examDate)->sub($dateInterval);
            $session->setSubscribeExpireDate($expiryDate);
        }
    }

    /**
     * Cancel the given session
     * @param AdminContext $adminContext - the context to extract the session
     */
    public function cancelSession(AdminContext $adminContext)
    {
        /** @var Session */
        $session = $adminContext->getEntity()->getInstance();
        $session->setStatus(EnumSessionStatus::CANCELED);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirect($adminContext->getReferrer());
    }
}
