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
     * @ORM\Column(type="string", name="email", length=190)
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
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    private $license;

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    private $specialty;

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    private $specialtySecond;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $experienceYears;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $experienceYearsSecond;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $licenseState = [];

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    private $homeState;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hasExperience;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $destinationStates = [];

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $onAssignment;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $assignmentEndDate;

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    private $availability;

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    private $resumeFile;


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


    public function getLicenseState(): ?array
    {
        return $this->licenseState;
    }

    public function setLicenseState(?array $licenseState): self
    {
        $this->licenseState = $licenseState;

        return $this;
    }

    public function getHomeState(): ?string
    {
        return $this->homeState;
    }

    public function setHomeState(?string $homeState): self
    {
        $this->homeState = $homeState;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getExperienceYearsSecond()
    {
        return $this->experienceYearsSecond;
    }

    /**
     * @param mixed $experienceYearsSecond
     */
    public function setExperienceYearsSecond($experienceYearsSecond): void
    {
        $this->experienceYearsSecond = $experienceYearsSecond;
    }

    public function getHasExperience(): ?bool
    {
        return $this->hasExperience;
    }

    public function setHasExperience(?bool $hasExperience): self
    {
        $this->hasExperience = $hasExperience;

        return $this;
    }

    public function getDestinationStates(): ?array
    {
        return $this->destinationStates;
    }

    public function setDestinationStates(?array $destinationStates): self
    {
        $this->destinationStates = $destinationStates;

        return $this;
    }

    public function getOnAssignment(): ?bool
    {
        return $this->onAssignment;
    }

    public function setOnAssignment(?bool $onAssignment): self
    {
        $this->onAssignment = $onAssignment;

        return $this;
    }

    public function getAssignmentEndDate(): ?\DateTimeInterface
    {
        return $this->assignmentEndDate;
    }

    public function setAssignmentEndDate(?\DateTimeInterface $assignmentEndDate): self
    {
        $this->assignmentEndDate = $assignmentEndDate;

        return $this;
    }

    public function getAvailability(): ?string
    {
        return $this->availability;
    }

    public function setAvailability(?string $availability): self
    {
        $this->availability = $availability;

        return $this;
    }

    public function getResumeFile(): ?string
    {
        return $this->resumeFile;
    }

    public function setResumeFile(?string $resumeFile): self
    {
        $this->resumeFile = $resumeFile;

        return $this;
    }

}
