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
use Vich\UploaderBundle\Form\Type\VichImageType;

class AdminBlogType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setRequired(false)
            ->add('image', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'download_link' => true,
            ])
            ->add('title', TextType::class, [
                'label' => 'Title',
                'required' => true,
                'constraints' => [new NotBlank(['message' => 'Blog is a required field'])]
            ])
            ->add('active', ChoiceType::class, [
                'choices' => array('Active' => '1', 'Disabled' => '0'),
            ])
            ->add('content', TextareaType::class)
            ->add('save', SubmitType::class)
            ->add('saveAndExit', SubmitType::class, ['label' => 'Save and Exit']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Blog'
        ]);
    }
}