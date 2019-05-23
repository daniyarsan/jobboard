<?php

namespace App\Form;

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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
            ])
            ->add('contract', EntityType::class, [
                'placeholder' => 'Choose Contract',
                'class' => 'App\Entity\Contract',
                'required' => false
            ])
            ->add('save', SubmitType::class)
            ->add('saveAndExit', SubmitType::class, ['label' => 'Save and Exit']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'user' => null,
            'data_class' => 'App\Entity\Job',
        ]);
    }
}
