<?php

namespace App\Form;

use App\Entity\Field;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
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
        $jobFields = $fieldRepository->findNotSystemFields();

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
            ->add('categories', ChoiceType::class, [
                'choices' => $categories->findAllNames(),
                'choice_label' => function ($choice) {
                    return ucfirst($choice);
                },
                'placeholder' => 'Choose Category',
                'multiple' => true,
            ])
            ->add('description', TextareaType::class);

        /* Adding custom fields to form */
        foreach ($jobFields as $jobField) {
            $builder->add($jobField->getFieldId(), Field::FIELD_TYPE[ $jobField->getType() ], [
                'mapped' => false,
                'required' => false
            ]);
        }

        $builder
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
