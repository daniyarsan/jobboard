<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @ORM\ManyToOne(targetEntity="Contract", inversedBy="jobs")
     * @ORM\JoinColumn(name="contract_id", referencedColumnName="id")
     */
    private $contract;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="jobs")
     * @ORM\JoinTable(name="job_category")
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="Favorite", mappedBy="job")
     */
    private $favorites;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $country;

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
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(name="modified", type="datetime", nullable=true)
     */
    private $modified;

    public function __construct()
    {
        $this->favorites = new ArrayCollection();
    }

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
    public function getisPublished()
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
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param mixed $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
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
    public function getContract()
    {
        return $this->contract;
    }

    /**
     * @param mixed $contract
     */
    public function setContract($contract): void
    {
        $this->contract = $contract;
    }

}