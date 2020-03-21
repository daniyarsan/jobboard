<?php

namespace App\Form\Type;

use App\DataTransformer\TextToArrayTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TagType extends AbstractType
{
    protected $em;
    protected $transformer;

    public function __construct(EntityManagerInterface $entityManager, TextToArrayTransformer $transformer)
    {
        $this->em = $entityManager;
        $this->transformer = $transformer;
    }

    public function getParent()
    {
        return TextType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->transformer);
    }
}