<?php

namespace App\Controller\Admin;

use App\Entity\Notice;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class NoticeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Notice::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Avviso')
            ->setEntityLabelInPlural('Avvisi');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield DateTimeField::new('createdAt', 'Data di creazione')->hideOnForm();
        yield AssociationField::new('user', 'Creato da')->formatValue(function ($value, $entity) {
            return $entity->getUser()->getFullName();
        })->hideOnForm();
        yield TextareaField::new('text', 'Testo')->hideOnIndex();
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var Notice $entityInstance */
        $entityInstance->setUser($this->getUser());
        $this->setNoticeDate($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->setNoticeDate($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    private function setNoticeDate(Notice $notice) 
    {
        $notice->setCreatedAt(new \DateTime());
    }
}
