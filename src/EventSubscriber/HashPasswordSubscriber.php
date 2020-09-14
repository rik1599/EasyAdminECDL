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
        ];
    }

    public function onCreate(BeforeEntityPersistedEvent $beforeEntityPersistedEvent)
    {
        /** @var User $entity */
        $entity = $beforeEntityPersistedEvent->getEntityInstance();

        if ($entity instanceof User) {
            $this->userSecurityService->setupUserPassword($entity, $entity->getPassword());
            $entity->setCreatedAt(new \DateTime());
        } else {
            return;
        }
    }
}
