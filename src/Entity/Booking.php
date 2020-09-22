<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 */
class Booking
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=SkillCard::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $skillCard;

    /**
     * @ORM\ManyToOne(targetEntity=SkillCardModule::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $module;

    /**
     * @ORM\ManyToOne(targetEntity=Session::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $session;

    /**
     * @ORM\Column(type="integer")
     */
    private $turn;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $status;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isApproved;

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

    public function getModule(): ?SkillCardModule
    {
        return $this->module;
    }

    public function setModule(?SkillCardModule $module): self
    {
        $this->module = $module;

        return $this;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): self
    {
        $this->session = $session;

        return $this;
    }

    public function getTurn(): ?int
    {
        return $this->turn;
    }

    public function setTurn(int $turn): self
    {
        $this->turn = $turn;

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

    public function getIsApproved(): ?bool
    {
        return $this->isApproved;
    }

    public function setIsApproved(bool $isApproved): self
    {
        $this->isApproved = $isApproved;

        return $this;
    }
}
