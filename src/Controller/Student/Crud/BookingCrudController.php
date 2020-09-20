<?php

namespace App\Controller\Student\Crud;

use App\Entity\Booking;
use App\Entity\Module;
use App\Entity\Session;
use App\Form\NewBookingFormType;
use DateTime;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

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

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('skillCard')->hideOnForm();
        yield AssociationField::new('session')->hideOnForm();
        yield AssociationField::new('module')->hideOnForm();
        yield TextField::new('rounds')->hideOnForm();
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
