<?php

namespace App\Controller\Admin;

use App\Entity\Student;
use App\Entity\User;
use App\Field\PasswordField;
use App\Service\UserSecurityService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;

class UserCrudController extends AbstractCrudController
{
    /** @var UserSecurityService */
    private $userSecurityService;

    public function __construct(UserSecurityService $userSecurityService)
    {
        $this->userSecurityService = $userSecurityService;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsAsDropdown(true);
    }

    public function configureActions(Actions $actions): Actions
    {
        $anagrafica = Action::new('anagrafica', 'Anagrafica')
            ->displayIf(static function (User $user) {
                return !is_null($user->getStudent()) && 'ROLE_STUDENT' == $user->getRole();
            })
            ->linkToUrl(function(User $user) {
                $crudUrlGenerator = $this->get(CrudUrlGenerator::class);
                $url = $crudUrlGenerator->build()
                    ->setController(StudentCrudController::class)
                    ->setAction(Action::DETAIL)
                    ->setEntityId($user->getStudent()->getId())
                    ->generateUrl();
                return $url;
            });
        
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, $anagrafica)
            ->add(Crud::PAGE_INDEX, $anagrafica)
            ->add(Crud::PAGE_DETAIL, $anagrafica)
            ->disable(Action::SAVE_AND_ADD_ANOTHER);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('firstName', 'Nome');
        yield TextField::new('lastName', 'Cognome');
        yield EmailField::new('email');
        yield PasswordField::new('password', 'Password')->onlyWhenCreating();

        if (in_array($pageName, [Crud::PAGE_DETAIL, Crud::PAGE_INDEX, Crud::PAGE_NEW])) {
            yield ChoiceField::new('role', 'Permessi')
            ->setChoices(User::ROLES)
            ->setRequired(true);
        }

        yield DateTimeField::new('createdAt')->hideOnForm();
        yield DateTimeField::new('updatedAt')->hideOnForm();
        yield DateTimeField::new('lastLoginAt')->hideOnForm();
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var User $entityInstance */
        $this->userSecurityService->setupUserPassword($entityInstance, $entityInstance->getPassword());
        $entityInstance->setCreatedAt(new \DateTime());

        if (in_array('ROLE_STUDENT', $entityInstance->getRoles())) {
            $student = new Student();
            $student->setUser($entityInstance);
            $student->setBirthDate(new \DateTime('1990-01-01'));
            $entityInstance->setStudent($student);
        }
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function new(AdminContext $context)
    {
        $response = parent::new($context);

        //Catch submit button
        $submitButtonName = isset($context->getRequest()->request->get('ea')['newForm']['btn']) ? 
            $context->getRequest()->request->get('ea')['newForm']['btn'] : null;

        /** @var User */
        $user = $context->getEntity()->getInstance();
        if (!is_null($submitButtonName) && !is_null($user->getStudent()) &&
            Action::SAVE_AND_RETURN === $submitButtonName) {    //If I created a Student
            $crudUrlGenerator = $this->get(CrudUrlGenerator::class);
            $url = $crudUrlGenerator->build()
                    ->setController(StudentCrudController::class)
                    ->setAction(Action::EDIT)
                    ->setEntityId($user->getStudent()->getId())
                    ->generateUrl();
            return $this->redirect($url);
        } else {
            return $response;
        }
    }
}
