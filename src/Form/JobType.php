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
            ->add('title', TextType::class)
            ->add('location', TextType::class)
            ->add('categories', EntityType::class, [
                'placeholder' => 'Choose Category',
                'class' => 'App\Entity\Category',
                'multiple' => true
            ])
            ->add('description', TextareaType::class);

        /* Adding custom fields to form */
        foreach ($jobFields as $jobField) {
            $builder->add($jobField->getFieldId(), Field::FIELD_TYPE[ $jobField->getType() ], [
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Job',
            'user' => false
        ]);
    }
}
