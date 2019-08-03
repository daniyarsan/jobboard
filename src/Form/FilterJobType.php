<?php

namespace App\Form;

use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterJobType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setRequired(false)
            ->add('keyword', HiddenType::class, [
            'label' => false,
        ])

        ->add('categories', EntityType::class, [
            'class' => 'App\Entity\Category',
            'query_builder' => function (CategoryRepository $repository) {
                return $repository->findAllOrderedByName();
            },
            'expanded' => false
        ])
        ->add('location', CountryType::class)
        ->add('contracts', EntityType::class, [
            'label' => 'Contracts',
            'class' => 'App\Entity\Contract',
        ])
        ->add('save', SubmitType::class, [
            'label' => 'Filter Jobs',
            'attr' => ['class' => 'btn-primary btn-block'],
        ])
        ->setMethod('GET')
        ->setAction($options['router']->generate('jobs_index'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'router' => null,
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ]);
    }

    public function getBlockPrefix()
    {
        return null;
    }
}
