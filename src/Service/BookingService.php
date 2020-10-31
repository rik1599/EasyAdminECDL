<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;

class BookingService
{

    /** @var EntityManager */
    private $entityManager;

    public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }
}
