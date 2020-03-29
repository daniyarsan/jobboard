<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdminStaticPageType extends AbstractType
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
            ->add('url', TextType::class, [
                'label' => 'URL',
//                'constraints' => array(new UniqueUrl())
            ])
            ->add('status', ChoiceType::class, [
                'choices' => array('Active' => '1', 'Disabled' => '0'),
            ])
            ->add('content', TextareaType::class)
            ->add('metaTitle', TextType::class, [
                'label' => 'Meta Title',
            ])
            ->add('metaDescription', TextType::class, [
                'label' => 'Meta Description',
            ])
            ->add('metaKeywords', TextType::class, [
                'label' => 'Meta Keywords',
            ])
            ->add('save', SubmitType::class)
            ->add('saveAndExit', SubmitType::class, ['label' => 'Save and Exit']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\StaticPage',
            'router' => null
        ]);
    }
}
