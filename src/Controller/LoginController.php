<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        /** @var \App\Entity\User $user */
        if ($user = $this->getUser()) {
            $role = $user->getRole();
            $this->lastAccess($user);
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
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

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

    private function lastAccess(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $user->setLastLoginAt(new \DateTime());
        $em->flush();
    }
}
