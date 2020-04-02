<?php

namespace App\Form;

use App\Entity\Education;
use App\Entity\Profile;
use App\Form\Type\StateType;
use Svg\Tag\Text;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CandidateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setRequired(true)
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('email', EmailType::class)
            ->add('phone', TextType::class, [
                'attr' => [
                    'pattern' => '\([0-9]{3}\) [0-9]{3}-[0-9]{4}',
                    'placeholder' => 'Phone',
                ]
            ])
            ->add('zipcode', TextType::class, [
                'attr' => [
                    'inputmode' => 'numeric',
                    'maxlength' => '5',
                    'pattern' => '[0-9]*'
                ]
            ])
            ->add('license', ChoiceType::class, [
                'choices' => [
                    'RN' => 'rn',
                    'LPN' => 'lpn',
                    'CNA' => 'cna',
                ],
                'placeholder' => 'Choose License'
            ])

            ->add('licenseState', StateType::class, [
                'choice_label' => function ($choice, $key, $value) {
                    return ucfirst($choice);
                },
                'multiple' => true,
                'attr' => ['data-placeholder' => 'States Licensed']
            ])

            ->add('specialty', EntityType::class, [
                'class' => 'App\Entity\Category',
                'placeholder' => 'Choose Specialty'
            ])
            ->add('specialtySecond', EntityType::class, [
                'class' => 'App\Entity\Category',
                'placeholder' => 'Choose Specialty Secondary'
            ])
            ->add('experienceYears', ChoiceType::class, [
                'choices' => [
                    'New grad',
                    '0-1',
                    '1-3',
                    '3-5',
                    '5-10',
                    '10-15',
                    '15+',
                ],
                'placeholder' => 'Choose Experience Years',
                'choice_label' => function ($option, $key, $value) {
                    return ucfirst($option);
                }
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Profile::class
        ]);
    }
}
