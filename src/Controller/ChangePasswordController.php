<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserSecurityService;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ChangePasswordController extends AbstractController
{
    /** @var UserSecurityService */
    private $userSecurityService;

    public function __construct(UserSecurityService $userSecurityService) {
        $this->userSecurityService = $userSecurityService;
    }

    /**
     * @Route("change-password", name="change_password")
     */
    public function changePasswordLoginForm(AdminContext $adminContext, Request $request)
    {
        $fieldName = 'password';
        /** @var User */
        $user = $this->getUser();
        $form = $this->form($fieldName);

        $form->handleRequest($request);
        $view = [
            'ea' => $adminContext,
            'form' => $form->createView()
        ];

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->getData()[$fieldName];
            $this->userSecurityService->setupUserPassword($user, $password);
            $this->getDoctrine()->getManager()->flush();
            $view['done'] = true; 
        }

        return $this->render('changePassword.html.twig', $view);
    }

    protected function form($name) 
    {
        return $this->createFormBuilder()
            ->add($name, RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Le password non corrispondono',
                'required' => 'true',
                'first_options' => ['label' => 'Nuova password'],
                'second_options' => ['label' => 'Ripeti nuova password']
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Salva'
            ])
            ->getForm();
    }
}
