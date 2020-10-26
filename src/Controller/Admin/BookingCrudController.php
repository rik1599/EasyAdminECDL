<?php

namespace App\Controller\Admin;

use App\Entity\Booking;
use App\Enum\EnumBookingStatus;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class BookingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Booking::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $confirmAction = Action::new('confirmAction', 'Conferma prenotazione')
            ->displayIf(function (Booking $booking) {
                return !$booking->getIsApproved() && $booking->getStatus() === EnumBookingStatus::SUBSCRIBED;
            })
            ->linkToCrudAction('confirmAction');
        return $actions
            ->add(Crud::PAGE_INDEX, $confirmAction)
            ->add(Crud::PAGE_DETAIL, $confirmAction)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW, Action::DELETE, Action::EDIT);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id');
        yield TextField::new('skillCard.skillCardOwnerName', 'nome');
        yield AssociationField::new('skillCard');
        yield AssociationField::new('session');
        yield AssociationField::new('module');
        yield IntegerField::new('turn');
    }

    public function confirmAction(AdminContext $adminContext)
    {
        return $this->redirect($adminContext->getReferrer());
    }
}
