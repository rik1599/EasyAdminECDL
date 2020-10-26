<?php

namespace App\Entity;

use App\Repository\SkillCardModuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SkillCardModuleRepository::class)
 */
class SkillCardModule
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
     * @ORM\ManyToOne(targetEntity=CertificationModule::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $module;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $status;

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

    public function getModule(): ?CertificationModule
    {
        return $this->module;
    }

    public function setModule(?CertificationModule $module): self
    {
        $this->module = $module;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

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

    public function __toString()
    {
        return $this->getModule()->getModule()->getNome();
    }
}
