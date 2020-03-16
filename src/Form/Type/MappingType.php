<?php

namespace App\Form\Type;

use App\Repository\FeedRepository;
use App\Repository\FieldRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MappingType extends AbstractType
{
    protected $em;
    protected $fieldRepository;
    protected $feedRepository;

    public function __construct(EntityManagerInterface $entityManager,
                                FieldRepository $fieldRepository,
                                FeedRepository $feedRepository)
    {
        $this->em = $entityManager;
        $this->feedRepository = $feedRepository;
        $this->fieldRepository = $fieldRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /* Make it not required */
        $builder->setRequired(false);

        /* Get fields for values */
        $fields = $this->em->getClassMetadata('App:Job')->getColumnNames();
        $dynamicFields = $this->fieldRepository->findAllFieldIds();
        $fieldsCollection = array_merge($fields, $dynamicFields);

        /* Get fields to display on page */
        $feed = $this->feedRepository->find($options[ 'feedId' ]);
        $importFields = $feed->getMapperDefault();

        if (!empty($fieldsCollection)) {
            foreach ($fieldsCollection as $field) {
                $builder
                    ->add($field, ChoiceType::class, [
                        'choices' => $importFields,
                        'placeholder' => 'Select Field to Bind',
                        'choice_label' => function ($choice) {
                            return ucfirst($choice);
                        }
                    ]);
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