<?php

namespace App\Controller\Student;

use App\Entity\Booking;
use App\Entity\SkillCard;
use App\Entity\User;
use App\Enum\EnumBookingStatus;
use App\Enum\EnumSkillcardModule;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use App\Service\BookingService;
use DateInterval;
use DateTimeImmutable;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use Symfony\Component\HttpFoundation\Request;

class BookingCrudController extends AbstractCrudController
{
    const MAX_BOOKINGS_PER_TURN = 24;

    /** @var BookingRepository */
    private $bookingRepository;

    /** @var BookingService */
    private $bookingService;

    public function __construct(BookingRepository $bookingRepository, BookingService $bookingService)
    {
        $this->bookingRepository = $bookingRepository;
        $this->bookingService = $bookingService;
    }

    public static function getEntityFqcn(): string
    {
        return Booking::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {   
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        /** @var AdminContext */
        $adminContext = $this->get(AdminContextProvider::class)->getContext();

        /** @var User */
        $user = $adminContext->getUser();
        $student = $user->getStudent();

        /* Visualizza solo le prenotazioni dell'utente */
        $qb->andWhere($qb->expr()->in('entity.skillCard', ':sc'));
        $qb->addOrderBy('entity.status', 'DESC');
        $qb->setParameter('sc', $student->getSkillCards());
        return $qb;
    }

    public function configureActions(Actions $actions): Actions
    {
        $cancelBookingAction = Action::new('cancelBookingAction', 'Annulla')
            ->linkToCrudAction('cancelBooking')
            ->displayIf(function (Booking $booking) {
                return $booking->getStatus() === EnumBookingStatus::SUBSCRIBED;
            });

        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->linkToCrudAction('booking');
            })
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $cancelBookingAction)
            ->add(Crud::PAGE_DETAIL, $cancelBookingAction)
            ->disable(Action::DELETE, Action::EDIT);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('skillCard')->hideOnForm();
        yield AssociationField::new('session')->hideOnForm();
        yield AssociationField::new('module')
            ->hideOnForm();
        yield IntegerField::new('turn')
            ->hideOnForm()
            ->formatValue(function ($value, Booking $booking) {
                $baseTime = DateTimeImmutable::createFromMutable($booking->getSession()->getDatetime());
                return $baseTime->add(new DateInterval("PT" . $value . "H"))->format('H:i');
            });
        yield TextField::new('status')->hideOnForm();
    }

    public function booking(AdminContext $adminContext, Request $request)
    {
        /** @var User */
        $user = $adminContext->getUser();
        /** @var Booking */
        $booking = $adminContext->getEntity()->getInstance();
        $isCreating = is_null($booking);
        $booking = $isCreating ? new Booking() : $booking;
        $form = $this->createForm(BookingType::class, $booking, [
            'student' => $user->getStudent()
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $nBookings = $this->bookingRepository->countBookings($booking->getSession(), $booking->getTurn());

            if ($nBookings >= self::MAX_BOOKINGS_PER_TURN && $isCreating) {
                $this->addFlash('danger', 'Non è possibile inserire la prenotazione.
                È stato raggiunto il tetto massimo di iscrizioni per questo turno');
            } else if ($this->bookingService->bookExam($booking)) {
                $this->addFlash('success', 'Prenotazione inserita correttamente');
            } else {
                $this->addFlash('warning', 'Hai finito i crediti della tua skill card, la tua prenotazione dovrà essere approvata da un amministratore');
            }
            return $this->redirect($adminContext->getReferrer());
        }

        $adminContext->getAssets()->addJsFile('js/bookingFormModifier.js');
        return $this->render('customForm.html.twig', [
            'title' => 'Prenota un esame',
            'ea' => $adminContext,
            'form' => $form->createView()
        ]);
    }

    public function cancelBooking(AdminContext $adminContext)
    {
        /** @var Booking */
        $booking = $adminContext->getEntity()->getInstance();
        $this->bookingService->cancelBooking($booking);
        return $this->redirect($adminContext->getReferrer());
    }
}
