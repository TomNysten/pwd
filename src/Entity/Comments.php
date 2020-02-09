<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentsRepository")
 */
class Comments
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $auteur;

    /**
     * @ORM\Column(type="datetime")
     */
    private $postedAt;

    /**
     * * @Assert\Length(
     *      min = 3,
     *      max = 190,
     *      minMessage = "Votre commentaire doit au moins contenir {{ limit }} caractères.",
     *      maxMessage = "Votre commentaire ne peut pas dépasser {{ limit }} caractères."
     * )
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="boolean", options={"default" : 0})
     */
    private $validated;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Cards", inversedBy="comments")
     * @ORM\JoinColumn(referencedColumnName="card_id")
     */
    private $postedOnCard;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="commentsOnUser", fetch="EAGER")
     */
    private $postedOnUser;

    /**
     * @ORM\Column(type="boolean", options={"default" : 0})
     */
    private $deleted;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FlaggedBy", mappedBy="comment")
     */
    private $FlaggedByUsers;

    public function __construct()
    {
        $this->FlaggedByUsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuteur(): ?users
    {
        return $this->auteur;
    }

    public function setAuteur(?users $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getPostedAt(): ?\DateTimeInterface
    {
        return $this->postedAt;
    }

    public function setPostedAt(\DateTimeInterface $postedAt): self
    {
        $this->postedAt = $postedAt;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getValidated(): ?bool
    {
        return $this->validated;
    }

    public function setValidated(bool $validated): self
    {
        $this->validated = $validated;

        return $this;
    }

    public function getPostedOnCard(): ?cards
    {
        return $this->postedOnCard;
    }

    public function setPostedOnCard(?cards $postedOnCard): self
    {
        $this->postedOnCard = $postedOnCard;

        return $this;
    }

    public function getPostedOnUser(): ?users
    {
        return $this->postedOnUser;
    }

    public function setPostedOnUser(?users $postedOnUser): self
    {
        $this->postedOnUser = $postedOnUser;

        return $this;
    }

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * @return Collection|FlaggedBy[]
     */
    public function getFlaggedByUsers(): Collection
    {
        return $this->FlaggedByUsers;
    }

    public function addFlaggedByUser(FlaggedBy $flaggedByUser): self
    {
        if (!$this->FlaggedByUsers->contains($flaggedByUser)) {
            $this->FlaggedByUsers[] = $flaggedByUser;
            $flaggedByUser->setComment($this);
        }

        return $this;
    }

    public function removeFlaggedByUser(FlaggedBy $flaggedByUser): self
    {
        if ($this->FlaggedByUsers->contains($flaggedByUser)) {
            $this->FlaggedByUsers->removeElement($flaggedByUser);
            // set the owning side to null (unless already changed)
            if ($flaggedByUser->getComment() === $this) {
                $flaggedByUser->setComment(null);
            }
        }

        return $this;
    }
}
