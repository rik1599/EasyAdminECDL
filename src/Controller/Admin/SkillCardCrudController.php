<?php

namespace App\Controller\Admin;

use App\Entity\SkillCard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SkillCardCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SkillCard::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('number', 'Numero');
        yield AssociationField::new('student')
            ->formatValue(function($value, SkillCard $entity) {
                return $entity->getStudent()->getUser()->getEmail();
            });
        yield AssociationField::new('certification')
            ->formatValue(function ($value, SkillCard $entity) {
                return $entity->getCertification()->getName();
            });
        yield IntegerField::new('credits', 'Crediti');
    }
}
