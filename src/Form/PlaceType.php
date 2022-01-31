<?php

namespace App\Form;

use App\Entity\Place;
use App\Entity\Town;
use App\Repository\TownRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaceType extends AbstractType
{
    private EntityManagerInterface $em; //EntityManagerInterface

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $townRepository = $this->em->getRepository(Town::class);
        $towns = $townRepository->findAll();

        $builder
            ->add('town', EntityType::class, [
                'label' => 'Ville',
                'class' => Town::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisissez une ville',
                'choices' => $towns
            ])
            ->add('street', TextType::class, [
                'label' => 'Rue',
                'disabled' => true
            ])
            ->add('town_postal_code', TextType::class, [
                'label' => 'Code Postal',
                'disabled' => true
            ])
            ->add('latitude', TextType::class, [
                'label' => 'Latitude',
                'disabled' => true
            ])
            ->add('longitude', TextType::class, [
                'label' => 'Longitude',
                'disabled' => true
            ])
        ;
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onFormEvent'));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onFormEvent'));
    }

    private function addElements(FormInterface $outingForm, Town $town = null) {

        $placeRepository = $this->em->getRepository(Place::class);

        // If there is a town selected, load the places affiliated
        $places = $placeRepository->findByTown($town);

        // Add the Places field
        $outingForm->add('name', ChoiceType::class, [
            'label' => 'Lieu',
            'choice_label' => 'name',
            'placeholder' => 'Choisissez un lieu',
            'choices' => $places
        ]);
    }

    function onFormEvent(FormEvent $event) {
        $outing = $event->getData();
        $form = $event->getForm();

        // When you create a new Outing, the Town is always empty !!!!!!!
        $town = $form->getExtraData('outing_town') ? $outing->getExtraData('outing_town') : null;

        $this->addElements($form, $town);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Place::class,
        ]);
    }
}
