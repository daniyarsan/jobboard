<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FeedRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Feed
{

    public const UNIQUE_DISCIPLINES = 'disciplines';
    public const UNIQUE_SPECIALTIES = 'specialties';
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="feeds")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id", onDelete="cascade")
     */
    private $company;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="text")
     */
    private $url;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $mapper = [];

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $mapper_default = [];

    /**
     * @ORM\Column(type="text")
     */
    private $xml_text;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(name="modified", type="datetime", nullable=true)
     */
    private $modified;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $slug;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $activate;

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    private $defaultCountry;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $lastImport;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $metaUnique = [];

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

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getMapper()
    {
        return $this->mapper;
    }

    public function setMapper(array $mapper): self
    {
        $this->mapper = $mapper;

        return $this;
    }

    public function getMapperDefault(): ?array
    {
        return $this->mapper_default;
    }

    public function setMapperDefault(?array $mapper_default): self
    {
        $this->mapper_default = $mapper_default;

        return $this;
    }

    public function getXmlText(): ?string
    {
        return $this->xml_text;
    }

    public function setXmlText(string $xml_text): self
    {
        $this->xml_text = $xml_text;

        return $this;
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getActivate(): ?bool
    {
        return $this->activate;
    }

    public function setActivate(bool $activate): self
    {
        $this->activate = $activate;

        return $this;
    }

    public function getDefaultCountry(): ?string
    {
        return $this->defaultCountry;
    }

    public function setDefaultCountry(?string $defaultCountry): self
    {
        $this->defaultCountry = $defaultCountry;

        return $this;
    }

    public function getLastImport(): ?\DateTimeInterface
    {
        return $this->lastImport;
    }

    public function setLastImport(?\DateTimeInterface $lastImport): self
    {
        $this->lastImport = $lastImport;

        return $this;
    }

    public function getMetaUnique(): ?array
    {
        return $this->metaUnique;
    }

    public function setMetaUnique(?array $metaUnique): self
    {
        $this->metaUnique = $metaUnique;

        return $this;
    }
}
