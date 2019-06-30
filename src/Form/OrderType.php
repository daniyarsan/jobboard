<?php
namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('companyName', TextType::class)
            ->add('regNo', TextType::class)
            ->add('vatNo', TextType::class)
            ->add('country', CountryType::class)
            ->add('county', TextType::class)
            ->add('city', TextType::class)
            ->add('street', TextType::class)
            ->add('postalCode', TextType::class)
            ->add('save', SubmitType::class, array(
                'label' => 'Save Order & Process Payment',
                'attr' => array('class' => 'btn-primary btn-block'),
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'user' => null,
            'gateways' => null,
            'data_class' => 'App\Entity\Order',
        ]);
    }
}
