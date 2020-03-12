<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 *  @UniqueEntity(
 *     fields={"email"},
 *     message="This email address is already in use.")
 */
class User implements UserInterface
{
    public const ROLE_PROFILE = 'ROLE_PROFILE';
    public const ROLE_COMPANY = 'ROLE_COMPANY';
    public const DEMO_PASSWORD = '121212';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Profile", mappedBy="user", cascade={"persist", "remove"})
     **/
    private $profile;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Company", mappedBy="user", cascade={"persist", "remove"})
     */
    private $company;

    /**
     * @ORM\Column(type="boolean", nullable=true, name="is_verified")
     */
    private $isVerified;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var String
     */
    protected $plainPassword;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $confirmationCode;


    public function __construct()
    {
        $this->roles = [self::ROLE_PROFILE];
        $this->enabled = false;
        $this->companies = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $companies
     */
    public function setCompany($company)
    {
        $this->company = $company;
        $this->company->setUser($this);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param mixed $profile
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;
        $this->profile->setUser($this);
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    /**
     * @return mixed
     */
    public function getisVerified(): bool
    {
        return $this->isVerified;
    }

    /**
     * @param mixed $isVerified
     */
    public function setIsVerified($isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return string
     */
    public function getConfirmationCode(): string
    {
        return $this->confirmationCode;
    }

    /**
     * @param string $confirmationCode
     */
    public function setConfirmationCode(string $confirmationCode): void
    {
        $this->confirmationCode = $confirmationCode;
    }

    public function hasRole(string $role)
    {
        return in_array($role, $this->roles) ? true : false;
    }
}
