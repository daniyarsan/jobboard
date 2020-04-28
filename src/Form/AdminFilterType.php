<?php

namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setRequired(false)
            ->setMethod('GET')
            ->add('keyword', SearchType::class, [
                'attr' => ['placeholder' => 'Search']])
            ->add('company', EntityType::class, [
                'placeholder' => 'Choose Company',
                'class' => 'App\Entity\Company'])
            ->add('status', ChoiceType::class, [
                'choices' => ['Active' => '1', 'Disabled' => '0'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return null;
    }
}
