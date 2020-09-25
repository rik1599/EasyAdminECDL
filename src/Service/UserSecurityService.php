<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserSecurityService
{
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /**
     * @param UserPasswordEncoder $userPasswordEncoderInterface - autowired password encoder defined for the users
     */
    public function __construct(UserPasswordEncoderInterface $userPasswordEncoderInterface) {
        $this->passwordEncoder = $userPasswordEncoderInterface;
    }

    /**
     * Encode a plain password with the current encoder and set the last updated field at current datetime
     * @param User $user - the user to set encoded password
     * @param string $plainPassword - the password string to be encoded
     */
    public function setupUserPassword(User $user, string $plainPassword): void
    {
        $password = $this->passwordEncoder->encodePassword($user, $plainPassword);
        $user->setPassword($password);
        $this->lastUpdate($user);
    }

    /**
     * Set user last update to current datetime
     * @param User $user
     */
    public function lastUpdate(User $user)
    {
        $user->setUpdatedAt(new \DateTime());
    }

    /**
     * Set user last login to current datetime
     * @param User $user
     */
    public function lastAccess(User $user)
    {
        $user->setLastLoginAt(new \DateTime());
    }
}
