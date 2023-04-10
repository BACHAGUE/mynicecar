<?php

namespace App\Entity;

use App\Repository\CommandeDetailRepository;
use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommandeRepository::class)
 */
class Commande
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateDeCommande;

    /**
     * @ORM\Column(type="float")
     */
    private $prix;

    /**
     * @ORM\OneToMany(targetEntity=Voiture::class, mappedBy="commande")
     */
    private $voiture;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="commandes")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=CommandeDetail::class, mappedBy="commande", orphanRemoval=true)
     */
    public $commandeDetails;

    public function __construct()
    {
        $this->voiture = new ArrayCollection();
        $this->commandeDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDeCommande(): ?\DateTimeInterface
    {
        return $this->dateDeCommande;
    }

    public function setDateDeCommande(\DateTimeInterface $dateDeCommande): self
    {
        $this->dateDeCommande = $dateDeCommande;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * @return Collection<int, Voiture>
     */
    public function getVoiture(): Collection
    {
        return $this->voiture;
    }

    public function addVoiture(Voiture $voiture): self
    {
        if (!$this->voiture->contains($voiture)) {
            $this->voiture[] = $voiture;
            $voiture->setCommande($this);
        }

        return $this;
    }

    public function removeVoiture(Voiture $voiture): self
    {
        if ($this->voiture->removeElement($voiture)) {
            // set the owning side to null (unless already changed)
            if ($voiture->getCommande() === $this) {
                $voiture->setCommande(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, CommandeDetail>
     */
    public function getCommandeDetails(): Collection
    {
        return $this->commandeDetails;
    }

    /**
     * @return Collection<CommandeDetail>
     */
    public function getCommandeDetailsObjects(CommandeDetailRepository $repo)
    {
        //dd($repo); exit();
        $details = $repo->findBy(['commande' => $this->getId()]);
        
        return $details;
    }

    public function addCommandeDetail(CommandeDetail $commandeDetail): self
    {
        if (!$this->commandeDetails->contains($commandeDetail)) {
            $this->commandeDetails[] = $commandeDetail;
            $commandeDetail->setCommande($this);
        }

        return $this;
    }

    public function removeCommandeDetail(CommandeDetail $commandeDetail): self
    {
        if ($this->commandeDetails->removeElement($commandeDetail)) {
            // set the owning side to null (unless already changed)
            if ($commandeDetail->getCommande() === $this) {
                $commandeDetail->setCommande(null);
            }
        }

        return $this;
    }
}
