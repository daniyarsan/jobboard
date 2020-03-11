<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LocationRepository")
 */
class Location
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    private $state;

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    private $lon;

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    private $lat;

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    private $googleLocation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getLon(): ?string
    {
        return $this->lon;
    }

    public function setLon(?string $lon): self
    {
        $this->lon = $lon;

        return $this;
    }

    public function getLat(): ?string
    {
        return $this->lat;
    }

    public function setLat(?string $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getGoogleLocation(): ?string
    {
        return $this->googleLocation;
    }

    public function setGoogleLocation(?string $googleLocation): self
    {
        $this->googleLocation = $googleLocation;

        return $this;
    }
}
