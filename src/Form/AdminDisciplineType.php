<?php

namespace App\Form;

use App\Form\Type\TagType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdminDisciplineType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setRequired(false)
            ->add('name', TextType::class, [
                'label' => 'Name',
                'constraints' => [new NotBlank(['message' => 'Page Name is a required field'])]
            ])
            ->add('synonyms', TagType::class, [
                'attr' => [
                    'placeholder' => 'Input comma separated synonyms'
                ]
            ])
            ->add('specialties', EntityType::class, [
                'class' => 'App\Entity\Category',
                'multiple' => true
            ])

            ->add('save', SubmitType::class)
            ->add('saveAndExit', SubmitType::class, ['label' => 'Save and Exit']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Discipline',
            'router' => null
        ]);
    }
}
