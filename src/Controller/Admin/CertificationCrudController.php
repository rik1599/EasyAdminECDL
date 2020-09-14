<?php

namespace App\Controller\Admin;

use App\Entity\Certification;
use App\Field\DateIntervalField;
use App\Form\ModuleType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
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
            ->setDateIntervalFormat("%y anni");
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name');
        yield BooleanField::new('hasExpiry');
        yield DateIntervalField::new('expiryTimeInterval')
            ->formatValue(function($value) {
                return is_null($value) ? "" : $value->format("%y anni");
            });
        yield CollectionField::new('certificationModules', null)
            ->setFormTypeOptions([
                'entry_type' => ModuleType::class,
                'by_reference' => false,
                'allow_delete' => true
            ])
            ->addCssClass('form-inline')
            ->onlyOnForms();
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($this->checkExpires($entityInstance)) {
            parent::persistEntity($entityManager, $entityInstance);
        }
        
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($this->checkExpires($entityInstance)) {
            parent::updateEntity($entityManager, $entityInstance);
        }
        
    }

    protected function checkExpires(Certification $certification)
    {
        if (!$certification->getHasExpiry()) {
            $certification->setExpiryTimeInterval(null);
            return true;
        } elseif (is_null($certification->getExpiryTimeInterval())) {
            return false;
        }
    }
}
