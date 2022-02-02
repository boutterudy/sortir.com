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
    //TODO reformate Type for Place Adding purpose. Actually unused
    /*private EntityManagerInterface $em; //EntityManagerInterface

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('street', TextType::class, [
                'label' => 'Rue',
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
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
    }

    private function addElements(FormInterface $placeListForm, Town $town = null) {
        // Add the Places field
        $placeListForm->add('town', EntityType::class, [
                'label' => 'Ville',
                'class' => Town::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisissez une ville',
                'mapped' => false,
                'data' => $town
            ])
            ->add('town_postal_code', TextType::class, [
                'label' => 'Code Postal',
                'disabled' => true,
                'mapped' => false,
                'data' => $town->getPostalCode()
            ])
        ;

        $places = $town ? $town->getPlaces() : [];

        $placeListForm->add('name', EntityType::class, [
            'label' => 'Lieu',
            'class' => Place::class,
            'choice_label' => 'name',
            'placeholder' => 'Choisissez un lieu',
            'choices' => $places
        ]);
    }

    function onPreSubmit(FormEvent $event) {
        $form = $event->getForm();

        $town = $this->em->getRepository(Town::class)->find($event->getData()['town']);

        $this->addElements($form, $town);
    }

    function onPreSetData(FormEvent $event) {
        $place = $event->getData();
        $form = $event->getForm();

        $town = $place ? $place->getTown() : null;

        $this->addElements($form, $town);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Place::class
        ]);
    }*/
}
