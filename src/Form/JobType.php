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
            ->add('name', TextType::class)
            ->add('country', CountryType::class)
            ->add('salary', IntegerType::class, [
                'required' => false,
            ])
            ->add('categories', EntityType::class, [
                'class' => 'App\Entity\Category',
                'multiple' => true,
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'wysiwyg'],
            ])
            ->add('contract', EntityType::class, [
                'class' => 'App\Entity\Contract',
                'required' => false
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'user' => null,
            'data_class' => 'App\Entity\Job',
        ]);
    }
}
