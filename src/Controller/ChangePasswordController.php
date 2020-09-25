<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserSecurityService;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * Create and handle the form to change user password
     * @param AdminContext $adminContext - current context
     * @param Request $request - current http request
     * @return Response|RedirectResponse - render the form or redirect to home 
     */
    public function changePasswordLoginForm(AdminContext $adminContext, Request $request)
    {
        $fieldName = 'password';
        /** @var User */
        $user = $this->getUser();
        $form = $this->form($fieldName);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->getData()[$fieldName];
            $this->userSecurityService->setupUserPassword($user, $password);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Password modificata correttamente!');
            return $this->redirectToRoute('home');
        }

        return $this->render('customForm.html.twig', [
            'title' => 'Cambia password del profilo',
            'ea' => $adminContext,
            'form' => $form->createView()
        ]);
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
