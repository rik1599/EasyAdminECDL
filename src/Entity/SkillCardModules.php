<?php

namespace App\Entity;

use App\Repository\SkillCardModulesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SkillCardModulesRepository::class)
 */
class SkillCardModules
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=SkillCard::class, inversedBy="skillCardModules")
     * @ORM\JoinColumn(nullable=false)
     */
    private $skillCard;

    /**
     * @ORM\ManyToOne(targetEntity=Module::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $module;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPassed;

    /**
     * @ORM\OneToMany(targetEntity=Booking::class, mappedBy="module", orphanRemoval=true)
     */
    private $bookings;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSkillCard(): ?SkillCard
    {
        return $this->skillCard;
    }

    public function setSkillCard(?SkillCard $skillCard): self
    {
        $this->skillCard = $skillCard;

        return $this;
    }

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function setModule(?Module $module): self
    {
        $this->module = $module;

        return $this;
    }

    public function getIsPassed(): ?bool
    {
        return $this->isPassed;
    }

    public function setIsPassed(bool $isPassed): self
    {
        $this->isPassed = $isPassed;

        return $this;
    }

    /**
     * @return Collection|Booking[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setModule($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->contains($booking)) {
            $this->bookings->removeElement($booking);
            // set the owning side to null (unless already changed)
            if ($booking->getModule() === $this) {
                $booking->setModule(null);
            }
        }

        return $this;
    }
}
