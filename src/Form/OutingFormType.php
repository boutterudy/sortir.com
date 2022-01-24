<?php

namespace App\Form;

use App\Entity\Outlet;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OutingFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la sortie'
            ])
            ->add('startAt', TimeType::class, [
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('limitSubscriptionDate', DateType::class, [
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('maxUsers', IntegerType::class)
            ->add('duration', IntegerType::class)
            ->add('about', TextareaType::class)
            ->add('users', ChoiceType::class, [
                'choices' => [

                ],
                'multiple' => false
            ])
            ->add('campus', TextType::class, [
                'disabled' => true
            ])
            ->add('place', ChoiceType::class, [
                'choices' => [

                ],
                'multiple' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Outlet::class,
        ]);
    }
}
