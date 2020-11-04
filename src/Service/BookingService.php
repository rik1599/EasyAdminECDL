<?php

namespace App\Service;

use App\Entity\Booking;
use App\Entity\SkillCard;
use App\Enum\EnumBookingStatus;
use App\Enum\EnumSkillcardModule;
use Doctrine\ORM\EntityManagerInterface;

class BookingService
{

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }
    
    public function bookExam(Booking $booking)
    {
        $booking->setStatus(EnumBookingStatus::SUBSCRIBED);
        $booking->getModule()->setStatus(EnumSkillcardModule::SUBSCRIBED);
        
        $isApproved = $this->approveBooking($booking);

        $this->entityManager->persist($booking);
        $this->entityManager->flush();

        return $isApproved;
    }

    public function approveBookingByAdmin(Booking $booking) {
        $isApproved = $this->approveBooking($booking);
        $this->entityManager->flush();
        return $isApproved;
    }

    protected function approveBooking(Booking $booking)
    {
        $skillCard = $booking->getSkillCard();
        $credits = $skillCard->getCredits();
        $isApproved = true;

        if (!is_null($credits) && $credits == 0) {
            $booking->setIsApproved(false);
            $isApproved = false;
        } else {
            $skillCard->setCredits($credits--);
            $booking->setIsApproved(true);
        }
        return $isApproved;
    }

    public function cancelBooking(Booking $booking) {
        $booking->setStatus(EnumBookingStatus::CANCELED);

        /** @var SkillCard */
        $skillCard = $booking->getSkillCard();
        $credits = $skillCard->getCredits();
        $skillCard->setCredits($credits++);

        $this->entityManager->flush();
    }
}
