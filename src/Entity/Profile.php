<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProfileRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 */
class Profile implements \Serializable
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
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="profile", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(name="type", type="string")
     */
    private $visibility = self::VISIBILITY_PUBLIC;

    /**
     * @Assert\File(mimeTypes={"image/png", "image/jpeg", "image/pjpeg"})
     * @Vich\UploadableField(mapping="avatar_image", fileNameProperty="avatar_name")
     */
    private $avatarImage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="avatar_name")
     */
    private $avatarName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
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

    public function __toString()
    {
        return self::class;
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
        if (!empty($this->firstName) && !empty($this->lastName)) {
            return sprintf('%s %s', $this->firstName, $this->lastName);
        }

        return $this->getUser()->getUsername();
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
    public function getAvatarImage()
    {
        return $this->avatarImage;
    }

    /**
     * @param mixed $avatarImage
     */
    public function setAvatarImage(File $avatarImage)
    {
        $this->avatarImage = $avatarImage;

        if ($avatarImage) {
            $this->modified = new \DateTime('now');
        }
    }

    /**
     * @return mixed
     */
    public function getAvatarName()
    {
        return $this->avatarName;
    }

    /**
     * @param mixed $avatarName
     */
    public function setAvatarName($avatarName)
    {
        $this->avatarName = $avatarName;
    }

    /**
     * String representation of object
     * @link https://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        $this->avatarImage = base64_encode($this->avatarImage);
    }

    /**
     * Constructs the object
     * @link https://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        $this->avatarImage = base64_decode($this->avatarImage);
    }
}
