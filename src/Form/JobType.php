<?php

namespace App\Form;

use App\Entity\Field;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobType extends AbstractType
{
    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $fieldRepository = $this->em->getRepository('App:Field');
        $jobFields = $fieldRepository->findAll();

        $builder
            ->add('company', EntityType::class, [
                'placeholder' => 'Choose Company',
                'class' => 'App\Entity\Company',
                'required' => false,
            ])
            ->add('title', TextType::class)
            ->add('country', CountryType::class, [
                'empty_data' => 'Choose Country',
            ])
            ->add('state', TextType::class)
            ->add('salary', IntegerType::class, [
                'required' => false,
            ])
            ->add('categories', EntityType::class, [
                'placeholder' => 'Choose Category',
                'class' => 'App\Entity\Category',
                'multiple' => true,
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'required' => false
            ]);

        /* Adding custom fields to form */
        foreach ($jobFields as $jobField) {
            $builder->add($jobField->getFieldId(), Field::FIELD_TYPE[ $jobField->getType() ], [
                'mapped' => false
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Job'
        ]);
    }
}
