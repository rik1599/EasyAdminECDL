<?php

namespace App\Entity;

use App\Repository\CertificationModuleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CertificationModuleRepository::class)
 */
class CertificationModule
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Certification::class, inversedBy="certificationModules")
     * @ORM\JoinColumn(nullable=false)
     */
    private $certification;

    /**
     * @ORM\ManyToOne(targetEntity=Module::class, inversedBy="certificationModules")
     * @ORM\JoinColumn(nullable=false)
     */
    private $module;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isMandatory;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function setModule(?Module $module): self
    {
        $this->module = $module;

        return $this;
    }

    public function getIsMandatory(): ?bool
    {
        return $this->isMandatory;
    }

    public function setIsMandatory(bool $isMandatory): self
    {
        $this->isMandatory = $isMandatory;

        return $this;
    }
}
