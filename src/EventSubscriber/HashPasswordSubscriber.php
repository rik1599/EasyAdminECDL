<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Service\UserSecurityService;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class HashPasswordSubscriber implements EventSubscriberInterface
{
    /** @var UserSecurityService */
    private $userSecurityService;

    public function __construct(UserSecurityService $userSecurityService) {
        $this->userSecurityService = $userSecurityService;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['onCreate'],
            BeforeEntityUpdatedEvent::class => ['onUpdate']
        ];
    }

    public function onCreate(BeforeEntityPersistedEvent $beforeEntityPersistedEvent)
    {
        /** @var User $entity */
        $entity = $beforeEntityPersistedEvent->getEntityInstance();

        if ($entity instanceof User) {
            $this->updatePassword($entity);
            $entity->setCreatedAt(new \DateTime());
        } else {
            return;
        }
    }

    public function onUpdate(BeforeEntityUpdatedEvent $beforeEntityUpdatedEvent)
    {
        /** @var User $entity */
        $entity = $beforeEntityUpdatedEvent->getEntityInstance();

        if ($entity instanceof User) {
            $this->updatePassword($entity);
        } else {
            return;
        }
    }

    private function updatePassword(?User $user)
    {
        $this->userSecurityService->setupUser($user);
        $user->setUpdatedAt(new \DateTime());
    }
}
