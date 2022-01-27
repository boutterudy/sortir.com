<?php

namespace App\Form;

use App\Entity\Campus;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OutingsFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
                'query_builder' => function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('c')->orderBy('c.name', 'ASC');
                },
                'required' => false,
                'empty_data' => null,
                'mapped' => false
            ])
            ->add('name', TextType::class, [
                'label' => 'Search outing',
                'required' => false,
                'attr' => [
                    'placeholder' => 'search'
                ]
            ])

            ->add('startAt', DateType::class,[
            'label' => 'Entre',
            'attr' => [
                'class' => 'form-control datetimepicker-input',
                'data-toggle'=>'datetimepicker',
                'data-target'=>'#filter_start'
            ],
            'required' => false,
            'empty_data' => null,
            'mapped' => false
        ])
            ->add('limitSubscriptionDate', DateType::class,[
                'label' => 'et',
                'attr' => [
                    'class' => 'form-control datetimepicker-input',
                    'data-toggle'=>'datetimepicker',
                    'data-target'=>'#filter_close'
                ],
                'required' => false,
                'empty_data' => null,
                'mapped' => false
            ])

            ->add('organizer', CheckboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur/trice',
                'required' => false,
                'empty_data' => null,
                'mapped' => false
            ] )
            ->add('subscribed', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit/e',
                'required' => false,
                'empty_data' => null,
                'mapped' => false
            ])
            ->add('unsubscribed', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit/e',
                'required' => false,
                'empty_data' => null,
                'mapped' => false
            ])
            ->add('passed', CheckboxType::class, [
                'label' => 'Sorties passées',
                'required' => false,
                'empty_data' => null,
                'mapped' => false
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }
}
