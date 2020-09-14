<?php

namespace App\Controller\Admin;

use App\Entity\Student;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;

class StudentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Student::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::DELETE, Action::INDEX, Action::DETAIL)
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN, function (Action $action) {
                return $action->linkToUrl(function (Student $student) {
                    /** @var CrudUrlGenerator */
                    $crudUrlGenerator = $this->get(CrudUrlGenerator::class);
                    $url = $crudUrlGenerator->build()
                        ->setController(UserCrudController::class)
                        ->setAction(Action::INDEX)
                        ->generateUrl();
                    return $url;
                });
            });
    }

    public function configureFields(string $pageName): iterable
    {
        yield DateField::new('birthDate')->onlyOnForms();
    }
}
