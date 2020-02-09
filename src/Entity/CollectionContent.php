<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * * @ORM\Table(
 *     uniqueConstraints={ @ORM\UniqueConstraint(name="assignment_unique", columns={"cards_id", "from_collection_id"}) }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\CollectionContentRepository")
 * @UniqueEntity(
 *     fields={"cards_id", "from_collection_id"},
 *     errorPath="cards_id",
 *     message="Cette carte existe déjà dans votre collection."
 * )
 */
class CollectionContent
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Cards")
     * @ORM\JoinColumn(referencedColumnName="card_id")
     */
    private $cards;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Collections", inversedBy="collectionContents", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fromCollection;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getCards(): ?cards
    {
        return $this->cards;
    }

    public function setCards(?cards $cards): self
    {
        $this->cards = $cards;

        return $this;
    }

    public function getFromCollection(): ?collections
    {
        return $this->fromCollection;
    }

    public function setFromCollection(?collections $fromCollection): self
    {
        $this->fromCollection = $fromCollection;

        return $this;
    }
}
