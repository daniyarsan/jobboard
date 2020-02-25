<?php

namespace App\Form\Type;

use App\Entity\Feed;
use App\Repository\FieldRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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

        $feedRepo = $this->em->getRepository('App:Feed')->find($options['feedId']);
        $importFields = $feedRepo->getMapperDefault();

        if (!empty($importFields)) {
            foreach (array_keys($importFields) as $field) {
                $builder
                    ->add($field, TextType::class);
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'feedId' => null
        ]);
    }
}


//[
//    'choices' => array_combine(
//        $this->fieldRepository->findAllFieldNames(),
//        $this->fieldRepository->findAllFieldIds())
//]