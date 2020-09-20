<?php

namespace App\Entity;

use App\Repository\ModuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ModuleRepository::class)
 */
class Module
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $nome;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $syllabus;

    /**
     * @ORM\OneToMany(targetEntity=CertificationModule::class, mappedBy="module", orphanRemoval=true)
     */
    private $certificationModules;

    /**
     * @ORM\Column(type="integer")
     */
    private $minVote;

    public function __construct()
    {
        $this->certificationModules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getSyllabus(): ?string
    {
        return $this->syllabus;
    }

    public function setSyllabus(string $syllabus): self
    {
        $this->syllabus = $syllabus;

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
            $certificationModule->setModule($this);
        }

        return $this;
    }

    public function removeCertificationModule(CertificationModule $certificationModule): self
    {
        if ($this->certificationModules->contains($certificationModule)) {
            $this->certificationModules->removeElement($certificationModule);
            // set the owning side to null (unless already changed)
            if ($certificationModule->getModule() === $this) {
                $certificationModule->setModule(null);
            }
        }

        return $this;
    }

    public function getMinVote(): ?int
    {
        return $this->minVote;
    }

    public function setMinVote(?int $minVote): self
    {
        $this->minVote = $minVote;

        return $this;
    }

    public function __toString()
    {
        return $this->nome;
    }
}
