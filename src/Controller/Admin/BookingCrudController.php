<?php

namespace App\Controller\Admin;

use App\Entity\Booking;
use App\Enum\EnumBookingStatus;
use App\Service\BookingService;
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
    /** @var BookingService */
    private $bookingService;

    public function __construct(BookingService $bookingService) {
        $this->bookingService = $bookingService;
    }

    public static function getEntityFqcn(): string
    {
        return Booking::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $approveBookingAction = Action::new('approveBookingAction', 'Conferma prenotazione')
            ->displayIf(function (Booking $booking) {
                return !$booking->getIsApproved() && $booking->getStatus() === EnumBookingStatus::SUBSCRIBED;
            })
            ->linkToCrudAction('approveBookingAction');
        return $actions
            ->add(Crud::PAGE_INDEX, $approveBookingAction)
            ->add(Crud::PAGE_DETAIL, $approveBookingAction)
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

    public function approveBookingAction(AdminContext $adminContext)
    {
        /** @var Booking */
        $booking = $adminContext->getEntity()->getInstance();

        if ($this->bookingService->approveBookingByAdmin($booking)) {
            $this->addFlash('success', 'Prenotazione confermata correttamente');
        } else {
            $this->addFlash('danger', 'Impossibile confermare la prenotazione, crediti insufficienti');
        }

        return $this->redirect($adminContext->getReferrer());
    }
}
