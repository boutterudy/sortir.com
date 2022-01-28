<?php

namespace App\Form;

use App\Entity\Place;
use App\Entity\Town;
use App\Repository\PlaceRepository;
use ArrayObject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Entity\Outing;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OutingType extends AbstractType
{
    private EntityManagerInterface $em; //EntityManagerInterface

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la sortie'
            ])
            ->add('startAt', DateTimeType::class, [
                'label' => 'Date et heure de la sortie',
                'html5' => true,
                'date_widget' => 'single_text',
                'time_widget' => 'single_text'
            ])
            ->add('limitSubscriptionDate', DateType::class, [
                'label' => 'Date limite d\'inscription',
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('maxUsers', IntegerType::class, [
                'label' => 'Nombre de places'
            ])
            ->add('duration', IntegerType::class, [
                'label' => 'Durée'
            ])
            ->add('about', TextareaType::class, [
                'label' => 'Description et infos'
            ])
            ->add('campus', TextType::class, [
                'label' => 'Campus',
                'disabled' => true
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
            ->add('publish', SubmitType::class, [
                'label' => 'Publier la sortie'
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
        $outingForm->add('place', EntityType::class, [
            'label' => 'Lieu',
            'class' => Place::class,
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
            'data_class' => Outing::class,
        ]);
    }
}
