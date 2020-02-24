<?php

namespace App\Form\Type;

use App\Repository\FieldRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class MappingType extends AbstractType
{
    protected $em;
    protected $fieldRepository;

    public function __construct(EntityManagerInterface $entityManager, FieldRepository $fieldRepository)
    {
        $this->em = $entityManager;
        $this->fieldRepository = $fieldRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setRequired(false);

        $feedRepo = $this->em->getRepository('App:Feed');
        $importFields = $feedRepo->findDefaultMappingFields();


        if (!empty($importFields)) {
            foreach (array_keys(unserialize($importFields[0])) as $field) {
                $builder
                    ->add($field, TextType::class);
            }
        }
    }
}

//[
//    'choices' => array_combine(
//        $this->fieldRepository->findAllFieldNames(),
//        $this->fieldRepository->findAllFieldIds())
//]