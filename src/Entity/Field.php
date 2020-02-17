<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FieldRepository")
 */
class Field
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
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

    public function getFieldId(): ?int
    {
        return $this->field_id;
    }

    public function setFieldId(int $field_id): self
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
}
