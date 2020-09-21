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
     * @ORM\OneToMany(targetEntity=CertificationModule::class, mappedBy="certification", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $certificationModules;

    /**
     * @ORM\Column(type="dateinterval", nullable=true)
     */
    private $duration;

    /**
     * @ORM\ManyToOne(targetEntity=Certification::class, inversedBy="updateOf")
     * @ORM\JoinColumn(name="update_certification_id", referencedColumnName="id")
     */
    private $updateCertification;

    /**
     * @ORM\OneToMany(targetEntity=Certification::class, mappedBy="updateCertification")
     */
    private $updateOf;

    /**
     * @ORM\OneToMany(targetEntity=Session::class, mappedBy="certification", orphanRemoval=true)
     */
    private $sessions;

    public function __construct()
    {
        $this->certificationModules = new ArrayCollection();
        $this->sessions = new ArrayCollection();
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

    public function __toString()
    {
        return $this->name;
    }

    public function getDuration(): ?\DateInterval
    {
        return $this->duration;
    }

    public function setDuration(?\DateInterval $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function hasExpiry(): bool
    {
        return is_null($this->duration);
    }

    public function getUpdateCertification(): ?self
    {
        return $this->updateCertification;
    }

    public function setUpdateCertification(?self $updateCertification): self
    {
        $this->updateCertification = $updateCertification;

        return $this;
    }

    /**
     * @return Collection|Session[]
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): self
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions[] = $session;
            $session->setCertification($this);
        }

        return $this;
    }

    public function removeSession(Session $session): self
    {
        if ($this->sessions->contains($session)) {
            $this->sessions->removeElement($session);
            // set the owning side to null (unless already changed)
            if ($session->getCertification() === $this) {
                $session->setCertification(null);
            }
        }

        return $this;
    }
}
