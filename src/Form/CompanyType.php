<?php

namespace App\Form;

use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;

class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setRequired(false)
            ->add('logo', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Image type',
                    ])
                ]
            ])
            ->add('name', TextType::class)
            ->add('email', EmailType::class)
            ->add('website', TextType::class)
            ->add('phone', TextType::class)
            ->add('country', CountryType::class)
            ->add('state', TextType::class)
            ->add('address', TextType::class)
            ->add('latitude', TextType::class)
            ->add('longitude', TextType::class)
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'wysiwyg']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Company::class
        ]);
    }
}
