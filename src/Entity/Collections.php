<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CollectionsRepository")
 */
class Collections
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Users", inversedBy="collection")
     * @ORM\JoinColumn(nullable=false)
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CollectionContent", mappedBy="fromCollection")
     */
    private $collectionContents;

    public function __construct()
    {
        $this->collectionContents = new ArrayCollection();
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

    public function getUsers(): ?Users
    {
        return $this->users;
    }

    public function setUsers(?Users $users): self
    {
        $this->users = $users;

        return $this;
    }

    /**
     * @return Collection|CollectionContent[]
     */
    public function getCollectionContents(): Collection
    {
        return $this->collectionContents;
    }

    public function addCollectionContent(CollectionContent $collectionContent): self
    {
        if (!$this->collectionContents->contains($collectionContent)) {
            $this->collectionContents[] = $collectionContent;
            $collectionContent->setFromCollection($this);
        }

        return $this;
    }

    public function removeCollectionContent(CollectionContent $collectionContent): self
    {
        if ($this->collectionContents->contains($collectionContent)) {
            $this->collectionContents->removeElement($collectionContent);
            // set the owning side to null (unless already changed)
            if ($collectionContent->getFromCollection() === $this) {
                $collectionContent->setFromCollection(null);
            }
        }

        return $this;
    }

}
