<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserSecurityService
{
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoderInterface) {
        $this->passwordEncoder = $userPasswordEncoderInterface;
    }

    public function setupUserPassword(User $user, string $plainPassword): void
    {
        $password = $this->passwordEncoder->encodePassword($user, $plainPassword);
        $user->setPassword($password);
        $this->lastUpdate($user);
    }

    public function lastUpdate(User $user)
    {
        $user->setUpdatedAt(new \DateTime());
    }

    public function lastAccess(User $user)
    {
        $user->setLastLoginAt(new \DateTime());
    }
}
