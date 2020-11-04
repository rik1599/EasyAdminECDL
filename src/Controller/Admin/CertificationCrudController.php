<?php

namespace App\Controller\Admin;

use App\Entity\Certification;
use App\Enum\EnumRole;
use App\Field\DateIntervalField;
use App\Form\ModuleType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CertificationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Certification::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDateIntervalFormat('%Y anni');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->setPermissions([
                Action::NEW => EnumRole::ROLE_ADMIN,
                Action::EDIT => EnumRole::ROLE_ADMIN,
                Action::DELETE => EnumRole::ROLE_ADMIN,
                Action::INDEX => EnumRole::ROLE_ADMIN
            ]);
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
        yield DateIntervalField::new('duration', 'Durata certificazione')
            ->formatValue(function (?\DateInterval $value) {
                $duration = is_null($value) ? 'Nessuna scadenza' : $value->format('%y anni');
                return $duration;
            });
        yield AssociationField::new('updateCertification', 'Certificazione di aggionamento');
    }
}
