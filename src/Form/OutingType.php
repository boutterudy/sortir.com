<?php

namespace App\Form;

use App\Entity\Town;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OutingType extends AbstractType
{
    private EntityManagerInterface $em; //EntityManagerInterface
    private PlaceRepository  $placeRepository;

    public function __construct(EntityManagerInterface $em, PlaceRepository $placeRepository)
    {
        $this->em = $em;
        $this->placeRepository = $placeRepository;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     * {@inheritdoc }
     */
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
        $builder->addEventListener(FormEvents::PRE_SET_DATA,  array($this, 'onPreSetData'));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
    }

    private function addElements(FormInterface $outingForm, Town $town = null) {

        $outingForm->add('town', EntityType::class, array(
            'required'=>true,
            'data'=>$town,
            'placeholder'=>'Sélectionnez une ville',
            'class'=>'App\Entity\Town'
        ));

        $places = array();

        if($town){
            $places = $this->placeRepository->createQueryBuilder('query')
                ->where('query.town = :townid')
                ->setParameter('townid', $town->getId())
                ->getQuery()
                ->getResult();
        }

        $outingForm->add('place', EntityType::class, array(
            'required'=>true,
            'placeholder'=>'Sélectionner d\'abord une ville',
            'class'=>'App\Entity\Place',
            'choices'=>$places
        ));
    }

    function onPreSubmit(FormEvent $event)
    {
        $outingForm = $event->getForm();
        $data = $event->getData();
        $town = $this->em->getRepository('App:Town')->find($data['town']);

        $this->addElements($outingForm, $town);
    }

    function onPreSetData(FormEvent $event)
    {
        $outing = $event->getData();
        $outingForm = $event->getForm();
        $town = $outing->getTown() ? $outing->getTown():null;

        $this->addElements($outingForm, $town);
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     * {@inheritdoc }
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Outing',
        ]);
    }

    /**
     * @return string
     * {@inheritdoc }
     */
    public function getBlockPrefix(): string
    {
        return 'app_outing';
    }
}
