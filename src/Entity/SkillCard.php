<?php

namespace App\Entity;

use App\Repository\SkillCardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SkillCardRepository::class)
 */
class SkillCard
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=7)
     * @Assert\Regex(
     *  pattern="/^\d{7}$/",
     *  message="Consentite solo stringhe di 7 cifre decimali"
     * )
     */
    private $number;

    /**
     * @ORM\ManyToOne(targetEntity=Student::class, inversedBy="skillCards")
     * @ORM\JoinColumn(nullable=false)
     */
    private $student;

    /**
     * @ORM\ManyToOne(targetEntity=Certification::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $certification;

    /**
     * @ORM\Column(type="integer")
     */
    private $credits;

    /**
     * @ORM\ManyToMany(targetEntity=Module::class)
     */
    private $chosenModules;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expiresAt;

    public function __construct()
    {
        $this->chosenModules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): self
    {
        $this->student = $student;

        return $this;
    }

    public function getCertification(): ?Certification
    {
        return $this->certification;
    }

    public function setCertification(?Certification $certification): self
    {
        $this->certification = $certification;

        return $this;
    }

    public function getCredits(): ?int
    {
        return $this->credits;
    }

    public function setCredits(int $credits): self
    {
        $this->credits = $credits;

        return $this;
    }

    /**
     * @return Collection|Module[]
     */
    public function getChosenModules(): Collection
    {
        return $this->chosenModules;
    }

    public function addChosenModule(Module $chosenModule): self
    {
        if (!$this->chosenModules->contains($chosenModule)) {
            $this->chosenModules[] = $chosenModule;
        }

        return $this;
    }

    public function removeChosenModule(Module $chosenModule): self
    {
        if ($this->chosenModules->contains($chosenModule)) {
            $this->chosenModules->removeElement($chosenModule);
        }

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTimeInterface $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }
}
