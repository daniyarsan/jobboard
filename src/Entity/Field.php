<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FieldRepository")
 */
class Field
{
    public const FIELD_TYPE = [
        'text' => TextType::class,
        'select' => ChoiceType::class,
        'textarea' => TextareaType::class,
        'checkbox' => CheckboxType::class,
        'country' => CountryType::class,
    ];

    public const LISTING_TYPE = [
        'job',
        'resume'
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=190)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $field_id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FieldItems", mappedBy="field_id", orphanRemoval=true)
     */
    private $fieldItems;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isSystem;

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    private $listingType;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $inFilter;


    public function __construct()
    {
        $this->fieldItems = new ArrayCollection();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getFieldId(): ?string
    {
        return $this->field_id;
    }

    public function setFieldId(string $field_id): self
    {
        $this->field_id = $field_id;

        return $this;
    }

    /**
     * @return Collection|FieldItems[]
     */
    public function getFieldItems(): Collection
    {
        return $this->fieldItems;
    }

    public function addFieldItem(FieldItems $fieldItem): self
    {
        if (!$this->fieldItems->contains($fieldItem)) {
            $this->fieldItems[] = $fieldItem;
            $fieldItem->setFieldId($this);
        }

        return $this;
    }

    public function removeFieldItem(FieldItems $fieldItem): self
    {
        if ($this->fieldItems->contains($fieldItem)) {
            $this->fieldItems->removeElement($fieldItem);
            // set the owning side to null (unless already changed)
            if ($fieldItem->getFieldId() === $this) {
                $fieldItem->setFieldId(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getIsSystem(): ?bool
    {
        return $this->isSystem;
    }

    public function setIsSystem(?bool $isSystem): self
    {
        $this->isSystem = $isSystem;

        return $this;
    }

    public function getListingType(): ?string
    {
        return $this->listingType;
    }

    public function setListingType(?string $listingType): self
    {
        $this->listingType = $listingType;

        return $this;
    }

    public function getInFilter(): ?bool
    {
        return $this->inFilter;
    }

    public function setInFilter(bool $inFilter): self
    {
        $this->inFilter = $inFilter;

        return $this;
    }
}
