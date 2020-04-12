<?php

namespace App\Form;

use App\Entity\Profile;
use App\Form\Type\StateType;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CandidateType extends AbstractType
{
    private $em;

    /**
     * The Type requires the EntityManager as argument in the constructor. It is autowired
     * in Symfony 3.
     *
     * @param ManagerRegistry $em
     */
    public function __construct(ManagerRegistry $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
            ->add('license', EntityType::class, [
                'class' => 'App\Entity\Discipline',
                'label' => 'License',
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
                'required' => false,
                'attr' => [
                    'data-onlabel' => 'Experienced',
                    'data-offlabel' => 'Not Experienced'
                ]
            ])
            ->add('onAssignment', CheckboxType::class, [
                'required' => false,
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
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Image type',
                    ])
                ]
            ]);

        // 3. Add 2 event listeners for the form
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
    }

    function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $categories = $this->em->getRepository('App:Category')->findBy(['discipline' => $data[ 'license' ]]);

        $this->addElements($form, $categories);
    }

    function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $this->addElements($form, []);
    }

    protected function addElements(FormInterface $form, $categories = [])
    {

        $form->add('specialty', EntityType::class, [
            'label' => 'Specialty',
            'class' => 'App\Entity\Category',
            'choices' => $categories,
            'placeholder' => 'Choose Specialty',
            'attr' => [
                'class' => 'dynamicCategory'
            ]
        ]);
        $form->add('specialtySecond', EntityType::class, [
            'label' => 'Secondary Specialty',
            'class' => 'App\Entity\Category',
            'choices' => $categories,
            'placeholder' => 'Choose Specialty',
            'attr' => [
                'class' => 'dynamicCategory'
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Profile::class
        ]);
    }
}
