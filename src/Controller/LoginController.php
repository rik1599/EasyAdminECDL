<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserSecurityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    /** @var UserSecurityService */
    private $userSecurityService;

    public function __construct(UserSecurityService $userSecurityService) {
        $this->userSecurityService = $userSecurityService;
    }

    /**
     * @Route("/", name="home")
     * Redirections to Dashboards home pages
     * @return RedirectResponse
     */
    public function home()
    {
        /** @var User $user */
        if ($user = $this->getUser()) {
            $role = $user->getRole();
            $this->userSecurityService->lastAccess($user);
            $this->getDoctrine()->getManager()->flush();
        } else {
            $role = "ANONYMOUS";
        }

        switch ($role) {
            case 'ROLE_ADMIN':
                $redirect = $this->redirectToRoute('admin');
                break;

            case 'ROLE_STUDENT':
                $redirect = $this->redirectToRoute('student');
                break;
            
            default:
                $redirect = $this->redirectToRoute('app_login');
                break;
        }

        return $redirect;
    }

    /**
     * @Route("/login", name="app_login")
     * Login form
     * @param AuthenticationUtils $authenticationUtils - the authenticator service
     * @return Response login page view
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@EasyAdmin/page/login.html.twig', [
            'last_username' => $lastUsername, 
            'error' => $error,
            'page_title' => 'ICDL Dashboard',
            'csrf_token_intention' => 'authenticate',
            'username_label' => 'Indirizzo email',
            'password_label' => 'Password',
            'username_parameter' => 'email',
            'password_parameter' => 'password'
            ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
