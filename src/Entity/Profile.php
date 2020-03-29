<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProfileRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Profile
{
    const VISIBILITY_PUBLIC = 'PUBLIC';
    const VISIBILITY_AUTHENTICATED = 'AUTHENTICATED';
    const VISIBILITY_PRIVATE = 'PRIVATE';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="profile", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(name="type", type="string")
     */
    private $visibility = self::VISIBILITY_PUBLIC;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $avatarName;

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", name="email", length=190, nullable=true)
     */
    private $email;


    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $phone;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Education", mappedBy="profile", cascade={"persist"}, orphanRemoval=true)
     */
    private $educations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Experience", mappedBy="profile", cascade={"persist"}, orphanRemoval=true)
     */
    private $experiences;

    /**
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(name="modified", type="datetime", nullable=true)
     */
    private $modified;

    /**
     * @ORM\Column(type="string", length=190)
     */
    private $licenseState;

    /**
     * @ORM\Column(type="string", length=190)
     */
    private $license;

    /**
     * @ORM\Column(type="string", length=190)
     */
    private $specialty;

    /**
     * @ORM\Column(type="string", length=190)
     */
    private $specialtySecond;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $experienceYears;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $zipcode;

    public function __construct()
    {
        $this->educations = new ArrayCollection();
        $this->experiences = new ArrayCollection();
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getEducations()
    {
        return $this->educations;
    }

    /**
     * @param mixed $educations
     */
    public function setEducations($educations)
    {
        $this->educations = $educations;
    }

    public function addEducation(Education $education): void
    {
        $this->educations->add($education);
        $education->setProfile($this);
    }

    public function addExperience(Experience $experience): void
    {
        $this->experiences->add($experience);
        $experience->setProfile($this);
    }

    /**
     * @return mixed
     */
    public function getExperiences()
    {
        return $this->experiences;
    }

    /**
     * @param mixed $experiences
     */
    public function setExperiences($experiences)
    {
        $this->experiences = $experiences;
    }

    public function setDefaultRole()
    {
        $this->getUser()->setRoles(['ROLE_USER']);
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
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * @param mixed $visibility
     */
    public function setVisibility($visibility): void
    {
        $this->visibility = $visibility;
    }


    public function getFullName()
    {
        if (!empty($this->firstName) || !empty($this->lastName)) {
            return sprintf('%s %s', $this->firstName, $this->lastName);
        }

        return '';
    }

    public function getInitials()
    {
        if (!empty($this->firstName) && !empty($this->lastName)) {
            return $this->firstName[0] . $this->lastName[0];
        }

        return $this->getUser()->getUsername()[0];
    }

    public function getUsername()
    {
        return $this->getUser()->getUserName();
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getAvatarName()
    {
        return $this->avatarName;
    }

    public function getAvatarPath(): string
    {
        return $this->avatarName;
    }

    /**
     * @param mixed $avatarName
     */
    public function setAvatarName($avatarName): void
    {
        $this->avatarName = $avatarName;
    }


    public function __toString()
    {
        return self::class;
    }

    public function getLicenseState(): ?string
    {
        return $this->licenseState;
    }

    public function setLicenseState(string $licenseState): self
    {
        $this->licenseState = $licenseState;

        return $this;
    }

    public function getLicense(): ?string
    {
        return $this->license;
    }

    public function setLicense(string $license): self
    {
        $this->license = $license;

        return $this;
    }

    public function getSpecialty(): ?string
    {
        return $this->specialty;
    }

    public function setSpecialty(string $specialty): self
    {
        $this->specialty = $specialty;

        return $this;
    }

    public function getSpecialtySecond(): ?string
    {
        return $this->specialtySecond;
    }

    public function setSpecialtySecond(string $specialtySecond): self
    {
        $this->specialtySecond = $specialtySecond;

        return $this;
    }

    public function getExperienceYears(): ?string
    {
        return $this->experienceYears;
    }

    public function setExperienceYears(?string $experienceYears): self
    {
        $this->experienceYears = $experienceYears;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(?string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }


}
