<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * * @ORM\Table(
 *     uniqueConstraints={ @ORM\UniqueConstraint(name="assignment_unique", columns={"comment_id", "user_id"}) }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\FlaggedByRepository").
 * @UniqueEntity(
 *     fields={"user_id", "comment_id"},
 *     errorPath="user_id",
 *     message="Vous avez déjà signalé ce commentaire."
 * )
 */
class FlaggedBy
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\comments", inversedBy="FlaggedByUsers")
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?comments
    {
        return $this->comment;
    }

    public function setComment(?comments $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getUser(): ?users
    {
        return $this->user;
    }

    public function setUser(?users $user): self
    {
        $this->user = $user;

        return $this;
    }
}
