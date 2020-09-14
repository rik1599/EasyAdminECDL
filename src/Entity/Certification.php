<?php

namespace App\Entity;

use App\Repository\CertificationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CertificationRepository::class)
 */
class Certification
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasExpiry;

    /**
     * @ORM\Column(type="dateinterval", nullable=true)
     */
    private $expiryTimeInterval;

    /**
     * @ORM\OneToMany(targetEntity=CertificationModule::class, mappedBy="certification", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $certificationModules;

    public function __construct()
    {
        $this->certificationModules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getHasExpiry(): ?bool
    {
        return $this->hasExpiry;
    }

    public function setHasExpiry(bool $hasExpiry): self
    {
        $this->hasExpiry = $hasExpiry;

        return $this;
    }

    public function getExpiryTimeInterval(): ?\DateInterval
    {
        return $this->expiryTimeInterval;
    }

    public function setExpiryTimeInterval(?\DateInterval $expiryTimeInterval): self
    {
        $this->expiryTimeInterval = $expiryTimeInterval;

        return $this;
    }

    /**
     * @return Collection|CertificationModule[]
     */
    public function getCertificationModules(): Collection
    {
        return $this->certificationModules;
    }

    public function addCertificationModule(CertificationModule $certificationModule): self
    {
        if (!$this->certificationModules->contains($certificationModule)) {
            $this->certificationModules[] = $certificationModule;
            $certificationModule->setCertification($this);
        }

        return $this;
    }

    public function removeCertificationModule(CertificationModule $certificationModule): self
    {
        if ($this->certificationModules->contains($certificationModule)) {
            $this->certificationModules->removeElement($certificationModule);
            // set the owning side to null (unless already changed)
            if ($certificationModule->getCertification() === $this) {
                $certificationModule->setCertification(null);
            }
        }

        return $this;
    }
}
