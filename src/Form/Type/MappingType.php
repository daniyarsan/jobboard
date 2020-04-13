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
        $builder->setRequired(false);

        $fields = $this->fieldRepository->findAllFieldIds();

        /* Get fields to display on page */
        $feed = $this->feedRepository->find($options[ 'feedId' ]);
        $importFields = $feed->getMapperDefault();

        if (!empty($fields)) {
            foreach ($fields as $field) {
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