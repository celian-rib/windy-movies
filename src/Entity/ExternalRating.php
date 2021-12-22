<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExternalRating
 *
 * @ORM\Table(name="external_rating", indexes={@ORM\Index(name="IDX_AC0AB9CB5278319C", columns={"series_id"}), @ORM\Index(name="IDX_AC0AB9CB953C1C61", columns={"source_id"})})
 * @ORM\Entity
 */
class ExternalRating
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
     * @ORM\Column(name="value", type="string", length=255, nullable=false)
     */
    private $value;

    /**
     * @var int|null
     *
     * @ORM\Column(name="votes", type="integer", nullable=true)
     */
    private $votes;

    /**
     * @var \ExternalRatingSource
     *
     * @ORM\ManyToOne(targetEntity="ExternalRatingSource")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="source_id", referencedColumnName="id")
     * })
     */
    private $source;

    /**
     * @var \Series
     *
     * @ORM\ManyToOne(targetEntity="Series")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="series_id", referencedColumnName="id")
     * })
     */
    private $series;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getVotes(): ?int
    {
        return $this->votes;
    }

    public function setVotes(?int $votes): self
    {
        $this->votes = $votes;

        return $this;
    }

    public function getSource(): ?ExternalRatingSource
    {
        return $this->source;
    }

    public function setSource(?ExternalRatingSource $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getSeries(): ?Series
    {
        return $this->series;
    }

    public function setSeries(?Series $series): self
    {
        $this->series = $series;

        return $this;
    }


}
