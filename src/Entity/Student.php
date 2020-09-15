<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StudentRepository::class)
 */
class Student
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="student", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="date")
     */
    private $birthDate;

    /**
     * @ORM\OneToMany(targetEntity=SkillCard::class, mappedBy="student", orphanRemoval=true)
     */
    private $skillCards;

    public function __construct()
    {
        $this->skillCards = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * @return Collection|SkillCard[]
     */
    public function getSkillCards(): Collection
    {
        return $this->skillCards;
    }

    public function addSkillCard(SkillCard $skillCard): self
    {
        if (!$this->skillCards->contains($skillCard)) {
            $this->skillCards[] = $skillCard;
            $skillCard->setStudent($this);
        }

        return $this;
    }

    public function removeSkillCard(SkillCard $skillCard): self
    {
        if ($this->skillCards->contains($skillCard)) {
            $this->skillCards->removeElement($skillCard);
            // set the owning side to null (unless already changed)
            if ($skillCard->getStudent() === $this) {
                $skillCard->setStudent(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getUser()->getFullName() . " " . $this->getUser()->getEmail();
    }
}
