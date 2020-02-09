<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(
 *     uniqueConstraints={ @ORM\UniqueConstraint(name="assignment_unique", columns={"cards_id", "from_wishlist_id"}) }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\WishlistContentRepository")
 */
class WishlistContent
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Cards", fetch="EAGER")
     * @ORM\JoinColumn(referencedColumnName="card_id")
     */
    private $cards;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Wishlists", inversedBy="wishlistContents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fromWishlist;

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

    public function getFromWishlist(): ?wishlists
    {
        return $this->fromWishlist;
    }

    public function setFromWishlist(?wishlists $fromWishlist): self
    {
        $this->fromWishlist = $fromWishlist;

        return $this;
    }
}
