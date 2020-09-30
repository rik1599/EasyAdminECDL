<?php

namespace App\Controller\Student;

use App\Entity\Booking;
use App\Entity\CertificationModule;
use App\Entity\User;
use App\Enum\EnumBookingStatus;
use App\Form\BookingType;
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
use Symfony\Component\HttpFoundation\Request;

class BookingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Booking::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        /** @var Booking */
        $booking = $entityDto->getInstance();
        dump($searchDto);
        dump($booking);
        dump($fields);
        dump($filters);
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function(Action $action) {
                return $action->linkToCrudAction('booking');
            })
            ->disable(Action::DELETE, Action::EDIT);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('skillCard')->hideOnForm();
        yield AssociationField::new('session')->hideOnForm();
        yield AssociationField::new('module')
            ->hideOnForm()
            ->formatValue(function ($value, Booking $booking) {
                return $booking->getModule()->getModule()->getModule()->getNome();
            });
        yield IntegerField::new('turn')->hideOnForm();
    }
    
    public function booking(AdminContext $adminContext, Request $request)
    {
        /** @var User */
        $user = $adminContext->getUser();
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking, [
            'student' => $user->getStudent()
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $booking->setStatus(EnumBookingStatus::SUBSCRIBED);
            $skillCard = $booking->getSkillCard();
            $credits = $skillCard->getCredits();
            if ($credits == 0) {
                $this->addFlash('warning', 'Hai finito i crediti della tua skill card, la tua prenotazione dovrà essere approvata da un amministratore');
                $booking->setIsApproved(false);
            } else {
                $this->addFlash('success', 'Prenotazione inserita correttamente');
                $skillCard->setCredits($credits--);
                $booking->setIsApproved(true);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($booking);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirect($adminContext->getReferrer());
        }
        
        $adminContext->getAssets()->addJsFile('js/bookingFormModifier.js');
        return $this->render('customForm.html.twig', [
            'title' => 'Prenota un esame',
            'ea' => $adminContext,
            'form' => $form->createView()
        ]);
    }
}
