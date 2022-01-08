<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * Episode
 *
 * @ORM\Table(name="episode", indexes={@ORM\Index(name="IDX_DDAA1CDA4EC001D1", columns={"season_id"})})
 * @ORM\Entity
 */
class Episode
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
     * @ORM\Column(name="title", type="string", length=128, nullable=false)
     */
    private $title;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="imdb", type="string", length=128, nullable=false)
     */
    private $imdb;

    /**
     * @var float|null
     *
     * @ORM\Column(name="imdbrating", type="float", precision=10, scale=0, nullable=true)
     */
    private $imdbrating;

    /**
     * @var int
     *
     * @ORM\Column(name="number", type="integer", nullable=false)
     */
    private $number;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="episode")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Season::class, inversedBy="episodes")
     */
    private $season;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->user = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getImdb(): ?string
    {
        return $this->imdb;
    }
  
    public function getImdbLink(): ?string
    {
        return "https://www.imdb.com/title/" . $this->imdb;
    }

    public function setImdb(string $imdb): self
    {
        $this->imdb = $imdb;

        return $this;
    }

    public function getImdbrating(): ?float
    {
        return $this->imdbrating;
    }

    public function setImdbrating(?float $imdbrating): self
    {
        $this->imdbrating = $imdbrating;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
            $user->addEpisode($this);
        }

        return $this;
    }

    public function seenByUser(User $usr) {
        $sort = new Criteria();
        $sort->where(Criteria::expr()->eq('id', $usr->getId()));
        return count($this->user->matching($sort));
    }

    public function removeUser(User $user): self
    {
        if ($this->user->removeElement($user)) {
            $user->removeEpisode($this);
        }

        return $this;
    }

    public function getSeason(): ?Season
    {
        return $this->season;
    }

    public function setSeason(?Season $season): self
    {
        $this->season = $season;

        return $this;
    }

}
