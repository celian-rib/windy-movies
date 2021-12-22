<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Genre
 *
 * @ORM\Table(name="genre")
 * @ORM\Entity
 */
class Genre
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
     * @ORM\Column(name="name", type="string", length=128, nullable=false)
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Series", inversedBy="genre")
     * @ORM\JoinTable(name="genre_series",
     *   joinColumns={
     *     @ORM\JoinColumn(name="genre_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="series_id", referencedColumnName="id")
     *   }
     * )
     */
    private $series;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->series = new \Doctrine\Common\Collections\ArrayCollection();
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

    /**
     * @return Collection|Series[]
     */
    public function getSeries(): Collection
    {
        return $this->series;
    }

    public function addSeries(Series $series): self
    {
        if (!$this->series->contains($series)) {
            $this->series[] = $series;
        }

        return $this;
    }

    public function removeSeries(Series $series): self
    {
        $this->series->removeElement($series);

        return $this;
    }

}
