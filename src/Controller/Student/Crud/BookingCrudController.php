<?php

namespace App\Controller\Student\Crud;

use App\Entity\Booking;
use App\Entity\Module;
use App\Entity\Session;
use App\Entity\SkillCard;
use App\Entity\Student;
use App\Entity\User;
use App\Form\NewBookingFormType;
use App\Repository\ModuleRepository;
use DateTime;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
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
            ->disable(Action::DELETE, Action::EDIT)
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action
                    ->linkToCrudAction('booking')
                    ->setLabel('Prenota un esame');
            });
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('skillCard', false)->hideOnForm();
        yield AssociationField::new('session')->hideOnForm();
        yield AssociationField::new('module')->hideOnForm();
        yield IntegerField::new('round')->hideOnForm();
    }

    public function booking(AdminContext $adminContext, Request $request)
    {
        /** @var User */
        $user = $adminContext->getUser();
        assert(!is_null($user->getStudent()));

        /** @var Booking */
        $booking = $adminContext->getEntity()->getInstance();
        $booking = is_null($booking) ? (new Booking()) : $booking;

        $form = $this->newBookingForm($user->getStudent(), $booking);
        $form->handleRequest($request);

        $adminContext->getAssets()->addJsFile('js/bookingFormModifier.js');
        return $this->render('customForm.html.twig', [
            'ea' => $adminContext,
            'title' => 'Prenota un esame',
            'form' => $form->createView()
        ]);
    }

    public function newBookingForm(Student $student, ?Booking $booking)
    {
        $builder = $this->createFormBuilder($booking)
            ->add('skillCard', EntityType::class, [
                'class' => SkillCard::class,
                'choices' => $student->getValidSkillCard(),
                'choice_label' => 'number'
            ]);
        
            $builder->addEventListener(
                FormEvents::PRE_SET_DATA, function (FormEvent $formEvent) {
                    $form = $formEvent->getForm();
                    /** @var Booking */
                    $booking = $form->getData();
                    assert($booking instanceof Booking);
                    /** @var SkillCard */
                    $skillCard = $booking->getSkillCard();
                    $cert = $booking->getSkillCard()->getCertification();
                    /** @var ModuleRepository */
                    $repo = $this->getDoctrine()->getRepository(Module::class);
                    
                    $form->add('module', EntityType::class, [
                        'class' => Module::class,
                        'choices' => array_merge($mandatoryModules, $chosenModules)
                    ]);
                }
            );
        return $builder->getForm();
    }

    protected function addRoundsField(FormBuilderInterface $builder)
    {
        $formModifier = function (FormInterface $form, Session $session) {
            $rounds = [];
            $baseTime = (new DateTime())->setTimestamp($session->getDatetime()->getTimestamp());
            $roundInterval = new \DateInterval('PT1H');
            for ($i=0; $i < $session->getRounds(); $i++) {
                $baseTime->add($roundInterval); 
                $rounds[$baseTime->format('d/m/Y H:i')] = $i;
            }

            $form->add('rounds', ChoiceType::class, [
                'choices' => $rounds
            ]);
        };
    }
}
