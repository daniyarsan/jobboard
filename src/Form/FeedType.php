<?php

namespace App\Form;

use App\Entity\Feed;
use App\Form\Type\MappingType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeedType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('company', EntityType::class, [
                'placeholder' => 'Choose Company',
                'class' => 'App\Entity\Company'
            ])
            ->add('name')
            ->add('description')
            ->add('url', TextType::class)
            ->add('activate', CheckboxType::class, [
                'label' => "Activate job after import",
                'data' => true
            ]);

        $builder
            ->add('xml_text', TextareaType::class, [
                'attr' => [
                    'raw' => true
                ]
            ]);

        if ($options[ 'feedId' ]) {
            $builder
                ->add('mapper', MappingType::class, [
                    'feedId' => $options[ 'feedId' ],
                    'label' => 'Map import fields with System fields']);
        }
        
        $builder
            ->add('save', SubmitType::class)
            ->add('saveAndExit', SubmitType::class, ['label' => 'Save and Exit']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Feed::class,
            'feedId' => null,
        ]);
    }
}
