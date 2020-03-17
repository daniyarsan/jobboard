<?php

namespace App\Entity;

use App\Service\View\DataTransformer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\JobRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Job
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="jobs")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id", onDelete="cascade")
     */
    private $company;


    /**
     * @ORM\Column(type="string", length=100)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    private $state;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=0, nullable=true)
     */
    private $salary;

    /**
     * @ORM\Column(type="boolean", nullable=true, name="is_featured")
     */
    private $isFeatured;

    /**
     * @ORM\Column(type="datetime", nullable=true, name="featured_until")
     */
    private $featuredUntil;

    /**
     * @ORM\Column(type="boolean", nullable=true, name="is_published")
     */
    private $isPublished;

    /**
     * @ORM\Column(type="datetime", nullable=true, name="published_until")
     */
    private $publishedUntil;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $extraFields = [];

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    private $feedId;

    /**
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(name="modified", type="datetime", nullable=true)
     */
    private $modified;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Location", cascade={"persist", "remove"})
     */
    private $location;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $categories = [];


    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->created = new \DateTime('now');
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->modified = new \DateTime('now');
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    public function getSalary()
    {
        return $this->salary;
    }

    public function setSalary($salary): self
    {
        $this->salary = $salary;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsFeatured()
    {
        return $this->isFeatured;
    }

    /**
     * @param mixed $isFeatured
     */
    public function setIsFeatured($isFeatured): void
    {
        $this->isFeatured = $isFeatured;
    }

    /**
     * @return mixed
     */
    public function getFeaturedUntil()
    {
        return $this->featuredUntil;
    }

    /**
     * @param mixed $featuredUntil
     */
    public function setFeaturedUntil($featuredUntil): void
    {
        $this->featuredUntil = $featuredUntil;
    }

    /**
     * @return mixed
     */
    public function getIsPublished()
    {
        return $this->isPublished;
    }

    /**
     * @param mixed $isPublished
     */
    public function setIsPublished($isPublished): void
    {
        $this->isPublished = $isPublished;
    }

    /**
     * @return mixed
     */
    public function getPublishedUntil()
    {
        return $this->publishedUntil;
    }

    /**
     * @param mixed $publishedUntil
     */
    public function setPublishedUntil($publishedUntil): void
    {
        $this->publishedUntil = $publishedUntil;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     */
    public function setCreated($created): void
    {
        $this->created = $created;
    }

    /**
     * @return mixed
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @param mixed $modified
     */
    public function setModified($modified): void
    {
        $this->modified = $modified;
    }



    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $company
     */
    public function setCompany($company): void
    {
        $this->company = $company;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state): void
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getFeedId()
    {
        return $this->feedId;
    }

    /**
     * @param mixed $feedId
     */
    public function setFeedId($feedId): void
    {
        $this->feedId = $feedId;
    }

    /**
     * __set and __get is for dynamically create fields and add values to Job Entity
     */
    public function __get($name)
    {
        return isset($this->extraFields[$name]) ? $this->extraFields[$name] : false;
    }
    public function __set($name, $value)
    {
        $this->extraFields[$name] = $value;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function activateJob(): self
    {
        $this->setIsPublished(true);

        return $this;
    }

    public function getCategories(): ?array
    {
        return $this->categories;
    }

    public function setCategories($categories): self
    {
        if(is_array($categories)) {
            $this->categories = $categories;
        } else {
            $this->setCategory($categories);
        }

        return $this;
    }

    public function setCategory(string $category): self
    {
        array_push($this->categories, $category);

        return $this;
    }

}
