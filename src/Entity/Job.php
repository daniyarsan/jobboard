<?php

namespace App\Entity;

use App\Service\Data\States;
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
     * @ORM\Column(type="boolean", nullable=true, name="featured")
     */
    private $featured;

    /**
     * @ORM\Column(type="boolean", nullable=true, name="active")
     */
    private $active;

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
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="jobs")
     */
    private $categories;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Discipline", inversedBy="jobs")
     */
    private $discipline;

    private $categoryString;

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    private $refId;


    public function __construct()
    {
        $this->categories = new ArrayCollection();
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


    /**
     * @return mixed
     */
    public function getFeatured()
    {
        return $this->featured;
    }

    /**
     * @param mixed $featured
     */
    public function setFeatured($featured): void
    {
        $this->featured = $featured;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active): void
    {
        $this->active = $active;
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
    public function setState($state)
    {
        if (preg_match('/^[A-Z]{2}$/', $state)) {
            $this->state = (States::list())[ $state ];
        } else {
            $this->state = $state;
        }
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

    public function getExtraFields()
    {
        return $this->extraFields;
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

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function setCategories($categories): self
    {
        $this->setCategoryString($categories);

        return $this;
    }

    public function setCategoriesCollection(Collection $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCategoryString()
    {
        return $this->categoryString;
    }

    /**
     * @param mixed $categoryString
     */
    public function setCategoryString($categoryString): void
    {
        $this->categoryString = $categoryString;
    }

    public function getDiscipline()
    {
        return $this->discipline;
    }

    public function setDiscipline($discipline): self
    {
        $this->discipline = $discipline;

        return $this;
    }

    public function getLocationString()
    {
        return sprintf('%s, %s', $this->country, $this->state);
    }

    public function getRefId(): ?string
    {
        return $this->refId;
    }

    public function setRefId(?string $refId): self
    {
        $this->refId = $refId;

        return $this;
    }

}
