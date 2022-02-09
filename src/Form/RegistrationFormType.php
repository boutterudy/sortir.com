<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nickName', null, [
                'label'=>"Choisissez un identifiant : ",
//                'label_attr'=> array('class'=>'label'),
//                'attr'=> array('class'=>'input')

            ])
            ->add('lastName', null, [
                'label'=>'Nom : ',
//                'label_attr'=> array('class'=>'label'),
//                'attr'=> array('class'=>'input')
//

            ])
            ->add('firstName', null, [
                'label'=>'Prénom : ',
//                'label_attr'=> array('class'=>'label'),
//                'attr'=> array('class'=>'input')

            ])
            ->add('phoneNumber', null, [
                'label'=>'Numéro de téléphone : ',
//                'label_attr'=> array('class'=>'label'),
//                'attr'=> array('class'=>'input')

            ])
            ->add('email', null, [
                'label'=>'Email : ',
//                'label_attr'=> array('class'=>'label'),
//                'attr'=> array('class'=>'input')

            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions',
                    ]),
                ],
            ])
            ->add('campus', EntityType::class, [
                'label'=>'Campus',
                'class'=>Campus::class,
                'choice_label'=>'name',
                'placeholder'=>'Choisir',
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit être\'une longueur d\'au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
