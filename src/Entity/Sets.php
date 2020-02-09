<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sets
 *
 * @ORM\Table(name="sets", indexes={@ORM\Index(name="fk_block", columns={"block"})})
 * @ORM\Entity(repositoryClass="App\Repository\SetsRepository")
 */
class Sets
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="year", type="string", length=10, nullable=false)
     */
    private $year;

    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="string", length=100, nullable=false)
     */
    private $icon;

    /**
     * @var \Blocks
     *
     * @ORM\ManyToOne(targetEntity="Blocks", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="block", referencedColumnName="id")
     * })
     */
    private $block;

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

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(string $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getBlock(): ?Blocks
    {
        return $this->block;
    }

    public function setBlock(?Blocks $block): self
    {
        $this->block = $block;

        return $this;
    }


}
