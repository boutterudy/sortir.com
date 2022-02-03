<?php

namespace App\Form;

use App\Entity\Place;
use App\Entity\Town;
use App\Repository\PlaceRepository;
use ArrayObject;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Entity\Outing;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Sodium\add;

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
            ->add('street', TextType::class, [
                'label' => 'Rue',
                'disabled' => true,
                'mapped' => false
            ])
            ->add('latitude', TextType::class, [
                'label' => 'Latitude',
                'disabled' => true,
                'mapped' => false
            ])
            ->add('longitude', TextType::class, [
                'label' => 'Longitude',
                'disabled' => true,
                'mapped' => false
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
            ->add('publish', SubmitType::class, [
                'label' => 'Publier la sortie'
            ])
            ->add('confirm_suppress', HiddenType::class, [
                'data' => false,
                'mapped' => false
            ])
        ;
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
    }

    private function addElements(FormInterface $form, Town $town = null) {
        // Add the Places field
        $form->add('town', EntityType::class, [
                'label' => 'Ville',
                'class' => Town::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisissez une ville',
                'mapped' => false,
                'data' => $town
            ])
            ->add('postal_code', TextType::class, [
                'label' => 'Code Postal',
                'disabled' => true,
                'mapped' => false,
                'data' => $town ? $town->getPostalCode() : null
            ])
        ;

        $places = $town ? $town->getPlaces() : [];

        $form->add('place', EntityType::class, [
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
        //dd($event->getData());
        $place = $event->getData()->getPlace();
        $form = $event->getForm();

        $town = $place ? $place->getTown() : null;

        $this->addElements($form, $town);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Outing::class
        ]);
    }
}
