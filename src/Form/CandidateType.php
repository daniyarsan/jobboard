<?php

namespace App\Form;

use App\Entity\Education;
use App\Entity\Profile;
use App\Form\Type\StateType;
use Svg\Tag\Text;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\File;

class CandidateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setRequired(true)
            ->add('firstName', TextType::class, [
                'label' => 'First Name',
                'attr' => [
                    'placeholder' => 'First Name'
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Last Name',
                'attr' => [
                    'placeholder' => 'Last Name'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'Email',
                    'inputmode' => 'email'
                ]
            ])
            ->add('phone', TextType::class, [
                'label' => 'Phone',
                'attr' => [
                    'pattern' => '\([0-9]{3}\) [0-9]{3}-[0-9]{4}',
                    'placeholder' => 'Phone',
                    'inputmode' => 'tel'
                ]
            ])
            ->add('homeState', StateType::class, [
                'label' => 'Home State',
                'choice_label' => function ($choice, $key, $value) {
                    return ucfirst($choice);
                },
                'placeholder' => 'Choose State'
            ])
            ->add('license', ChoiceType::class, [
                'label' => 'License',
                'choices' => [
                    'RN' => 'rn',
                    'LPN' => 'lpn',
                    'CNA' => 'cna',
                ],
                'placeholder' => 'Choose License'
            ])
            ->add('licenseState', StateType::class, [
                'label' => 'License State',
                'choice_label' => function ($choice, $key, $value) {
                    return ucfirst($choice);
                },
                'multiple' => true,
                'attr' => [
                    'data-placeholder' => 'States Licensed',
                    'placeholder' => 'Choose Licensed States'
                ]
            ])
            ->add('destinationStates', StateType::class, [
                'label' => 'Destination States',
                'choice_label' => function ($choice, $key, $value) {
                    return ucfirst($choice);
                },
                'multiple' => true,
                'attr' => [
                    'data-placeholder' => 'Destination States',
                    'placeholder' => 'Choose Destination States'
                ]
            ])
            ->add('specialty', EntityType::class, [
                'label' => 'Specialty',
                'class' => 'App\Entity\Category',
                'placeholder' => 'Choose Specialty'
            ])
            ->add('specialtySecond', EntityType::class, [
                'label' => 'Secondary Specialty',
                'class' => 'App\Entity\Category',
                'placeholder' => 'Choose Specialty Secondary'
            ])
            ->add('experienceYears', ChoiceType::class, [
                'label' => 'Primary Specialty Experience',
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
            ])
            ->add('experienceYearsSecond', ChoiceType::class, [
                'label' => 'Secondary Specialty Experience',
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
            ])
            ->add('hasExperience', CheckboxType::class, [
                'attr' => [
                    'data-onlabel' => 'Experienced',
                    'data-offlabel' => 'Not Experienced'
                ]
            ])
            ->add('onAssignment', CheckboxType::class, [
                'attr' => [
                    'data-onlabel' => 'On Assignment',
                    'data-offlabel' => 'Not On Assignment'
                ]
            ])
            ->add('assignmentEndDate', DateType::class, [
                'label' => 'Assignment End Date',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => [
                    'pattern' => '[0-9]{2}/[0-9]{2}/[0-9]{4}',
                    'placeholder' => 'Assignment End Date',
                    'inputmode' => 'numeric',
                    'class' => 'mask-date'
                ]
            ])
            ->add('resume', FileType::class, [
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Image type',
                    ])
                ]
            ]);
        $formModifier = function (FormInterface $form, Sport $sport = null) {
            $positions = null === $sport ? [] : $sport->getAvailablePositions();

            $form->add('position', EntityType::class, [
                'class' => 'App\Entity\Position',
                'placeholder' => '',
                'choices' => $positions,
            ]);
        };


        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($formModifier) {
            // It's important here to fetch $event->getForm()->getData(), as
            // $event->getData() will get you the client data (that is, the ID)
            $sport = $event->getForm()->getData();

            // since we've added the listener to the child, we'll have to pass on
            // the parent to the callback functions!
            $formModifier($event->getForm()->getParent(), $sport);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Profile::class
        ]);
    }
}
