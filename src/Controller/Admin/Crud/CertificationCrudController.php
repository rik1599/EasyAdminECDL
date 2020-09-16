<?php

namespace App\Controller\Admin\Crud;

use App\Entity\Certification;
use App\Form\ModuleType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CertificationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Certification::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
    
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name');
        yield CollectionField::new('certificationModules', "Moduli")
            ->setFormTypeOptions([
                'entry_type' => ModuleType::class,
                'by_reference' => false,
                'allow_delete' => true
            ])
            ->setTemplatePath('fields/modules.html.twig')
            ->hideOnIndex()
            ->addCssClass('form-inline');
    }
}
