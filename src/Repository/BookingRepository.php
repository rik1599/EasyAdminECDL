<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\Session;
use App\Entity\SkillCard;
use App\Enum\EnumBookingStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * Return the number of bookings for a given session and turn
     */
    public function countBookings(Session $session, int $turn)
    {
        $qb = $this->createQueryBuilder('b');
        $qb->select($qb->expr()->count('b'))
            ->where('b.session = :ss')
            ->andWhere('b.turn = :turn')
            ->andWhere('b.status = :status')
            ->setParameters([
                'ss' => $session,
                'turn' => $turn,
                'status' => EnumBookingStatus::SUBSCRIBED
            ]);
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Get all booked turns in a session for a given skillCard
     */
    public function getBookedTurnInSession(Session $session, SkillCard $skillCard)
    {
        $qb = $this->createQueryBuilder('b')
            ->select('b.turn')
            ->where('b.skillCard = :sc')
            ->andWhere('b.session = :ss')
            ->andWhere('b.status = :st')
            ->setParameters([
                'sc' => $skillCard,
                'ss' => $session,
                'st' => EnumBookingStatus::SUBSCRIBED
            ]);
        $res = $qb->getQuery()->getResult();
        $turns = [];
        
        foreach ($res as $turn) {
            $turns[] = $turn['turn'];
        }

        return $turns;
    }
}
