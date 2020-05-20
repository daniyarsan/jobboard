<?php

namespace App\Form;

use App\Entity\Field;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminJobType extends AbstractType
{
    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $fieldRepository = $this->em->getRepository('App:Field');
        $categories = $this->em->getRepository('App:Category');

        $builder
            ->add('company', EntityType::class, [
                'placeholder' => 'Choose Company',
                'class' => 'App\Entity\Company',
            ])
            ->add('title', TextType::class)
            ->add('country', CountryType::class, [
                'placeholder' => 'Choose Country',
            ])
            ->add('state', TextType::class)
            ->add('discipline')
            ->add('categories', EntityType::class, [
                'class' => 'App\Entity\Category',
                'placeholder' => 'Choose Category',
                'multiple' => true,
            ])
            ->add('description', TextareaType::class);


        // Add listeners
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));

    }

    function onPreSubmit(FormEvent $event) {
        $form = $event->getForm();
        $job = $event->getData();

        $extraFields = $job->getExtraFields();

        $jobFields = $this->em->getRepository('App:Field')->findNotSystemFields();
        /* Adding custom fields to form */
        foreach ($jobFields as $jobField) {
            $form->add($jobField->getFieldId(), Field::FIELD_TYPE[ $jobField->getType() ], [
                'required' => false,
                'data' => $extraFields[$jobField->getFieldId()] ?? ''
            ]);
        }
        $form
            ->add('save', SubmitType::class)
            ->add('saveAndExit', SubmitType::class, ['label' => 'Save and Exit']);
    }


    function onPreSetData(FormEvent $event) {
        $job = $event->getData();
        $form = $event->getForm();

        $extraFields = $job->getExtraFields();

        $jobFields = $this->em->getRepository('App:Field')->findNotSystemFields();

        /* Adding custom fields to form */
        foreach ($jobFields as $jobField) {
            $form->add($jobField->getFieldId(), Field::FIELD_TYPE[ $jobField->getType() ], [
                'required' => false,
                'data' => $extraFields[$jobField->getFieldId()] ?? ''
            ]);
        }
        $form
            ->add('save', SubmitType::class)
            ->add('saveAndExit', SubmitType::class, ['label' => 'Save and Exit']);

    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Job'
        ]);
    }
}
