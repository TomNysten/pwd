<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UsersRepository")
 * @UniqueEntity(
 *     fields={"email"},
 *     message="L'adresse mail est déjà utilisée."
 * )
 */
class Users implements UserInterface
{

    private const DEFAULT_IMAGE = "default.jpg";

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Length(min="5", minMessage="Votre pseudo doit faire plus de 4 caractères")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="6", minMessage="Votre mot de passe doit faire plus de 5 caractères")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Collections", mappedBy="users", orphanRemoval=true)
     */
    private $collection;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Wishlists", mappedBy="users")
     */
    private $wishlists;

    /**
     * @ORM\Column(type="datetime", options={"default" : "CURRENT_TIMESTAMP"})
     */
    private $registeredAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comments", mappedBy="auteur", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comments", mappedBy="postedOnUser")
     */
    private $commentsOnUser;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"default" : "default.jpg"})
     */
    private $image;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Wishlists", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $defaultWishlist;

    public function __construct()
    {
        //$this->collection = new ArrayCollection();
        $this->wishlists = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->commentsOnUser = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getRoles(): array
    {
        $role = $this->roles;
        // guarantee every user at least has ROLE_USER
        // $role[] = 'ROLE_USER';

        return array_unique($role);
    }

    public function setRole(array $role): self
    {
        $this->roles = $role;

        return $this;
    }

    public function getCollection(): ?collections
    {
        return $this->collection;
    }

    /* peut-être à retirer */
    public function addCollection(collections $collection): self
    {
        if (!$this->collection->contains($collection)) {
            $this->collection[] = $collection;
            $collection->setUsers($this);
        }

        return $this;
    }

    public function removeCollection(collections $collection): self
    {
        if ($this->collection->contains($collection)) {
            $this->collection->removeElement($collection);
            // set the owning side to null (unless already changed)
            if ($collection->getUsers() === $this) {
                $collection->setUsers(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|wishlists[]
     */
    public function getWishlists(): Collection
    {
        return $this->wishlists;
    }

    public function addWishlist(wishlists $wishlist): self
    {
        if (!$this->wishlists->contains($wishlist)) {
            $this->wishlists[] = $wishlist;
            $wishlist->setUsers($this);
        }

        return $this;
    }

    public function removeWishlist(wishlists $wishlist): self
    {
        if ($this->wishlists->contains($wishlist)) {
            $this->wishlists->removeElement($wishlist);
            // set the owning side to null (unless already changed)
            if ($wishlist->getUsers() === $this) {
                $wishlist->setUsers(null);
            }
        }

        return $this;
    }

    public function getRegisteredAt(): ?\DateTimeInterface
    {
        return $this->registeredAt;
    }

    public function setRegisteredAt(\DateTimeInterface $registeredAt): self
    {
        $this->registeredAt = $registeredAt;

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
            $comment->setAuteur($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAuteur() === $this) {
                $comment->setAuteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comments[]
     */
    public function getCommentsOnUser(): Collection
    {
        return $this->commentsOnUser;
    }

    public function addCommentsOnUser(Comments $commentsOnUser): self
    {
        if (!$this->commentsOnUser->contains($commentsOnUser)) {
            $this->commentsOnUser[] = $commentsOnUser;
            $commentsOnUser->setPostedOnUser($this);
        }

        return $this;
    }

    public function removeCommentsOnUser(Comments $commentsOnUser): self
    {
        if ($this->commentsOnUser->contains($commentsOnUser)) {
            $this->commentsOnUser->removeElement($commentsOnUser);
            // set the owning side to null (unless already changed)
            if ($commentsOnUser->getPostedOnUser() === $this) {
                $commentsOnUser->setPostedOnUser(null);
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getDefaultWishlist(): ?Wishlists
    {
        return $this->defaultWishlist;
    }

    public function setDefaultWishlist(Wishlists $defaultWishlist): self
    {
        $this->defaultWishlist = $defaultWishlist;

        return $this;
    }
}
