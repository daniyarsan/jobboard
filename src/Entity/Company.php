<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CompanyRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Company
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="company", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="Job", mappedBy="company", cascade={"persist"})
     */
    private $jobs;

    /**
     * @ORM\OneToMany(targetEntity="Feed", mappedBy="company", cascade={"persist"})
     */
    private $feeds;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $logoName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    private $website;

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isVerified;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(name="modified", type="datetime", nullable=true)
     */
    private $modified;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Location", mappedBy="company", cascade={"persist", "remove"})
     */
    private $location;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $glassdoor = [];

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    private $youtube;

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

    public function __construct()
    {
        $this->jobs = new ArrayCollection();
        $this->feeds = new ArrayCollection();
        $this->companies = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    public function initUser(User $user, UserPasswordEncoderInterface $passwordEncoder): void
    {
        $user->setEmail($this->getEmail())
            ->setRoles([User::ROLE_COMPANY]);
        $password = $passwordEncoder->encodePassword($user, User::DEMO_PASSWORD);
        $user->setPassword($password);
        $this->setUser($user);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name ?? '';
    }

    public function setName(?string $name): self
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }


    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function getLocationString()
    {
        return $this->location ? $this->location->getAddressString() : false;
    }
    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(?bool $isVerified): self
    {
        if (!isset($isVerified)) {
            $isVerified = 0;
        }

        $this->isVerified = $isVerified;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address): void
    {
        $this->address = $address;
    }

    public function setRole()
    {
        $this->getUser()->setRoles(['ROLE_COMPANY']);
    }

    /**
     * @return mixed
     */
    public function getJobs()
    {
        return $this->jobs;
    }

    /**
     * @param mixed $jobs
     */
    public function setJobs($jobs): void
    {
        $this->jobs = $jobs;
    }

    /**
     * @return mixed
     */
    public function getFeeds()
    {
        return $this->feeds;
    }

    /**
     * @param mixed $jobs
     */
    public function setFeeds($feeds): void
    {
        $this->feeds = $feeds;
    }

    public function getUsername()
    {
        return $this->getUser()->getUserName();
    }

    /**
     * @return mixed
     */
    public function getLogoName()
    {
        return $this->logoName;
    }

    /**
     * @param mixed $logoName
     */
    public function setLogoName($logoName): void
    {
        $this->logoName = $logoName;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        // set (or unset) the owning side of the relation if necessary
        $newCompany = null === $location ? null : $this;
        if ($location->getCompany() !== $newCompany) {
            $location->setCompany($newCompany);
        }

        return $this;
    }

    public function getGlassdoor(): ?array
    {
        return $this->glassdoor;
    }

    public function setGlassdoor(?array $glassdoor): self
    {
        $this->glassdoor = $glassdoor;

        return $this;
    }

    public function getYoutube(): ?string
    {
        return $this->youtube;
    }

    public function setYoutube(?string $youtube): self
    {
        if ($youtube) {
            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $youtube, $match);
            $youtube = $match[1];
        }

        $this->youtube = $youtube;

        return $this;
    }
}
