<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cards
 *
 * @ORM\Table(name="cards", indexes={@ORM\Index(name="fk_type", columns={"card_type"}), @ORM\Index(name="fk_rarity", columns={"card_rarity"}), @ORM\Index(name="fk_color", columns={"card_color"}), @ORM\Index(name="fk_set", columns={"card_set"})})
 * @ORM\Entity(repositoryClass="App\Repository\CardsRepository")
 */
class Cards
{
    /**
     * @var int
     *
     * @ORM\Column(name="card_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $cardId;

    /**
     * @var string
     *
     * @ORM\Column(name="card_name", type="string", length=100, nullable=false)
     */
    private $cardName;

    /**
     * @var int
     *
     * @ORM\Column(name="card_set_num", type="integer", nullable=false)
     */
    private $cardSetNum;

    /**
     * @var string|null
     *
     * @ORM\Column(name="card_alter", type="string", length=1, nullable=true, options={"fixed"=true})
     */
    private $cardAlter;

    /**
     * @var string
     *
     * @ORM\Column(name="card_image", type="string", length=100, nullable=false)
     */
    private $cardImage;

    /**
     * @var \Colors
     *
     * @ORM\ManyToOne(targetEntity="Colors", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="card_color", referencedColumnName="id")
     * })
     */
    private $cardColor;

    /**
     * @var \Rarities
     *
     * @ORM\ManyToOne(targetEntity="Rarities", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="card_rarity", referencedColumnName="id")
     * })
     */
    private $cardRarity;

    /**
     * @var \Sets
     *
     * @ORM\ManyToOne(targetEntity="Sets", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="card_set", referencedColumnName="id")
     * })
     */
    private $cardSet;

    /**
     * @var \Types
     *
     * @ORM\ManyToOne(targetEntity="Types", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="card_type", referencedColumnName="id")
     * })
     */
    private $cardType;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comments", mappedBy="postedOnCard")
     */
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }


    public function getCardId(): ?int
    {
        return $this->cardId;
    }

    public function getCardName(): ?string
    {
        return $this->cardName;
    }

    public function setCardName(string $cardName): self
    {
        $this->cardName = $cardName;

        return $this;
    }

    public function getCardSetNum(): ?int
    {
        return $this->cardSetNum;
    }

    public function setCardSetNum(int $cardSetNum): self
    {
        $this->cardSetNum = $cardSetNum;

        return $this;
    }

    public function getCardAlter(): ?string
    {
        return $this->cardAlter;
    }

    public function setCardAlter(?string $cardAlter): self
    {
        $this->cardAlter = $cardAlter;

        return $this;
    }

    public function getCardImage(): ?string
    {
        return $this->cardImage;
    }

    public function setCardImage(string $cardImage): self
    {
        $this->cardImage = $cardImage;

        return $this;
    }

    public function getCardColor(): ?Colors
    {
        return $this->cardColor;
    }

    public function setCardColor(?Colors $cardColor): self
    {
        $this->cardColor = $cardColor;

        return $this;
    }

    public function getCardRarity(): ?Rarities
    {
        return $this->cardRarity;
    }

    public function setCardRarity(?Rarities $cardRarity): self
    {
        $this->cardRarity = $cardRarity;

        return $this;
    }

    public function getCardSet(): ?Sets
    {
        return $this->cardSet;
    }

    public function setCardSet(?Sets $cardSet): self
    {
        $this->cardSet = $cardSet;

        return $this;
    }

    public function getCardType(): ?Types
    {
        return $this->cardType;
    }

    public function setCardType(?Types $cardType): self
    {
        $this->cardType = $cardType;

        return $this;
    }

    /**
     * @return Collection|Comments[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPostedOnCard($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getPostedOnCard() === $this) {
                $comment->setPostedOnCard(null);
            }
        }

        return $this;
    }

}
