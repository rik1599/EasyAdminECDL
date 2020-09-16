<?php

namespace App\Controller\Admin\Crud;

use App\Entity\SkillCard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
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

    public function configureActions(Actions $actions): Actions
    {   
        $modules = Action::new('Scegli esami')
            ->linkToRoute('choose_modules', function(SkillCard $skillCard) {
                return [
                    'id' => $skillCard->getId()
                ];
            });

        return $actions
            ->add(Crud::PAGE_INDEX, $modules)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('number', 'Numero');
        yield AssociationField::new('student', 'Email studente');
        yield AssociationField::new('certification');
        yield IntegerField::new('credits', 'Crediti');
    }
}
