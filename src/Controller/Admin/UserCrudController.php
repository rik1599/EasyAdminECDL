<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Field\PasswordField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud;
    }

    public function configureActions(Actions $actions): Actions
    {   
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);   
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('firstName', 'Nome');
        yield TextField::new('lastName', 'Cognome');
        yield EmailField::new('email');
        yield PasswordField::new('password', 'Password')->onlyWhenCreating();
        yield ChoiceField::new('role', 'Permessi')->setChoices(User::ROLES);
        yield DateTimeField::new('createdAt')->hideOnForm();
        yield DateTimeField::new('updatedAt')->hideOnForm();
        yield DateTimeField::new('lastLoginAt')->hideOnForm();
    }
}
