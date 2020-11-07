<?php

namespace App\Service;

use App\Entity\Booking;
use App\Entity\SkillCard;
use App\Enum\EnumBookingStatus;
use App\Enum\EnumSkillcardModule;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Symfony service to manage bookings actions
 * (book an exam, cancel a booking, approve a booking)
 */
class BookingService
{

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }
    
    /**
     * Approves, subtracts a credits from the booking skillcard (if enough) and save it to db
     * @param Booking $booking - precompilated booking (for example from a form) to save in db
     * @return bool - true if the booking is saved and approved (enough credits in skillcard), false if there aren't enough credits in skillcard, but the booking is saved however
     */
    public function bookExam(Booking $booking)
    {
        $booking->setStatus(EnumBookingStatus::SUBSCRIBED);
        $booking->getModule()->setStatus(EnumSkillcardModule::SUBSCRIBED);
        
        $isApproved = $this->approveBooking($booking);

        $this->entityManager->persist($booking);
        $this->entityManager->flush();

        return $isApproved;
    }

    /**
     * Update a booking approving it (if there are enough credits in skillcard)
     * @param Booking $booking
     * @return bool - true if the booking being approved, else false
     */
    public function approveBookingByAdmin(Booking $booking) {
        if ($booking->getIsApproved() == true) {
            return true;
        }

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

    /**
     * Cancel a booking and refund a credit to skillcard
     * @param Booking $booking
     */
    public function cancelBooking(Booking $booking) {
        $booking->setStatus(EnumBookingStatus::CANCELED);

        /** @var SkillCard */
        $skillCard = $booking->getSkillCard();
        $credits = $skillCard->getCredits();
        $skillCard->setCredits($credits++);

        $this->entityManager->flush();
    }
}
